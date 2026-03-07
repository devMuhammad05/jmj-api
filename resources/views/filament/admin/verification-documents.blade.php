@php
    use Filament\Support\Facades\FilamentAsset;
@endphp

<div class="space-y-4">
    {{-- User Info --}}
    <div class="rounded-lg bg-gray-100 dark:bg-gray-800 p-4">
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div>
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium mb-1">Full Name</div>
                <div class="text-gray-900 dark:text-white font-semibold">{{ $record->user->full_name }}</div>
            </div>
            <div>
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium mb-1">ID Type</div>
                <div class="text-gray-900 dark:text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $record->id_type->value)) }}</div>
            </div>
            <div>
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium mb-1">ID Number</div>
                <div class="text-gray-900 dark:text-white font-mono font-semibold">{{ $record->id_number }}</div>
            </div>
        </div>
    </div>

    {{-- ID Card Front --}}
    <div>
        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">ID Card - Front</div>
        <div class="rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600">
            <img 
                src="{{ $record->id_card_front_img_url }}" 
                alt="ID Card Front"
                class="w-full h-auto"
                style="max-height: 400px; object-fit: contain; background: #f3f4f6;"
            />
        </div>
        <a 
            href="{{ $record->id_card_front_img_url }}" 
            target="_blank"
            class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
        >
            Open in new tab →
        </a>
    </div>

    {{-- ID Card Back --}}
    @if($record->id_card_back_img_url)
    <div>
        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">ID Card - Back</div>
        <div class="rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600">
            <img 
                src="{{ $record->id_card_back_img_url }}" 
                alt="ID Card Back"
                class="w-full h-auto"
                style="max-height: 400px; object-fit: contain; background: #f3f4f6;"
            />
        </div>
        <a 
            href="{{ $record->id_card_back_img_url }}" 
            target="_blank"
            class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
        >
            Open in new tab →
        </a>
    </div>
    @endif

    {{-- Selfie --}}
    <div>
        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Selfie with ID</div>
        <div class="rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600 max-w-md mx-auto">
            <img 
                src="{{ $record->selfie_img_url }}" 
                alt="Selfie"
                class="w-full h-auto"
                style="max-height: 500px; object-fit: contain; background: #f3f4f6;"
            />
        </div>
        <div class="text-center">
            <a 
                href="{{ $record->selfie_img_url }}" 
                target="_blank"
                class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
            >
                Open in new tab →
            </a>
        </div>
    </div>

    {{-- Submission Date --}}
    <div class="rounded-lg bg-gray-100 dark:bg-gray-800 p-3 text-sm">
        <span class="text-gray-500 dark:text-gray-400">Submitted:</span>
        <span class="text-gray-900 dark:text-white font-medium ml-2">{{ $record->created_at->format('M j, Y g:i A') }}</span>
    </div>
</div>
