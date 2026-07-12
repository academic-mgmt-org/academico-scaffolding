<?php

namespace App\Http\Controllers;

use App\Contracts\GatewayClient;
use App\Exceptions\GatewayRpcException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class NotificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, GatewayClient $gateway): View|RedirectResponse
    {
        $session = $request->session()->get('gateway_auth', []);
        $accessToken = (string) ($session['access_token'] ?? '');

        if ($accessToken === '') {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'La sesión del gateway no está disponible.']);
        }

        try {
            $unreadCount = $gateway->countUnread($accessToken);
            $limit = min($unreadCount, max(1, (int) config('gateway.max_notifications')));
            $unread = $unreadCount > 0 ? $gateway->listUnread($accessToken, $limit) : [];
            $recent = $gateway->recentNotifications($accessToken, 5);
        } catch (GatewayRpcException $exception) {
            if ($exception->isUnauthenticated()) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'La sesión expiró o fue revocada.']);
            }

            report($exception);

            return view('notifications.index', [
                'unreadCount' => null,
                'unread' => [],
                'recent' => [],
                'gatewayError' => 'No se pudieron consultar las notificaciones.',
                'gatewaySessionId' => $session['session_id'] ?? null,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return view('notifications.index', [
                'unreadCount' => null,
                'unread' => [],
                'recent' => [],
                'gatewayError' => 'Ocurrió un error al consultar el gateway.',
                'gatewaySessionId' => $session['session_id'] ?? null,
            ]);
        }

        return view('notifications.index', [
            'unreadCount' => $unreadCount,
            'unread' => $unread,
            'recent' => $recent,
            'gatewayError' => null,
            'gatewaySessionId' => $session['session_id'] ?? null,
        ]);
    }
}
