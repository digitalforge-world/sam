<x-guest-layout>
    @section('title', 'Vérification email')

    <div class="auth-card-header">
        <div class="greeting">Presque terminé</div>
        <h1>Vérifiez votre email</h1>
        <p>Merci de votre inscription ! Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-flash auth-flash-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Un nouveau lien de vérification a été envoyé à votre adresse email.
        </div>
    @endif

    <div class="auth-form-card">
        {{-- Illustration --}}
        <div style="text-align:center;margin-bottom:24px;">
            <div style="width:80px;height:80px;margin:0 auto;background:linear-gradient(135deg,#E8F5EE,#DCFCE7);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#1B6B4A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            </div>
        </div>

        <p style="text-align:center;font-size:14px;color:#6B7280;margin-bottom:24px;line-height:1.6;">
            Si vous n'avez pas reçu l'email, vérifiez votre dossier spam ou cliquez ci-dessous pour renvoyer le lien.
        </p>

        <div style="display:flex;flex-direction:column;gap:12px;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="auth-submit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Renvoyer l'email de vérification
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width:100%;padding:12px 24px;font-size:14px;font-weight:600;font-family:var(--font);color:#6B7280;background:#F3F4F6;border:1px solid #E5E7EB;border-radius:10px;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:8px" onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
