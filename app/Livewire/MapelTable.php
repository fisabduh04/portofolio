<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Mapel;


class MapelTable extends DataTableComponent
{
    protected $model = Mapel::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("No", "id")
                ->sortable()->searchable(),
            Column::make("Kode", "kode")
                ->sortable()->searchable(),
            Column::make("Jurusan", "jurusan.jurusan")
                ->sortable()->searchable(),
            Column::make("Mapel", "mapel")
                ->sortable()->searchable(),
            Column::make("Ket", "ket")
                ->sortable()->searchable(),
            // Column::make("Created at", "created_at")
            //     ->sortable(),
            // Column::make("Updated at", "updated_at")
            //     ->sortable(),
        ];
    }
    public function bulkActions(): array
    {
    return [
        'exportSelected' => 'Hapus',
        'exportSaja' => 'edit',
    ];
    }
public function exportSelected()
{
    foreach($this->getSelected() as $item)
    {
        Mapel::destroy($item);
    }
    $this->dispatch('error');
    $this->clearSelected();
}
public function exportSaja()
{
    foreach($this->getSelected() as $item)
    {
    }

    $this->dispatch('error');
    // $this->clearSelected();
}
}
