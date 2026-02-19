<?php

namespace App\Http\Controllers\LEADER_Controller;

use App\Http\Controllers\Controller;
use App\Models\PerformanceCuts;
use App\Models\User;
use App\Notifications\CuttingSubmitted;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class CuttingController extends Controller
{
    public function index()
    {
        return view('leader_view.data_rekap.cutting.index');
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

        $cuttings = $this->filteredHistoryQuery($request)
            ->paginate($perPage)
            ->withQueryString();

        $users = $this->allowedUsersQuery()
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap']);

        return view('leader_view.data_rekap.cutting.history', [
            'cuttings' => $cuttings,
            'users' => $users,
            'perPage' => $perPage,
            'allowedPerPage' => $allowedPerPage,
        ]);
    }

    public function show($id)
    {
        $cutting = $this->baseQuery()
            ->findOrFail($id);

        return response()->json([
            'message' => 'Get cutting',
            'data' => $cutting,
            'error' => ''
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules($request));

        $cutting = PerformanceCuts::create([
            'user_id' => $validated['user_id'],
            'date_cut' => $validated['date_cutting'],
            'type_cut' => $validated['type_cutting'],
            'manual_type_cut' => $validated['type_cutting_manual'] ?? '',
            'desc' => $validated['desc'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Data cutting disimpan !',
            'data' => $cutting,
            'error' => ''
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $cutting = $this->baseQuery()->findOrFail($id);
        $validated = $request->validate($this->rules($request));

        $cutting->update([
            'user_id' => $validated['user_id'],
            'date_cut' => $validated['date_cutting'],
            'type_cut' => $validated['type_cutting'],
            'manual_type_cut' => $validated['type_cutting_manual'] ?? '',
            'desc' => $validated['desc'],
        ]);

        return response()->json([
            'message' => 'Cutting updated',
            'data' => $cutting->fresh('user'),
            'error' => ''
        ]);
    }

    public function destroy($id)
    {
        $cutting = $this->baseQuery()->findOrFail($id);
        $cutting->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Cutting deleted',
                'data' => null,
                'error' => ''
            ]);
        }

        toastr()->warning('Data cutting berhasil dihapus!', 'warning');
        return redirect()->back();
    }

    public function changeStatus($id)
    {
        $cutting = $this->baseQuery()->findOrFail($id);
        $currentStatus = $cutting->status ?? 'pending';

        if (!in_array($currentStatus, ['pending', null, ''], true)) {
            toastr()->info('Data ini tidak dapat diajukan lagi.');
            return redirect()->back();
        }

        $cutting->update(['status' => 'Di Ajukan']);
        $this->notifyApproverForSubmission($cutting->fresh('user'));
        toastr()->success('Cutting berhasil diajukan!', 'success');
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
            toastr()->info('Tidak ada data cutting yang bisa diajukan.');
            return back();
        }

        PerformanceCuts::whereIn('id', $items->pluck('id'))
            ->update(['status' => 'Di Ajukan']);

        $firstSubmitted = PerformanceCuts::with('user')->whereIn('id', $items->pluck('id'))->first();
        if ($firstSubmitted) {
            $this->notifyApproverForSubmission($firstSubmitted);
        }

        toastr()->success('Berhasil mengajukan semua data cutting sesuai filter!', 'success');
        return back();
    }

    public function fetchApi($id)
    {
        $cutting = $this->baseQuery()->findOrFail($id);

        return response()->json([
            'message' => 'Get data by id',
            'data' => $cutting,
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
                    $q->whereYear('date_cut', $date->year)
                        ->whereMonth('date_cut', $date->month);
                } catch (\Throwable $th) {
                    // Ignore invalid month and keep base query
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
        return PerformanceCuts::with('user')
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

    private function rules(Request $request): array
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
            'date_cutting' => ['required', 'date'],
            'type_cutting' => ['required', Rule::in(['Alpha', 'Kinerja', 'Lainnya'])],
            'type_cutting_manual' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn() => $request->type_cutting === 'Lainnya'),
            ],
            'desc' => ['required', 'string'],
        ];
    }

    private function notifyApproverForSubmission(PerformanceCuts $cutting): void
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
                ->where('data->type', 'cutting')
                ->where('data->kerjasama_id', $kerjasamaId)
                ->exists();
        });

        if ($filteredRecipients->isNotEmpty()) {
            Notification::send($filteredRecipients, new CuttingSubmitted($cutting));
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
