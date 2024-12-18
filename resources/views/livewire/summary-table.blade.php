<div class="p-6">
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-4">
            <input wire:model.live="search" type="search" placeholder="Search..."
                class="rounded-lg border-gray-300 shadow-sm">

            <select wire:model.live="perPage" class="rounded-lg border-gray-300 shadow-sm">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
    </div>
    <button wire:click="addNew"
            class="bg-indigo-600 hover:bg-indigo-700 mb-6 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Summary
        </button>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes
                    </th>

                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($summaries as $summary)
                    <tr class="hover:bg-gray-50 cursor-pointer transition-colors" wire:key="summary-{{ $summary->id }}"
                    x-on:click="$wire.edit({{ $summary->id }})">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $summary->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $summary->email }}</td>
                        <td class="px-6 py-4">{{ $summary->notes }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $summaries->links() }}
    </div>

    <!-- Modal -->
<!-- Modal -->
@if($isModalOpen)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div style="opacity:.75" class="fixed inset-0 transition-opacity bg-gray-500" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="relative inline-block p-6 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="w-full">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">                        {{ $isEditing ? 'Edit Summary' : 'Add New Summary' }}
                </h3>
                
                <div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Phone <span class="text-red-500">*</span></label>
                        <input type="tel" type="text" wire:model.live="phone" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-indigo-500 
                            {{ isset($validationErrors['phone']) ? 'border-red-500' : '' }}">
                        @if(isset($validationErrors['phone']))
                            <span class="text-red-500 text-xs mt-1">{{ $validationErrors['phone'] }}</span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" wire:model.live="email" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-indigo-500
                            {{ isset($validationErrors['email']) ? 'border-red-500' : '' }}">
                        @if(isset($validationErrors['email']))
                            <span class="text-red-500 text-xs mt-1">{{ $validationErrors['email'] }}</span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Notes <span class="text-red-500">*</span></label>
                        <textarea wire:model.live="notes" rows="3"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-indigo-500
                            {{ isset($validationErrors['notes']) ? 'border-red-500' : '' }}"></textarea>
                        @if(isset($validationErrors['notes']))
                            <span class="text-red-500 text-xs mt-1">{{ $validationErrors['notes'] }}</span>
                        @endif
                        
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button" 
                            wire:click="save"
                            @class([
                                'inline-flex justify-center px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2',
                                'text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' => $this->isEditButtonEnabled(),
                                'text-gray-400 bg-gray-200 cursor-not-allowed' => !$this->isEditButtonEnabled()
                            ])
                            @disabled(!$this->isEditButtonEnabled())>
                            {{ $isEditing ? 'Edit' : 'Save' }}

                        </button>
                        @if($isEditing)
                        <button type="button"
                            wire:click="delete"
                            wire:confirm="Are you sure you want to delete this record?"
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>
                        @endif

                        
                        <button type="button" 
                            wire:click="closeModal"
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
</div>