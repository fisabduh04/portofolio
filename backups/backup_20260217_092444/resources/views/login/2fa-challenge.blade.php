<x-layout.auth>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-xl border">
        <h1 class="text-lg font-semibold mb-2">Verifikasi 2FA</h1>
        <p class="text-sm text-gray-500 mb-4">
            Masukkan kode 6 digit dari Authenticator atau recovery code.
        </p>

        @if ($errors->any())
            <div class="text-sm text-red-600 mb-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-4">
            @csrf
            <input name="code" class="w-full border rounded-lg px-3 py-2" placeholder="123456 atau recovery-code"
                required>

            <button class="w-full bg-blue-600 text-white rounded-lg py-2">
                Verifikasi
            </button>
        </form>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="block text-center text-sm text-gray-600 mt-4 hover:underline">
            Keluar
        </a>

    </div>
</x-layout.auth>
