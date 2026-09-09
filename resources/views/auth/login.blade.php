@extends('layouts.store')
@section('title', 'Sign In & Member Studio — VarStore')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 bg-[#f7f5f0]">
    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl border border-[#eeebe5] overflow-hidden grid grid-cols-1 lg:grid-cols-12 transition-all">
        
        <!-- Left Editorial Showcase (5 cols) -->
        <div class="lg:col-span-5 bg-[#0d131a] text-white p-8 sm:p-10 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-[#dfbe95]/15 blur-2xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-56 h-56 rounded-full bg-[#3d83b4]/15 blur-2xl pointer-events-none"></div>

            <div class="relative z-10">
                <span class="eyebrow-luxury !text-[#dfbe95] before:!bg-[#dfbe95]">VIP Client Studio</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-3 text-white leading-tight">
                    Elevated Access for<br>
                    <span class="font-serif italic font-normal text-[#dfbe95]">Modern Living.</span>
                </h2>
                <p class="text-xs sm:text-sm text-[#94a3b8] mt-4 leading-relaxed">
                    Akses riwayat pesanan Anda, pantau pengiriman WhatsApp POS, dan dapatkan voucher promo eksklusif member.
                </p>

                <!-- Member Privileges List -->
                <div class="mt-8 space-y-3.5 pt-6 border-t border-white/10 text-xs">
                    <div class="flex items-center gap-3 text-[#e2e8f0]">
                        <span class="w-5 h-5 rounded-full bg-white/10 text-[#dfbe95] flex items-center justify-center font-bold text-[10px]">✦</span>
                        <span>Voucher Diskon Khusus Member (s/d 25%)</span>
                    </div>
                    <div class="flex items-center gap-3 text-[#e2e8f0]">
                        <span class="w-5 h-5 rounded-full bg-white/10 text-[#dfbe95] flex items-center justify-center font-bold text-[10px]">✦</span>
                        <span>Prioritas Layanan WhatsApp POS Concierge</span>
                    </div>
                    <div class="flex items-center gap-3 text-[#e2e8f0]">
                        <span class="w-5 h-5 rounded-full bg-white/10 text-[#dfbe95] flex items-center justify-center font-bold text-[10px]">✦</span>
                        <span>Pelacakan Pesanan & Garansi Tukar Ukuran</span>
                    </div>
                </div>
            </div>

            <!-- Quick Demo Account Pills (Interactive) -->
            <div class="relative z-10 mt-10 pt-6 border-t border-white/10">
                <span class="text-[10px] uppercase font-bold tracking-wider text-[#94a3b8] block mb-2.5">
                    Akun Demo Siap Pakai (1-Klik):
                </span>
                <div class="flex flex-col gap-2">
                    <button type="button" class="btn-demo-fill text-left px-3 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs transition-colors flex items-center justify-between"
                            data-demo-email="admin@varstore" data-demo-password="password">
                        <div>
                            <strong class="text-white block font-semibold text-xs">Akun Admin Studio</strong>
                            <small class="text-[#94a3b8] text-[10px]">admin@varstore · password</small>
                        </div>
                        <span class="text-[10px] text-[#dfbe95] font-bold bg-[#dfbe95]/10 px-2 py-0.5 rounded">Pasang ↗</span>
                    </button>

                    <button type="button" class="btn-demo-fill text-left px-3 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs transition-colors flex items-center justify-between"
                            data-demo-email="test@example.com" data-demo-password="password">
                        <div>
                            <strong class="text-white block font-semibold text-xs">Akun Pembeli Demo</strong>
                            <small class="text-[#94a3b8] text-[10px]">test@example.com · password</small>
                        </div>
                        <span class="text-[10px] text-[#dfbe95] font-bold bg-[#dfbe95]/10 px-2 py-0.5 rounded">Pasang ↗</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Side Interactive Form (7 cols) -->
        <div class="lg:col-span-7 p-8 sm:p-12 flex flex-col justify-center">
            
            <!-- Tab Switcher (Sign In vs Register) -->
            <div class="flex items-center gap-6 border-b border-[#eeebe5] pb-3 mb-8">
                <button type="button" id="tab-login-btn" class="auth-tab-btn active text-base font-extrabold text-[#0d131a] pb-2 relative transition-colors">
                    Masuk Akun
                    <span class="tab-indicator absolute bottom-0 left-0 w-full h-0.5 bg-[#0d131a]"></span>
                </button>
                <button type="button" id="tab-register-btn" class="auth-tab-btn text-base font-bold text-[#94a3b8] pb-2 relative transition-colors hover:text-[#0d131a]">
                    Daftar Baru
                    <span class="tab-indicator absolute bottom-0 left-0 w-full h-0.5 bg-[#0d131a] opacity-0 transition-opacity"></span>
                </button>
            </div>

            <!-- Validation Error Alert -->
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start gap-3">
                    <span class="text-rose-600 font-bold text-base leading-none">✕</span>
                    <div>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <!-- Form 1: Sign In -->
            <div id="login-form-panel">
                <div class="mb-6">
                    <h3 class="text-2xl font-extrabold text-[#0d131a] tracking-tight">Selamat Datang Kembali</h3>
                    <p class="text-xs text-[#64748b] mt-1">Masukkan kredensial akun Anda untuk melanjutkan belanja.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="login-email" class="text-xs font-bold text-[#334155] block mb-1.5">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <input type="text" id="login-email" name="email" value="{{ old('email') }}" 
                                   placeholder="nama@email.com atau admin@varstore" 
                                   class="w-full pl-10 pr-4 py-3 text-xs rounded-lg border border-[#d4cdbf] bg-[#fbfaf8] focus:bg-white focus:border-[#0d131a] outline-none transition-all" required>
                            <span class="absolute left-3.5 top-3.5 text-gray-400 text-xs">✉</span>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="login-password" class="text-xs font-bold text-[#334155]">
                                Kata Sandi
                            </label>
                            <a href="https://wa.me/6281297577567?text={{ urlencode('Halo VarStore, saya butuh bantuan reset password akun saya.') }}" target="_blank" rel="noopener noreferrer" 
                               class="text-[11px] font-bold text-[#c9a982] hover:text-[#0d131a] transition-colors">
                                Lupa sandi?
                            </a>
                        </div>
                        <div class="relative">
                            <input type="password" id="login-password" name="password" 
                                   placeholder="••••••••" 
                                   class="w-full pl-10 pr-11 py-3 text-xs rounded-lg border border-[#d4cdbf] bg-[#fbfaf8] focus:bg-white focus:border-[#0d131a] outline-none transition-all" required>
                            <span class="absolute left-3.5 top-3.5 text-gray-400 text-xs">🔒</span>
                            <button type="button" class="toggle-password-btn absolute right-3 top-3 text-gray-400 hover:text-gray-700 text-xs p-1" data-target="login-password" title="Lihat/sembunyikan sandi">
                                👁
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#0d131a] focus:ring-0">
                            <span class="text-xs text-[#64748b]">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-luxury-dark w-full justify-center text-xs py-3.5 mt-2">
                        <span>Masuk ke Akun Anda</span>
                        <span>↗</span>
                    </button>
                </form>
            </div>

            <!-- Form 2: Register (Hidden by default, switched via tab) -->
            <div id="register-form-panel" class="hidden">
                <div class="mb-6">
                    <h3 class="text-2xl font-extrabold text-[#0d131a] tracking-tight">Bergabung Sebagai Member</h3>
                    <p class="text-xs text-[#64748b] mt-1">Daftarkan akun dan nikmati diskon spesial untuk pesanan pertama.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="reg-name" class="text-xs font-bold text-[#334155] block mb-1.5">
                            Nama Lengkap
                        </label>
                        <input type="text" id="reg-name" name="name" value="{{ old('name') }}" 
                               placeholder="Nama Anda" 
                               class="w-full px-4 py-3 text-xs rounded-lg border border-[#d4cdbf] bg-[#fbfaf8] focus:bg-white focus:border-[#0d131a] outline-none transition-all" required>
                    </div>

                    <div>
                        <label for="reg-email" class="text-xs font-bold text-[#334155] block mb-1.5">
                            Alamat Email
                        </label>
                        <input type="email" id="reg-email" name="email" value="{{ old('email') }}" 
                               placeholder="nama@email.com" 
                               class="w-full px-4 py-3 text-xs rounded-lg border border-[#d4cdbf] bg-[#fbfaf8] focus:bg-white focus:border-[#0d131a] outline-none transition-all" required>
                    </div>

                    <div>
                        <label for="reg-password" class="text-xs font-bold text-[#334155] block mb-1.5">
                            Kata Sandi (Minimal 8 Karakter)
                        </label>
                        <div class="relative">
                            <input type="password" id="reg-password" name="password" 
                                   placeholder="••••••••" 
                                   class="w-full px-4 pr-11 py-3 text-xs rounded-lg border border-[#d4cdbf] bg-[#fbfaf8] focus:bg-white focus:border-[#0d131a] outline-none transition-all" required>
                            <button type="button" class="toggle-password-btn absolute right-3 top-3 text-gray-400 hover:text-gray-700 text-xs p-1" data-target="reg-password">
                                👁
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="reg-password-confirm" class="text-xs font-bold text-[#334155] block mb-1.5">
                            Konfirmasi Kata Sandi
                        </label>
                        <input type="password" id="reg-password-confirm" name="password_confirmation" 
                               placeholder="••••••••" 
                               class="w-full px-4 py-3 text-xs rounded-lg border border-[#d4cdbf] bg-[#fbfaf8] focus:bg-white focus:border-[#0d131a] outline-none transition-all" required>
                    </div>

                    <button type="submit" class="btn-luxury-gold w-full justify-center text-xs py-3.5 mt-2">
                        <span>Daftar Akun Baru</span>
                        <span>✦</span>
                    </button>
                </form>
            </div>

            <!-- Footer Trust Notes -->
            <div class="mt-8 pt-6 border-t border-[#eeebe5] flex items-center justify-between text-[11px] text-[#94a3b8]">
                <span>256-Bit SSL Secured</span>
                <span>•</span>
                <span>WhatsApp POS Sync</span>
                <span>•</span>
                <a href="{{ route('home') }}" class="hover:underline text-[#0d131a]">Kembali ke Beranda ↗</a>
            </div>

        </div>

    </div>
</div>

<!-- Interactive Client Scripts for Login & Register -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tab switching
        const tabLoginBtn = document.getElementById('tab-login-btn');
        const tabRegisterBtn = document.getElementById('tab-register-btn');
        const loginPanel = document.getElementById('login-form-panel');
        const registerPanel = document.getElementById('register-form-panel');

        function switchTab(isLogin) {
            if (isLogin) {
                tabLoginBtn.classList.add('text-[#0d131a]', 'font-extrabold');
                tabLoginBtn.classList.remove('text-[#94a3b8]', 'font-bold');
                tabLoginBtn.querySelector('.tab-indicator').classList.remove('opacity-0');

                tabRegisterBtn.classList.remove('text-[#0d131a]', 'font-extrabold');
                tabRegisterBtn.classList.add('text-[#94a3b8]', 'font-bold');
                tabRegisterBtn.querySelector('.tab-indicator').classList.add('opacity-0');

                loginPanel.classList.remove('hidden');
                registerPanel.classList.add('hidden');
            } else {
                tabRegisterBtn.classList.add('text-[#0d131a]', 'font-extrabold');
                tabRegisterBtn.classList.remove('text-[#94a3b8]', 'font-bold');
                tabRegisterBtn.querySelector('.tab-indicator').classList.remove('opacity-0');

                tabLoginBtn.classList.remove('text-[#0d131a]', 'font-extrabold');
                tabLoginBtn.classList.add('text-[#94a3b8]', 'font-bold');
                tabLoginBtn.querySelector('.tab-indicator').classList.add('opacity-0');

                registerPanel.classList.remove('hidden');
                loginPanel.classList.add('hidden');
            }
        }

        tabLoginBtn?.addEventListener('click', () => switchTab(true));
        tabRegisterBtn?.addEventListener('click', () => switchTab(false));

        // 1-Click Demo account fill
        document.querySelectorAll('.btn-demo-fill').forEach(btn => {
            btn.addEventListener('click', () => {
                switchTab(true);
                const emailInput = document.getElementById('login-email');
                const passwordInput = document.getElementById('login-password');
                const email = btn.getAttribute('data-demo-email');
                const password = btn.getAttribute('data-demo-password');

                if (emailInput && passwordInput) {
                    emailInput.value = email;
                    passwordInput.value = password;
                    
                    // Subtle highlight animation
                    emailInput.classList.add('ring-2', 'ring-[#dfbe95]');
                    passwordInput.classList.add('ring-2', 'ring-[#dfbe95]');
                    setTimeout(() => {
                        emailInput.classList.remove('ring-2', 'ring-[#dfbe95]');
                        passwordInput.classList.remove('ring-2', 'ring-[#dfbe95]');
                    }, 800);

                    if (window.showToast) {
                        window.showToast(`Akun demo ${email} berhasil diisi! Silakan klik Masuk.`, 'info');
                    }
                }
            });
        });

        // Toggle password visibility
        document.querySelectorAll('.toggle-password-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) return;

                if (input.type === 'password') {
                    input.type = 'text';
                    btn.textContent = '🙈';
                } else {
                    input.type = 'password';
                    btn.textContent = '👁';
                }
            });
        });
    });
</script>
@endsection
