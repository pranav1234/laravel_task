<?php

namespace App\Livewire;

use App\Models\Summary;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;

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

    // Store original values
    public $originalPhone;
    public $originalEmail;
    public $originalNotes;

    // Add property for validation errors
    public $validationErrors = [];

    protected $rules = [
        'phone' => 'required|min:10',
        'email' => 'required|email',
        'notes' => 'required|min:3'
    ];

    public function updated($propertyName)
    {
        // Validate single field
        $validator = Validator::make(
            [$propertyName => $this->$propertyName],
            [$propertyName => $this->rules[$propertyName]]
        );

        if ($validator->fails()) {
            $this->validationErrors[$propertyName] = $validator->errors()->first($propertyName);
        } else {
            unset($this->validationErrors[$propertyName]);
        }
    }

    public function edit($summaryId)
    {
        $this->validationErrors = [];
        $summary = Summary::findOrFail($summaryId);

        $this->editId = $summary->id;

        // Set current and original values
        $this->phone = $this->originalPhone = $summary->phone;
        $this->email = $this->originalEmail = $summary->email;
        $this->notes = $this->originalNotes = $summary->notes;

        $this->isModalOpen = true;
    }

    public function hasChanges()
    {
        return $this->phone !== $this->originalPhone ||
            $this->email !== $this->originalEmail ||
            $this->notes !== $this->originalNotes;
    }

    public function isEditButtonEnabled()
    {
        return empty($this->validationErrors) && $this->hasChanges();
    }

    public function update()
    {
        $validator = Validator::make(
            [
                'phone' => $this->phone,
                'email' => $this->email,
                'notes' => $this->notes,
            ],
            $this->rules
        );

        if ($validator->fails()) {
            $this->validationErrors = $validator->errors()->messages();
            return;
        }

        if (!$this->hasChanges()) {
            return;
        }

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
        $this->validationErrors = [];
        $this->reset(['editId', 'phone', 'email', 'notes', 'originalPhone', 'originalEmail', 'originalNotes']);
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