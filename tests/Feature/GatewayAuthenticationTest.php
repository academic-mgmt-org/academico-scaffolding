<?php

namespace Tests\Feature;

use App\Contracts\GatewayClient;
use App\Exceptions\GatewayRpcException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class GatewayAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_comes_from_the_starter_kit(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Iniciar sesión');
    }

    public function test_gateway_login_creates_the_laravel_session(): void
    {
        $gateway = $this->gatewayMock();
        $gateway->shouldReceive('login')
            ->once()
            ->with('student@example.edu', 'correct-password')
            ->andReturn($this->loginPayload());

        $response = $this->post(route('login.store'), [
            'email' => 'student@example.edu',
            'password' => 'correct-password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('gateway_auth.session_id', 'session-123')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'student@example.edu']);
    }

    public function test_invalid_gateway_credentials_are_rejected(): void
    {
        $gateway = $this->gatewayMock();
        $gateway->shouldReceive('login')
            ->once()
            ->andThrow(new GatewayRpcException('Login', \Grpc\STATUS_UNAUTHENTICATED, 'invalid credentials'));

        $this->post(route('login.store'), [
            'email' => 'student@example.edu',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dashboard_requires_a_local_and_gateway_session(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login', absolute: false));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login', absolute: false));

        $this->assertGuest();
    }

    public function test_dashboard_uses_the_bearer_token_for_notifications(): void
    {
        $gateway = $this->gatewayMock();
        $gateway->shouldReceive('countUnread')->once()->with('access-secret')->andReturn(1);
        $gateway->shouldReceive('listUnread')->once()->with('access-secret', 1)->andReturn([
            $this->notification('Pendiente'),
        ]);
        $gateway->shouldReceive('recentNotifications')->once()->with('access-secret', 5)->andReturn([
            $this->notification('Reciente'),
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->withSession(['gateway_auth' => $this->gatewaySession()])
            ->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('Pendiente')
            ->assertSee('Reciente')
            ->assertSee('session-123')
            ->assertDontSee('access-secret')
            ->assertDontSee('refresh-secret');
    }

    public function test_logout_revokes_the_remote_session_before_local_logout(): void
    {
        $gateway = $this->gatewayMock();
        $gateway->shouldReceive('logout')
            ->once()
            ->with('access-secret', 'refresh-secret')
            ->andReturn(['success' => true, 'message' => 'ok']);

        $response = $this->actingAs(User::factory()->create())
            ->withSession(['gateway_auth' => $this->gatewaySession()])
            ->post(route('logout'));

        $response->assertRedirect(route('home', absolute: false));
        $this->assertGuest();
        $response->assertSessionMissing('gateway_auth');
    }

    private function gatewayMock(): GatewayClient&MockInterface
    {
        $mock = \Mockery::mock(GatewayClient::class);
        $this->app->instance(GatewayClient::class, $mock);

        return $mock;
    }

    /** @return array{access_token: string, refresh_token: string, session_id: string, expires_in: int} */
    private function loginPayload(): array
    {
        return [
            'access_token' => 'access-secret',
            'refresh_token' => 'refresh-secret',
            'session_id' => 'session-123',
            'expires_in' => 900,
        ];
    }

    /** @return array{access_token: string, refresh_token: string, session_id: string, expires_at: string} */
    private function gatewaySession(): array
    {
        return [
            'access_token' => 'access-secret',
            'refresh_token' => 'refresh-secret',
            'session_id' => 'session-123',
            'expires_at' => now()->addMinutes(15)->toIso8601String(),
        ];
    }

    /** @return array<string, bool|string|null> */
    private function notification(string $title): array
    {
        return [
            'id' => 'notification-1',
            'titulo' => $title,
            'mensaje' => 'Contenido',
            'tipo' => 'info',
            'estado' => 'no_leido',
            'leida' => false,
            'creado_en' => '2026-07-12T00:00:00Z',
        ];
    }
}
