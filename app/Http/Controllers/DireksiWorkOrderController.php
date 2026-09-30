<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkOrder;
use App\Notifications\WorkOrderNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DireksiWorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with('divisi', 'jabatan')
            ->select(['id', 'nama_lengkap', 'devisi_id', 'jabatan_id'])
            ->whereNot('name', 'admin')
            ->whereNot('devisi_id', 18)
            ->where('kerjasama_id', 1)
            ->orderBy('nama_lengkap')
            ->get();
        if ($request->filled('user_id')) {
            return redirect()->route('direksi.work-order.calendar', ['user' => $request->integer('user_id')]);
        }
        $employee = null;
        $start = $request->filled('month') ? now()->createFromFormat('Y-m', $request->month)->startOfMonth() : now()->startOfMonth();
        $orders = $employee ? WorkOrder::where('user_id', $employee->id)->get()->keyBy(fn($order) => $order->tanggal->format('Y-m-d')) : collect();
        $calendar = collect();
        for ($date = $start->copy(); $date->lte($start->copy()->endOfMonth()); $date->addDay()) {
            $calendar->push($date->copy());
        }

        return view('direksi.work-order.index', compact('users'));
    }

    public function calendar(Request $request, User $user)
    {
        $start = $request->filled('month') ? now()->createFromFormat('Y-m', $request->month)->startOfMonth() : now()->startOfMonth();
        $orders = WorkOrder::where('user_id', $user->id)->get()->keyBy(fn($order) => $order->tanggal->format('Y-m-d'));
        $gridStart = $start->copy()->subDays($start->dayOfWeekIso - 1);
        $gridEnd = $start->copy()->endOfMonth()->addDays(7 - $start->copy()->endOfMonth()->dayOfWeekIso);
        $calendar = collect();
        for ($date = $gridStart->copy(); $date->lte($gridEnd); $date->addDay()) $calendar->push($date->copy());
        return view('direksi.work-order.calendar', compact('user', 'start', 'orders', 'calendar') + [
            'previousMonth' => $start->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $start->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function create(User $user, Request $request)
    {
        abort_if(!$request->filled('tanggal') || $request->date('tanggal')->isBefore(today()), 404);
        $orders = WorkOrder::where('user_id', $user->id)->where('tanggal', $request->tanggal)->get();
        return view('direksi.work-order.create', ['user' => $user, 'tanggal' => $request->tanggal, 'orders' => $orders]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'deskripsi' => ['required', 'string', 'max:5000'],
        ]);

        $workOrder = WorkOrder::create($data);
        $user = User::where('id', $data['user_id'])->first();

        $user->notify(
            new WorkOrderNotification(
                workOrderId: $workOrder->id,
                title: 'Pekerjaan Baru',
                message: 'Ada pekerjaan baru yang perlu kamu proses.'
            )
        );

        return redirect()->route('direksi.work-order.calendar', ['user' => $data['user_id']])
            ->with('success', 'Pekerjaan berhasil dikirim ke ' . $user->name . '.');
    }
}
