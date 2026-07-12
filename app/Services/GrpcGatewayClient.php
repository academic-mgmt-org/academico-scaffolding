<?php

namespace App\Services;

use App\Contracts\GatewayClient;
use App\Exceptions\GatewayRpcException;
use App\Grpc\Auth\V1\AuthServiceClient;
use App\Grpc\Auth\V1\LoginRequest;
use App\Grpc\Auth\V1\LogoutRequest;
use App\Grpc\Auth\V1\ValidateTokenRequest;
use App\Grpc\Notificaciones\V1\CountUnreadRequest;
use App\Grpc\Notificaciones\V1\ListNotificationsRequest;
use App\Grpc\Notificaciones\V1\Notification;
use App\Grpc\Notificaciones\V1\NotificationServiceClient;
use Grpc\ChannelCredentials;
use RuntimeException;

class GrpcGatewayClient implements GatewayClient
{
    private AuthServiceClient $auth;

    private NotificationServiceClient $notifications;

    /** @var array{timeout: int} */
    private array $callOptions;

    public function __construct()
    {
        if (! extension_loaded('grpc')) {
            throw new RuntimeException('La extensión grpc de PHP no está instalada. Inicia la aplicación con Laravel Sail.');
        }

        $host = (string) config('gateway.host');
        $options = ['credentials' => ChannelCredentials::createInsecure()];

        $this->auth = new AuthServiceClient($host, $options);
        $this->notifications = new NotificationServiceClient($host, $options);
        $this->callOptions = [
            'timeout' => max(1_000, (int) config('gateway.timeout_ms')) * 1_000,
        ];
    }

    public function login(string $username, string $password): array
    {
        $request = (new LoginRequest)
            ->setUsername($username)
            ->setPassword($password);

        [$response, $status] = $this->auth
            ->Login($request, [], $this->callOptions)
            ->wait();

        $this->assertOk($status, 'auth.v1.AuthService/Login');

        $result = [
            'access_token' => $response->getAccessToken(),
            'refresh_token' => $response->getRefreshToken(),
            'session_id' => $response->getSessionId(),
            'expires_in' => $response->getExpiresIn(),
        ];

        if ($result['access_token'] === '' || $result['refresh_token'] === '' || $result['session_id'] === '') {
            throw new RuntimeException('Login respondió sin los datos completos de sesión.');
        }

        return $result;
    }

    public function countUnread(string $accessToken): int
    {
        [$response, $status] = $this->notifications
            ->CountUnread(new CountUnreadRequest, $this->authorization($accessToken), $this->callOptions)
            ->wait();

        $this->assertOk($status, 'notificaciones.v1.NotificationService/CountUnread');

        return $response->getUnreadCount();
    }

    public function listUnread(string $accessToken, int $limit): array
    {
        $request = (new ListNotificationsRequest)
            ->setEstado('no_leido')
            ->setLimit(max(1, $limit));

        [$response, $status] = $this->notifications
            ->ListNotifications($request, $this->authorization($accessToken), $this->callOptions)
            ->wait();

        $this->assertOk($status, 'notificaciones.v1.NotificationService/ListNotifications');

        return $this->mapNotifications($response->getNotifications());
    }

    public function recentNotifications(?string $accessToken, int $limit): array
    {
        $request = (new ListNotificationsRequest)->setLimit(max(1, $limit));

        [$response, $status] = $this->notifications
            ->RecentNotifications($request, $this->authorization($accessToken), $this->callOptions)
            ->wait();

        $this->assertOk($status, 'notificaciones.v1.NotificationService/RecentNotifications');

        return $this->mapNotifications($response->getNotifications());
    }

    public function logout(string $accessToken, string $refreshToken): array
    {
        $request = (new LogoutRequest)
            ->setToken($accessToken)
            ->setRefreshToken($refreshToken);

        [$response, $status] = $this->auth
            ->Logout($request, [], $this->callOptions)
            ->wait();

        $this->assertOk($status, 'auth.v1.AuthService/Logout');

        return [
            'success' => $response->getSuccess(),
            'message' => $response->getMessage(),
        ];
    }

    public function validateToken(string $accessToken): bool
    {
        $request = (new ValidateTokenRequest)->setToken($accessToken);

        [$response, $status] = $this->auth
            ->ValidateToken($request, [], $this->callOptions)
            ->wait();

        $this->assertOk($status, 'auth.v1.AuthService/ValidateToken');

        return $response->getIsValid();
    }

    /** @return array<string, list<string>> */
    private function authorization(?string $accessToken): array
    {
        return $accessToken === null
            ? []
            : ['authorization' => ['Bearer '.$accessToken]];
    }

    /**
     * @param  iterable<Notification>  $notifications
     * @return list<array<string, bool|string|null>>
     */
    private function mapNotifications(iterable $notifications): array
    {
        $items = [];

        foreach ($notifications as $notification) {
            $items[] = [
                'id' => $notification->getId(),
                'titulo' => $notification->getTitulo(),
                'mensaje' => $notification->getMensaje(),
                'tipo' => $notification->getTipo(),
                'estado' => $notification->getEstado(),
                'leida' => $notification->getLeida(),
                'creado_en' => $notification->getCreadoEn(),
            ];
        }

        return $items;
    }

    private function assertOk(object $status, string $operation): void
    {
        if ($status->code === \Grpc\STATUS_OK) {
            return;
        }

        throw new GatewayRpcException(
            $operation,
            (int) $status->code,
            (string) ($status->details ?? ''),
        );
    }
}
