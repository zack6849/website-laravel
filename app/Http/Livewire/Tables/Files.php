<?php

namespace App\Http\Livewire\Tables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\File;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class Files extends DataTableComponent
{

    public $user;

    public $columnSearch = [
        'name' => null,
        'email' => null,
    ];

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "original_filename")
                ->sortable()
                ->searchable()
                ->linkTo(function($value, $column, $row){
                    return route('file.show', $row->original_filename);
                }),
            Column::make("File Type", "mime")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
        ];
    }

    public function query(): Builder
    {
        return File::forUser($this->user)
            ->when($this->getFilter('created_from'), function($query, $timestamp){
                return $query->where('created_at', '>=',$timestamp);
            })
            ->when($this->getFilter('created_to'), function($query, $timestamp){
                return $query->where('created_at', '<=', $timestamp);
            })
            ->when($this->getFilter("file_type"), function ($query, $type){
                return $query->where('mime', 'like',"%$type%");
            });

    }

    public function filters(): array
    {
        return [
            'created_from' => Filter::make('Created From')->date([]),
            'created_to' => Filter::make('Created To')->date([]),
        ];
    }

    public function deleteFile(){
        if($this->selectedRowsQuery()->count() > 0){
            $this->selectedRowsQuery()->delete();
            session()->flash('message-success', 'The selected files have been deleted');
        }else{
            session()->flash('message-error', 'No files selected!');
        }
        $this->redirect(route('file.index'));
    }

    public function bulkActions(): array
    {
        return [
            'deleteFile' => __('Delete'),
        ];
    }
}
