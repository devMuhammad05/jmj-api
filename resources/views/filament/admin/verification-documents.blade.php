<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Card Front</h3>
            <img src="{{ $record->id_card_front_img_url }}" alt="ID Card Front" class="w-full rounded-lg border border-gray-300 dark:border-gray-600">
        </div>
        
        @if($record->id_card_back_img_url)
        <div>
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Card Back</h3>
            <img src="{{ $record->id_card_back_img_url }}" alt="ID Card Back" class="w-full rounded-lg border border-gray-300 dark:border-gray-600">
        </div>
        @endif
    </div>
    
    <div>
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selfie with ID</h3>
        <img src="{{ $record->selfie_img_url }}" alt="Selfie" class="w-full max-w-md rounded-lg border border-gray-300 dark:border-gray-600">
    </div>
    
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
        <dl class="grid grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Type</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $record->id_type->value }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Number</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $record->id_number }}</dd>
            </div>
        </dl>
    </div>
</div>
