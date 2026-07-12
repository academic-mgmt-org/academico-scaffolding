<x-layouts::app title="Notificaciones">
    <div class="flex w-full flex-col gap-6">
        <div>
            <flux:heading size="xl">Flujo de autenticación y notificaciones</flux:heading>
            <flux:text class="mt-2">
                Usuario: {{ auth()->user()->email }} · Sesión gateway: {{ $gatewaySessionId ?: 'no disponible' }}
            </flux:text>
        </div>

        @if ($gatewayError)
            <flux:callout variant="danger" icon="exclamation-triangle" heading="Error del gateway">
                {{ $gatewayError }}
            </flux:callout>
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <flux:text>Notificaciones no leídas</flux:text>
                <flux:heading size="xl" class="mt-2">{{ $unreadCount ?? '—' }}</flux:heading>
            </div>
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <flux:text>Gateway gRPC</flux:text>
                <flux:heading size="lg" class="mt-2">Conectado mediante sesión Laravel</flux:heading>
            </div>
        </div>

        <section class="space-y-3">
            <flux:heading size="lg">No leídas</flux:heading>
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                @forelse ($unread as $notification)
                    <article class="border-b border-zinc-200 p-4 last:border-b-0 dark:border-zinc-700">
                        <flux:heading>{{ $notification['titulo'] ?: 'Sin título' }}</flux:heading>
                        <flux:text>{{ $notification['mensaje'] }}</flux:text>
                        <flux:text size="sm" class="mt-1">{{ $notification['creado_en'] }}</flux:text>
                    </article>
                @empty
                    <flux:text class="p-4">No hay notificaciones no leídas.</flux:text>
                @endforelse
            </div>
        </section>

        <section class="space-y-3">
            <flux:heading size="lg">Recientes</flux:heading>
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                @forelse ($recent as $notification)
                    <article class="border-b border-zinc-200 p-4 last:border-b-0 dark:border-zinc-700">
                        <flux:heading>{{ $notification['titulo'] ?: 'Sin título' }}</flux:heading>
                        <flux:text>{{ $notification['mensaje'] }}</flux:text>
                    </article>
                @empty
                    <flux:text class="p-4">No hay notificaciones recientes.</flux:text>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts::app>
