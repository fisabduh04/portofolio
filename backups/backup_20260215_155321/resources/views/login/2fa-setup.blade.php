<x-layout.auth>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-xl border">
        <h1 class="text-lg font-semibold mb-2">Aktifkan 2FA</h1>
        <p class="text-sm text-gray-500 mb-4">
            Scan QR ini dengan Google Authenticator, lalu masukkan kode 6 digit.
        </p>

        <div class="flex justify-center mb-4">
            {!! $qrImage !!}
        </div>

        @if ($errors->any())
            <div class="text-sm text-red-600 mb-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('2fa.enable') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm mb-1">Kode 6 digit</label>
                <input type="text" name="code" required class="w-full border rounded-lg px-3 py-2">
            </div>

            <button class="w-full bg-blue-600 text-white rounded-lg py-2">
                Aktifkan 2FA
            </button>
        </form>
    </div>
</x-layout.auth>
