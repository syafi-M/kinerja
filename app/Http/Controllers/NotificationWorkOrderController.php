<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationWorkOrderController extends Controller
{
    public function open(
        Request $request,
        DatabaseNotification $notification
    ): RedirectResponse {
        // Pastikan notification memang milik user yang sedang login
        abort_unless(
            $notification->notifiable_id === $request->user()->id,
            403
        );

        $workOrderId = $notification->data['work_order_id'];

        $workOrder = WorkOrder::findOrFail($workOrderId);

        // Tandai sudah dibaca
        $notification->markAsRead();
        $workOrder->update(["has_read" => 1]);

        // $data = $notification->data;

        return redirect()->route('checkpoint-user.create', [
            'work_order' => $workOrder->id,
        ]);
    }
}
