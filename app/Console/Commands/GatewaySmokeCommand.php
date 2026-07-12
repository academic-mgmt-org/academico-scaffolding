<?php

namespace App\Console\Commands;

use App\Contracts\GatewayClient;
use App\Exceptions\GatewayRpcException;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use RuntimeException;
use Throwable;

#[Signature('gateway:smoke {--username= : Usuario del gateway} {--no-prompt : No solicitar datos faltantes}')]
#[Description('Ejecuta login, consultas, logout y pruebas negativas contra el gateway gRPC')]
class GatewaySmokeCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(GatewayClient $gateway): int
    {
        $username = trim((string) ($this->option('username') ?: config('gateway.smoke.username')));
        $password = (string) config('gateway.smoke.password');

        if ($username === '' && ! $this->option('no-prompt')) {
            $username = trim((string) $this->ask('Usuario'));
        }

        if ($password === '' && ! $this->option('no-prompt')) {
            $password = (string) $this->secret('Contraseña');
        }

        if ($username === '' || $password === '') {
            $this->error('Define las credenciales por entorno o entrada interactiva.');

            return self::FAILURE;
        }

        $accessToken = null;
        $refreshToken = null;
        $loggedOut = false;

        try {
            $negativeOk = $this->components->task('Rechazo sin Authorization', function () use ($gateway): bool {
                try {
                    $gateway->recentNotifications(null, 1);
                } catch (GatewayRpcException $exception) {
                    return $exception->isUnauthenticated();
                }

                return false;
            });

            if (! $negativeOk) {
                throw new RuntimeException('El gateway aceptó notificaciones sin Authorization.');
            }

            $login = null;
            $loginOk = $this->components->task('Login por auth.v1.AuthService/Login', function () use (
                $gateway,
                $username,
                $password,
                &$login,
            ): bool {
                $login = $gateway->login($username, $password);

                return $login['access_token'] !== '' && $login['refresh_token'] !== '';
            });

            if (! $loginOk || $login === null) {
                throw new RuntimeException('Login no devolvió tokens.');
            }

            $accessToken = $login['access_token'];
            $refreshToken = $login['refresh_token'];

            $unreadCount = 0;
            $countOk = $this->components->task('Contador de no leídas', function () use (
                $gateway,
                $accessToken,
                &$unreadCount,
            ): bool {
                $unreadCount = $gateway->countUnread($accessToken);

                return $unreadCount >= 0;
            });

            if (! $countOk) {
                throw new RuntimeException('CountUnread devolvió un valor inválido.');
            }

            if ($unreadCount > 0) {
                $listOk = $this->components->task(
                    'Listado de no leídas',
                    fn (): bool => is_array($gateway->listUnread($accessToken, $unreadCount)),
                );

                if (! $listOk) {
                    throw new RuntimeException('ListNotifications no devolvió una lista.');
                }
            } else {
                $this->line('  INFO  No hay notificaciones no leídas; se omite el listado.');
            }

            $recent = [];
            $recentOk = $this->components->task('Notificaciones recientes', function () use (
                $gateway,
                $accessToken,
                &$recent,
            ): bool {
                $recent = $gateway->recentNotifications($accessToken, 5);

                return is_array($recent);
            });

            if (! $recentOk) {
                throw new RuntimeException('RecentNotifications no devolvió una lista.');
            }

            $logoutOk = $this->components->task(
                'Logout y revocación remota',
                fn (): bool => $gateway->logout($accessToken, $refreshToken)['success'],
            );

            if (! $logoutOk) {
                throw new RuntimeException('Logout no confirmó el cierre.');
            }
            $loggedOut = true;

            if (! $this->components->task('ValidateToken devuelve false', fn (): bool => ! $gateway->validateToken($accessToken))) {
                throw new RuntimeException('El token continuó válido después de logout.');
            }

            $revokedOk = $this->components->task('Rechazo del token revocado', function () use (
                $gateway,
                $accessToken,
            ): bool {
                try {
                    $gateway->recentNotifications($accessToken, 1);
                } catch (GatewayRpcException $exception) {
                    return $exception->isUnauthenticated();
                }

                return false;
            });

            if (! $revokedOk) {
                throw new RuntimeException('El token revocado todavía accedió a notificaciones.');
            }

            $this->newLine();
            $this->info("Flujo completo OK. No leídas: {$unreadCount}; recientes: ".count($recent).'.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->newLine();
            $this->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            if ($accessToken !== null && $refreshToken !== null && ! $loggedOut) {
                try {
                    $gateway->logout($accessToken, $refreshToken);
                    $this->warn('La sesión de prueba se cerró durante la limpieza.');
                } catch (Throwable) {
                    $this->warn('No se pudo cerrar la sesión de prueba; revise el gateway.');
                }
            }
        }
    }
}
