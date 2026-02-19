<?php

namespace App\Http\Controllers\LEADER_Controller;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\PersonIn;
use App\Models\User;
use App\Notifications\PersonInSubmitted;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class PersonInController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::where('type_jabatan', auth()->user()->jabatan->type_jabatan)->get();

        return view('leader_view.data_rekap.person_in.index', [
            'jabatans' => $jabatans,
        ]);
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

        $users = User::where('kerjasama_id', auth()->user()->kerjasama_id)
            ->whereHas('Jabatan', function ($q) {
                $q->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
            })
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

        $personIn = $this->filteredHistoryQuery($request)
            ->paginate($perPage)
            ->withQueryString();
        $jabatans = Jabatan::where('type_jabatan', auth()->user()->jabatan->type_jabatan)->get();

        return view('leader_view.data_rekap.person_in.history', [
            'personIn' => $personIn,
            'jabatans' => $jabatans,
            'perPage' => $perPage,
            'allowedPerPage' => $allowedPerPage,
        ]);
    }

    public function show($id)
    {
        $personIn = $this->baseQuery()
            ->with('jabatan')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Get person in',
            'data' => $personIn,
            'error' => ''
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules($request));
        unset($validated['has_account']);

        $validated['client_id'] = auth()->user()->kerjasama->client_id;
        $validated['status'] = 'pending';

        $personIn = PersonIn::create($validated);
        return response()->json([
            'message' => 'Data personil masuk disimpan !',
            'data' => $personIn,
            'error' => ''
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $personIn = $this->baseQuery()->findOrFail($id);

        $validated = $request->validate($this->rules($request));
        unset($validated['has_account']);

        $validated['client_id'] = auth()->user()->kerjasama->client_id;

        $personIn->update($validated);

        return response()->json([
            'message' => 'Person in updated',
            'data' => $personIn->fresh(),
            'error' => ''
        ]);
    }

    public function destroy($id)
    {
        $personIn = $this->baseQuery()->findOrFail($id);
        $personIn->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Person in deleted',
                'data' => null,
                'error' => ''
            ]);
        }

        toastr()->warning('Personil masuk berhasil dihapus!', 'warning');
        return redirect()->back();
    }

    public function changeStatus($id)
    {
        $personIn = $this->baseQuery()->findOrFail($id);
        $currentStatus = $personIn->status ?? 'pending';

        if (!in_array($currentStatus, ['pending', null, ''], true)) {
            toastr()->info('Data ini tidak dapat diajukan lagi.');
            return redirect()->back();
        }

        $personIn->update(['status' => 'Di Ajukan']);

        $this->notifyApproverForSubmission($personIn);

        toastr()->success('Personil masuk berhasil diajukan!', 'success');
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
            toastr()->info('Tidak ada data personil masuk yang bisa diajukan.');
            return back();
        }

        PersonIn::whereIn('id', $items->pluck('id'))
            ->update(['status' => 'Di Ajukan']);

        $firstSubmitted = PersonIn::whereIn('id', $items->pluck('id'))->first();
        if ($firstSubmitted) {
            $this->notifyApproverForSubmission($firstSubmitted);
        }

        toastr()->success('Berhasil mengajukan semua personil masuk sesuai filter!', 'success');
        return back();
    }

    public function fetchApi($id)
    {
        $personIn = $this->baseQuery()
            ->with('jabatan')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Get data by id',
            'data' => $personIn,
            'error' => ''
        ]);
    }

    private function filteredHistoryQuery(Request $request)
    {
        return $this->baseQuery()
            ->with('jabatan')
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('month'), function ($q) use ($request) {
                try {
                    $date = Carbon::createFromFormat('Y-m', $request->month);
                    $q->whereYear('date_in', $date->year)
                        ->whereMonth('date_in', $date->month);
                } catch (\Throwable $th) {
                    // Ignore invalid month and keep base query
                }
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('fullname', 'like', '%' . trim($request->search) . '%');
            })
            ->latest();
    }

    private function baseQuery()
    {
        return PersonIn::where('client_id', auth()->user()->kerjasama->client_id)
            ->whereHas('jabatan', function ($q) {
                $q->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
            });
    }

    private function rules(Request $request): array
    {
        $allowedFullnames = User::where('kerjasama_id', auth()->user()->kerjasama_id)
            ->whereHas('Jabatan', function ($q) {
                $q->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
            })
            ->pluck('nama_lengkap')
            ->all();

        return [
            'has_account' => ['nullable', Rule::in(['yes', 'no'])],
            'jabatan_id' => [
                'required',
                Rule::exists('jabatans', 'id')->where(function ($q) {
                    $q->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
                })
            ],
            'date_in' => ['required', 'date'],
            'method_salary' => ['required', Rule::in(['transfer', 'cash', 'manual'])],
            'method_salary_manual' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn() => $request->method_salary === 'transfer'),
            ],
            'fullname' => [
                'required',
                'string',
                'max:255',
                Rule::requiredIf(fn() => $request->has_account === 'yes'),
                function ($attribute, $value, $fail) use ($request, $allowedFullnames) {
                    if ($request->has_account === 'yes' && !in_array($value, $allowedFullnames, true)) {
                        $fail('Nama lengkap tidak valid untuk akun yang dipilih.');
                    }
                },
            ],
        ];
    }

    private function notifyApproverForSubmission(PersonIn $personIn): void
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
                ->where('data->type', 'person_in')
                ->where('data->kerjasama_id', $kerjasamaId)
                ->exists();
        });

        if ($filteredRecipients->isNotEmpty()) {
            Notification::send($filteredRecipients, new PersonInSubmitted($personIn, $kerjasamaId));
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
