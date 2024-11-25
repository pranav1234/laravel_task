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
    public $isModalOpen = false;

    public $editId;
    public $phone;
    public $email;
    public $notes;

    protected $rules = [
        'phone' => 'required|min:10',
        'email' => 'required|email',
        'notes' => 'required|min:3'
    ];

    // Add real-time validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function edit($summaryId)
    {
        $this->resetValidation();
        $summary = Summary::findOrFail($summaryId);

        $this->editId = $summary->id;
        $this->phone = $summary->phone;
        $this->email = $summary->email;
        $this->notes = $summary->notes;

        $this->isModalOpen = true;
    }

    public function update()
    {
        $this->validate();

        Summary::find($this->editId)->update([
            'phone' => $this->phone,
            'email' => $this->email,
            'notes' => $this->notes,
        ]);

        $this->closeModal();
        session()->flash('message', 'Summary updated successfully.');
    }

    public function delete()
    {
        Summary::find($this->editId)->delete();
        $this->closeModal();
        session()->flash('message', 'Summary deleted successfully.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
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