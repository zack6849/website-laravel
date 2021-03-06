<?php

namespace App\Http\Livewire\Files;

use App\File;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{ 
    
    use WithPagination;

    public $perPage = 50;
    public $sortField = 'created_at';
    public $sortAsc = false;
    public $search = '';
    public $includeDeleted = false;

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }
        $this->sortField = $field;
    }

    public function clear()
    {
        $this->search = '';
    }

    public function render()
    {
        $user = auth()->user();
        $files = $user->files()->search($this->search)->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')->paginate($this->perPage);
        return view('livewire.files.table', compact('files'));
    }
}
