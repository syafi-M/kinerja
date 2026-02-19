<?php

namespace App\Http\Controllers\LEADER_Controller;

use App\Http\Controllers\Controller;
use App\Models\FinishedTraining;
use App\Models\User;
use App\Notifications\FinishedTrainingSubmitted;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class FinishedTrainingController extends Controller
{
    public function index()
    {
        return view('leader_view.data_rekap.finished_training.index');
    }

    public function searchUsers(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'message' => 'Query too short',
                'data' => [],
                'error' => ''
            ]);
        }

        $users = $this->allowedUsersQuery()
            ->where('nama_lengkap', 'like', '%' . $query . '%')
            ->orderBy('nama_lengkap')
            ->limit(15)
            ->get(['id', 'nama_lengkap']);

        return response()->json([
            'message' => 'User list',
            'data' => $users,
            'error' => ''
        ]);
    }

    public function history(Request $request)
    {
        $allowedPerPage = [10, 15, 25, 50];
        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 15;
        }

        $finishedTrainings = $this->filteredHistoryQuery($request)
            ->paginate($perPage)
            ->through(function ($item) {
                $item->masa_training_hari = Carbon::parse($item->date_finish_train)->day;
                return $item;
            })
            ->withQueryString();

        $users = $this->allowedUsersQuery()
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap']);

        return view('leader_view.data_rekap.finished_training.history', [
            'finishedTrainings' => $finishedTrainings,
            'users' => $users,
            'perPage' => $perPage,
            'allowedPerPage' => $allowedPerPage,
        ]);
    }

    public function show($id)
    {
        $finishedTraining = $this->baseQuery()->findOrFail($id);
        $finishedTraining->masa_training_hari = Carbon::parse($finishedTraining->date_finish_train)->day;

        return response()->json([
            'message' => 'Get finished training',
            'data' => $finishedTraining,
            'error' => ''
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $finishedTraining = FinishedTraining::create([
            'user_id' => $validated['user_id'],
            'date_in' => $validated['date_in'],
            'date_finish_train' => $validated['date_finish_train'],
            'desc' => $validated['desc'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Data lepas training disimpan !',
            'data' => $finishedTraining,
            'error' => ''
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $finishedTraining = $this->baseQuery()->findOrFail($id);
        $validated = $request->validate($this->rules());

        $finishedTraining->update([
            'user_id' => $validated['user_id'],
            'date_in' => $validated['date_in'],
            'date_finish_train' => $validated['date_finish_train'],
            'desc' => $validated['desc'],
        ]);

        return response()->json([
            'message' => 'Finished training updated',
            'data' => $finishedTraining->fresh('user'),
            'error' => ''
        ]);
    }

    public function destroy($id)
    {
        $finishedTraining = $this->baseQuery()->findOrFail($id);
        $finishedTraining->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Finished training deleted',
                'data' => null,
                'error' => ''
            ]);
        }

        toastr()->warning('Data lepas training berhasil dihapus!', 'warning');
        return redirect()->back();
    }

    public function changeStatus($id)
    {
        $finishedTraining = $this->baseQuery()->findOrFail($id);
        $currentStatus = $finishedTraining->status ?? 'pending';

        if (!in_array($currentStatus, ['pending', null, ''], true)) {
            toastr()->info('Data ini tidak dapat diajukan lagi.');
            return redirect()->back();
        }

        $finishedTraining->update(['status' => 'Di Ajukan']);
        $this->notifyApproverForSubmission($finishedTraining->fresh('user'));
        toastr()->success('Lepas training berhasil diajukan!', 'success');
        return redirect()->back();
    }

    public function bulkStatus(Request $request)
    {
        $query = $this->filteredHistoryQuery($request)
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', 'pending');
            });

        $items = $query->get(['id']);

        if ($items->isEmpty()) {
            toastr()->info('Tidak ada data lepas training yang bisa diajukan.');
            return back();
        }

        FinishedTraining::whereIn('id', $items->pluck('id'))
            ->update(['status' => 'Di Ajukan']);

        $firstSubmitted = FinishedTraining::with('user')->whereIn('id', $items->pluck('id'))->first();
        if ($firstSubmitted) {
            $this->notifyApproverForSubmission($firstSubmitted);
        }

        toastr()->success('Berhasil mengajukan semua data lepas training sesuai filter!', 'success');
        return back();
    }

    public function fetchApi($id)
    {
        $finishedTraining = $this->baseQuery()->findOrFail($id);
        $finishedTraining->masa_training_hari = Carbon::parse($finishedTraining->date_finish_train)->day;

        return response()->json([
            'message' => 'Get data by id',
            'data' => $finishedTraining,
            'error' => ''
        ]);
    }

    private function filteredHistoryQuery(Request $request)
    {
        return $this->baseQuery()
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('month'), function ($q) use ($request) {
                try {
                    $date = Carbon::createFromFormat('Y-m', $request->month);
                    $q->whereYear('date_finish_train', $date->year)
                        ->whereMonth('date_finish_train', $date->month);
                } catch (\Throwable $th) {
                    // ignore invalid month
                }
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $keyword = trim($request->search);
                $q->whereHas('user', function ($userQuery) use ($keyword) {
                    $userQuery->where('nama_lengkap', 'like', '%' . $keyword . '%');
                });
            })
            ->latest();
    }

    private function baseQuery()
    {
        return FinishedTraining::with('user')
            ->whereHas('user', function ($q) {
                $q->where('kerjasama_id', auth()->user()->kerjasama_id)
                    ->whereHas('jabatan', function ($jabatanQuery) {
                        $jabatanQuery->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
                    });
            });
    }

    private function allowedUsersQuery()
    {
        return User::where('kerjasama_id', auth()->user()->kerjasama_id)
            ->whereHas('jabatan', function ($q) {
                $q->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
            });
    }

    private function rules(): array
    {
        return [
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($q) {
                    $q->where('kerjasama_id', auth()->user()->kerjasama_id);
                }),
                function ($attribute, $value, $fail) {
                    $exists = $this->allowedUsersQuery()->where('id', $value)->exists();
                    if (!$exists) {
                        $fail('Nama lengkap tidak valid untuk area kerja Anda.');
                    }
                },
            ],
            'date_in' => ['required', 'date'],
            'date_finish_train' => ['required', 'date', 'after_or_equal:date_in'],
            'desc' => ['required', 'string'],
        ];
    }

    private function notifyApproverForSubmission(FinishedTraining $finishedTraining): void
    {
        $targetCode = $this->resolveTargetCode();
        if (!$targetCode) {
            return;
        }

        $kerjasamaId = (int) auth()->user()->kerjasama_id;
        $approvers = User::whereHas('jabatan', function ($q) use ($targetCode) {
            $q->where('code_jabatan', $targetCode);
        })->get();

        if ($approvers->isEmpty()) {
            return;
        }

        $filteredRecipients = $approvers->filter(function ($recipient) use ($kerjasamaId) {
            return !$recipient->unreadNotifications()
                ->where('data->type', 'finished_training')
                ->where('data->kerjasama_id', $kerjasamaId)
                ->exists();
        });

        if ($filteredRecipients->isNotEmpty()) {
            Notification::send($filteredRecipients, new FinishedTrainingSubmitted($finishedTraining));
        }
    }

    private function resolveTargetCode(): ?string
    {
        $code = strtoupper((string) auth()->user()->jabatan->code_jabatan);
        if ($code === 'CO-CS') {
            return 'SPV';
        }

        if ($code === 'CO-SCR') {
            return 'MARKETING';
        }

        return null;
    }
}
