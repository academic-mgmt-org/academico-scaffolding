<x-layouts::auth title="Iniciar sesión">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Iniciar sesión" description="Las credenciales se validan en el gateway gRPC." />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Usuario del gateway -->
            <flux:input
                name="email"
                label="Correo institucional"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="usuario@institucion.edu.ec"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    label="Contraseña"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Contraseña"
                    viewable
                />
            </div>

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    Iniciar sesión
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::auth>
