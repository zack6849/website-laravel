<?php

declare(strict_types=1);

namespace App\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\File;
use Rappasoft\LaravelLivewireTables\Views\Columns\DateColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;

class Files extends DataTableComponent
{

    public $user;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            LinkColumn::make("Name", "original_filename")
                ->sortable()
                ->searchable()
                ->title(fn(File $row) => $row->original_filename)
                ->location(fn(File $row) => route('file.show', $row->filename))
                ->attributes(fn($row) => [
                    'target' => '_blank'
                ]),
            DateColumn::make("Uploaded At", "created_at")
            ->sortable(),
            Column::make("Type", "mime"),
            LinkColumn::make('Actions')
                ->title(fn(File $row) => 'Delete')
                ->location(fn(File $row) => $row->deleteURL)
                ->attributes(fn($row) => [
                    'target' => '_blank'
                ])
        ];
    }

    public function builder(): Builder
    {
        return File::forUser($this->user)
            ->latest()
            ->select('*');
    }

    public function filters(): array
    {
        $min = File::min('created_at') ?? now();
        $max = File::max('created_at') ?? now();
        return [
            DateFilter::make('Created From')->config([$min, $max]),
            DateFilter::make('Created To')->config([$min, $max])
        ];
    }
}
