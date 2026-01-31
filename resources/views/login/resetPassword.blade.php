<x-layout.auth>
    <section class="bg-neutral-primary-soft dark:bg-neutral-secondary-medium">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">

            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-heading dark:text-white">
                <img class="w-12 h-12 me-2" src="/img/logo.png" alt="logo">
                {{ $nama_sekolah ?? 'SMK AL-MIFTAH PAMEKASAN' }}
            </a>

            <div
                class="w-full bg-neutral-primary-soft rounded-base shadow-xs border border-default sm:max-w-md xl:p-0 dark:bg-neutral-secondary-medium dark:border-default-medium">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">

                    <h1 class="text-xl font-bold leading-tight tracking-tight text-heading md:text-2xl dark:text-white">
                        RESET PASSWORD
                    </h1>

                    @if ($errors->any())
                        <div class="p-3 rounded bg-red-50 text-red-700 text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form class="space-y-4 md:space-y-6" method="POST" action="{{ route('password.update') }}">
                        @csrf

                        {{-- token wajib --}}
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        {{-- email (wajib, Fortify pakai ini) --}}
                        <x-form.input name="email" id="email" type="email" label="Email Anda" :value="old('email', $request->email)"
                            required autocomplete="email" />

                        {{-- password baru --}}
                        <x-form.input name="password" id="password" type="password" label="Password Baru" required
                            autocomplete="new-password" />

                        {{-- konfirmasi --}}
                        <x-form.input name="password_confirmation" id="password_confirmation" type="password"
                            label="Konfirmasi Password" required autocomplete="new-password" />

                        <div class="pt-1">
                            <x-btn type="submit" text="Simpan Password" color="primary" class="w-full" />
                        </div>
                    </form>

                    <div class="text-sm text-center">
                        <a href="{{ route('login') }}" class="text-brand hover:underline">
                            Kembali ke Login
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
</x-layout.auth>
