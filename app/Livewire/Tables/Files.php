<?php

namespace App\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\File;

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
        ];
    }

    public function builder(): Builder
    {
        return File::forUser($this->user);

    }
//
//    public function filters(): array
//    {
//        return [
//            DateFilter::make('Created From', 'created_from'),
//            DateFilter::make('Created To', 'created_to'),
//        ];
//    }
//
//    public function deleteFile(){
//        if($this->selectedRowsQuery()->count() > 0){
//            $this->selectedRowsQuery()->delete();
//            session()->flash('message-success', 'The selected files have been deleted');
//        }else{
//            session()->flash('message-error', 'No files selected!');
//        }
//        $this->redirect(route('file.index'));
//    }
//
//    public function bulkActions(): array
//    {
//        return [
//            'deleteFile' => __('Delete'),
//        ];
//    }
}
