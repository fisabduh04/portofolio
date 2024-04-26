<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="mb-3">
        {{-- <input type="text" wire:model.live="nama" class="form-control"> --}}
        {{-- <input type="radio" wire:model.live="nama" value="laki">Laki-laki
        <input type="radio" wire:model.live="nama" value="perempuan">Perempuan --}}
        {{-- <select class="form-select" wire:model.live='nama'>
            <option hidden>Pilih Jenis Kelamin</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>
        <br>
        Halo nama saya adalah {{ $nama }} --}}

        <input type="number" wire:model="angka" wire:model="angka">
        @if ($angka>=0)
        <button class="btn btn-danger" wire:click="minus">- Minus</button>
        @endif
        <button class="btn btn-primary" wire:click="plus">+ plus</button>

    </div>
</div>