<x-layout.auth :logo="$sekolah?->logo_url ?? asset('img/logo.png')">
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <img class="w-8 h-8 mr-2" src="{{ $sekolah?->logo_url ?? asset('img/logo.png') }}" alt="logo">
                {{ $sekolah?->nama_sekolah ?? 'SISTEM INFORMASI SEKOLAH' }}
            </a>
            <div
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Masuk ke akun Anda
                    </h1>

                    {{-- Session Status Alert --}}
                    @if (session('status'))
                        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                            role="alert">
                            <span class="font-medium">Berhasil!</span> {{ session('status') }}
                        </div>
                    @endif

                    <form class="space-y-4 md:space-y-6" action="/login" method="POST">
                        @csrf

                        <x-form.input name="email" label="Email Anda" type="email" placeholder="nama@email.com"
                            required />

                        <x-form.input name="password" label="Kata Sandi" type="password" placeholder="••••••••"
                            required />

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex items-center h-5">
                                    <input id="remember" name="remember" type="checkbox"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 transition duration-150 ease-in-out cursor-pointer">
                                </div>
                                <div class="ml-4 text-sm">
                                    <label for="remember"
                                        class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                                        Ingat Saya
                                    </label>
                                </div>
                            </div>
                            <a href="{{ route('password.request') }}"
                                class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-500 hover:underline">
                                Lupa kata sandi?
                            </a>
                        </div>
                        <button type="submit"
                            class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex justify-center items-center">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                </path>
                            </svg>
                            Masuk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layout.auth>
