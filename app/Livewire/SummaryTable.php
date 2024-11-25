<?php

namespace App\Livewire;

use App\Models\Summary;
use Livewire\Component;
use Livewire\WithPagination;

class SummaryTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        return view('livewire.summary-table', [
            'summaries' => Summary::where('email', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")
                ->orWhere('notes', 'like', "%{$this->search}%")
                ->paginate($this->perPage)
        ]);
    }
}
