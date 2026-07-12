<?php

namespace App\Contracts;

interface GatewayClient
{
    /**
     * @return array{access_token: string, refresh_token: string, session_id: string, expires_in: int}
     */
    public function login(string $username, string $password): array;

    public function countUnread(string $accessToken): int;

    /** @return list<array<string, bool|string|null>> */
    public function listUnread(string $accessToken, int $limit): array;

    /** @return list<array<string, bool|string|null>> */
    public function recentNotifications(?string $accessToken, int $limit): array;

    /** @return array{success: bool, message: string} */
    public function logout(string $accessToken, string $refreshToken): array;

    public function validateToken(string $accessToken): bool;
}
