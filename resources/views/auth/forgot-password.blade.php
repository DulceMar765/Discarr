<x-guest-layout>
    <h2 class="text-center mb-4">¿Olvidaste tu contraseña?</h2>
    <p class="text-center mb-4 text-muted">
        No hay problema. Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
    </p>

    @if (session('status'))
        <div class="alert alert-success text-center">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="d-flex justify-content-end">
            <x-primary-button class="btn btn-primary">
                {{ __('Enviar enlace de restablecimiento') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
