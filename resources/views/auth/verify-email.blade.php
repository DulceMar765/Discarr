<x-guest-layout>
    <h2 class="text-center mb-4">Verifica tu correo electrónico</h2>
    <p class="text-center mb-4 text-muted">
        Gracias por registrarte. Antes de comenzar, verifica tu correo electrónico haciendo clic en el enlace que te enviamos. Si no recibiste el correo, con gusto te enviaremos otro.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success text-center">
            {{ __('Se ha enviado un nuevo enlace de verificación a tu correo electrónico.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <div class="d-flex justify-content-center">
            <x-primary-button class="btn btn-primary">
                {{ __('Reenviar enlace de verificación') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
