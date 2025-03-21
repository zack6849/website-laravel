<?php
declare(strict_types=1);

namespace App\Http\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait SendsStatusResponses
{
    public function sendResponse(
        Request $request,
        string $route,
        string $friendlyMessage,
        string $apiMessage = null,
    ): array|RedirectResponse
    {
        if ($request->expectsJson()) {
            $message = $apiMessage ?? $friendlyMessage;
            return ['status' => $message];
        }
        return \Redirect::to($route)->with('status', $friendlyMessage);
    }
}
