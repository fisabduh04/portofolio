<div>
    @dump($firstId)
    @dump($selectAll)
    @dump($mySelected)
    <table class="table">
        <thead>
            <th>No</th>
            <th><input type="checkbox" wire:model.live="selectAll"></th>
            <input type="hidden" wire:model="firstId" value="{{ $users[0]->id }}">
            <th>Nama</th>
        </thead>
        @foreach ($users as $index=>$item)
        <tr>
            <td>{{ $users->firstItem()+$index }}</td>
            <td><input type="checkbox" wire:model.live="mySelected" value="{{ $item->id }}">
            </td>
            <td>{{ $item->name }}</td>
        </tr>
        @endforeach
    </table>
    {{ $users->links() }}

    @push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"
        integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>

    <script>
        $(".page-item").on('click',function(event){
            Livewire.dispatch('resetmySelected');
        })
    </script>

    @endpush
</div>