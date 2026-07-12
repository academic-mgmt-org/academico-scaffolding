<?php

namespace App\Http\Middleware;

use App\Contracts\GatewayClient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RevokeGatewaySessionOnLogout
{
    public function __construct(private readonly GatewayClient $gateway) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && $request->routeIs('logout')) {
            $session = $request->session()->get('gateway_auth', []);

            if (! empty($session['access_token']) && ! empty($session['refresh_token'])) {
                try {
                    $this->gateway->logout(
                        $session['access_token'],
                        $session['refresh_token'],
                    );
                } catch (\Throwable $exception) {
                    Log::warning('No se pudo confirmar el logout remoto del gateway.', [
                        'exception' => $exception::class,
                    ]);
                }
            }
        }

        return $next($request);
    }
}
