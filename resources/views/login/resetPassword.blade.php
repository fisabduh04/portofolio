<x-layout.auth>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">

            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <img class="w-8 h-8 mr-2" src="{{ $sekolah?->logo_url ?? asset('img/logo.png') }}" alt="logo">
                {{ $nama_sekolah ?? 'SISTEM INFORMASI SEKOLAH' }}
            </a>

            <div
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">

                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Atur Ulang Kata Sandi
                    </h1>

                    @if ($errors->any())
                        <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                            role="alert">
                            <span class="font-medium">Error!</span> {{ $errors->first() }}
                        </div>
                    @endif

                    <form class="space-y-4 md:space-y-6" method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <x-form.input name="email" label="Email Anda" type="email" :value="old('email', $request->email)"
                            placeholder="nama@email.com" required />

                        <x-form.input name="password" label="Kata Sandi Baru" type="password" placeholder="••••••••"
                            required />

                        <x-form.input name="password_confirmation" label="Konfirmasi Kata Sandi" type="password"
                            placeholder="••••••••" required />

                        <button type="submit"
                            class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition duration-200">
                            Simpan Kata Sandi
                        </button>
                    </form>

                    <p class="text-sm font-light text-gray-500 dark:text-gray-400 text-center">
                        Sudah ingat? <a href="{{ route('login') }}"
                            class="font-medium text-blue-600 hover:underline dark:text-blue-500">Masuk sekarang</a>
                    </p>

                </div>
            </div>
        </div>
    </section>
</x-layout.auth>
