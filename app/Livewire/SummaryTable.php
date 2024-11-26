<?php

namespace App\Livewire;

use App\Models\Summary;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SummaryTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $isModalOpen = false;
    public $isEditing = false;

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
        'phone' => ['required', 'regex:/^07\d{9}$/', 'size:11'],
        'email' => 'required|email',
        'notes' => 'required|min:3'
    ];
    public function getValidationMessages()
    {
        return [
            'phone.required' => 'The phone number is required.',
            'phone.regex' => 'The phone number must start with 07 and contain 11 digits.',
            'phone.size' => 'The phone number must be exactly 11 digits.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'notes.required' => 'Notes are required.',
            'notes.min' => 'Notes must be at least 3 characters.'
        ];
    }

    public function addNew()
    {
        $this->resetFields();
        $this->isEditing = false;
        $this->isModalOpen = true;
    }

    public function edit($summaryId)
    {
        Log::info('Edit method called', [
            'summaryId' => $summaryId,
            'isEditing' => $this->isEditing,
            'current_time' => now()
        ]);

        try {
            $this->resetValidation();
            $summary = Summary::findOrFail($summaryId);

            $this->editId = $summary->id;
            $this->phone = $this->originalPhone = $summary->phone;
            $this->email = $this->originalEmail = $summary->email;
            $this->notes = $this->originalNotes = $summary->notes;

            $this->isEditing = true;
            $this->isModalOpen = true;

            Log::info('Modal should be open', [
                'isModalOpen' => $this->isModalOpen,
                'isEditing' => $this->isEditing,
                'summary' => $summary->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in edit method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function save()
    {
        $validator = Validator::make(
            [
                'phone' => $this->phone,
                'email' => $this->email,
                'notes' => $this->notes,
            ],
            $this->rules,
            $this->getValidationMessages()

        );

        if ($validator->fails()) {
            $this->validationErrors = collect($validator->errors()->messages())
                ->map(function ($messages) {
                    return $messages[0]; // Get first message from each field
                })
                ->toArray();
            return;
        }

        if ($this->isEditing) {
            if (!$this->hasChanges()) {
                return;
            }
            Summary::find($this->editId)->update([
                'phone' => $this->phone,
                'email' => $this->email,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Summary updated successfully.');
        } else {
            Summary::create([
                'phone' => $this->phone,
                'email' => $this->email,
                'notes' => $this->notes,
            ]);
            $this->resetPage();

            session()->flash('message', 'Summary added successfully.');
        }

        $this->closeModal();
        $this->reset(['phone', 'email', 'notes', 'editId', 'isEditing']);
        $this->isModalOpen = false;
        $this->validationErrors = [];
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'phone') {
            $this->phone = preg_replace('/[^0-9]/', '', $this->phone);
        }
        // Validate single field
        $validator = Validator::make(
            [$propertyName => $this->$propertyName],
            [$propertyName => $this->rules[$propertyName]],
            $this->getValidationMessages()

        );

        if ($validator->fails()) {
            $this->validationErrors[$propertyName] = $validator->errors()->first($propertyName);
        } else {
            unset($this->validationErrors[$propertyName]);
        }
    }

    public function hasChanges()
    {
        return $this->phone !== $this->originalPhone ||
            $this->email !== $this->originalEmail ||
            $this->notes !== $this->originalNotes;
    }

    public function isEditButtonEnabled()
    {
        if ($this->isEditing) {
            return empty($this->validationErrors) && $this->hasChanges();
        }

        return !empty($this->phone) &&
            !empty($this->email) &&
            !empty($this->notes) &&
            empty($this->validationErrors);
    }

    public function delete()
    {
        Summary::find($this->editId)->delete();
        $this->closeModal();
        session()->flash('message', 'Summary deleted successfully.');
    }

    private function resetFields()
    {
        $this->editId = null;
        $this->phone = '';
        $this->email = '';
        $this->notes = '';
        $this->originalPhone = '';
        $this->originalEmail = '';
        $this->originalNotes = '';
        $this->validationErrors = [];
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->isEditing = false;
        $this->validationErrors = [];
        $this->reset(['phone', 'email', 'notes', 'editId', 'originalPhone', 'originalEmail', 'originalNotes']);
    }

    public function render()
    {
        return view('livewire.summary-table', [
            'summaries' => Summary::where('email', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")
                ->orWhere('notes', 'like', "%{$this->search}%")
                ->latest()
                ->paginate($this->perPage)
        ]);
    }
}