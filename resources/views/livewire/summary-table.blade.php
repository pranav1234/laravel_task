<div class="p-6">
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

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
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
                    <tr>
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
</div>