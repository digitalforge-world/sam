<x-guest-layout>
    @section('title', 'Mot de passe oublié')

    <div class="auth-card-header">
        <div class="greeting">Récupération</div>
        <h1>Mot de passe oublié ?</h1>
        <p>Pas de souci ! Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="auth-flash auth-flash-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="auth-form-card">
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            {{-- Email --}}
            <div class="auth-form-group">
                <label class="auth-label" for="email">Adresse email</label>
                <div class="auth-input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <input id="email" type="email" name="email" class="auth-input {{ $errors->has('email') ? 'input-error' : '' }}" value="{{ old('email') }}" placeholder="votre@email.com" required autofocus>
                </div>
                @error('email')
                    <div class="auth-error">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit" class="auth-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Envoyer le lien de réinitialisation
            </button>
        </form>
    </div>

    <div class="auth-footer">
        <a href="{{ route('login') }}">← Retour à la connexion</a>
    </div>
</x-guest-layout>
