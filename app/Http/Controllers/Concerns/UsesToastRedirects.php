<?php

namespace App\Http\Controllers\Concerns;

trait UsesToastRedirects
{
    protected function backWithToast(string $type, string $message)
    {
        return back()->with('toast', $this->toastPayload($type, $message));
    }

    protected function backWithInputToast(string $type, string $message)
    {
        return back()->withInput()->with('toast', $this->toastPayload($type, $message));
    }

    protected function redirectBackWithToast(string $type, string $message)
    {
        return redirect()->back()->with('toast', $this->toastPayload($type, $message));
    }

    protected function redirectBackWithInputToast(string $type, string $message)
    {
        return redirect()->back()->withInput()->with('toast', $this->toastPayload($type, $message));
    }

    protected function toastPayload(string $type, string $message): array
    {
        return [
            'type' => $type,
            'message' => $message,
        ];
    }
}
