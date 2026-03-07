<div class="space-y-4">
    {{-- Warning --}}
    <div class="rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                <div class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">Security Warning</div>
                <div class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                    These credentials provide full access to the trading account. Handle with extreme care and only access when necessary for trade execution.
                </div>
            </div>
        </div>
    </div>

    {{-- User Info --}}
    <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-4">
        <div class="text-sm text-blue-600 dark:text-blue-400 font-medium mb-1">Account Owner</div>
        <div class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ $record->user->full_name }}</div>
        <div class="text-sm text-blue-700 dark:text-blue-300">{{ $record->user->email }}</div>
    </div>

    {{-- Credentials --}}
    <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4 space-y-4">
        {{-- Account Number --}}
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">ACCOUNT NUMBER</div>
            <div class="flex items-center gap-2">
                <code class="flex-1 text-base font-mono font-bold text-gray-900 dark:text-white bg-white dark:bg-gray-900 px-3 py-2 rounded border border-gray-300 dark:border-gray-600">
                    {{ $record->mt_account_number }}
                </code>
                <button 
                    onclick="copyText('{{ $record->mt_account_number }}', 'Account number')"
                    class="px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded"
                >
                    Copy
                </button>
            </div>
        </div>

        {{-- Server --}}
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">SERVER</div>
            <div class="flex items-center gap-2">
                <code class="flex-1 text-base font-mono font-bold text-gray-900 dark:text-white bg-white dark:bg-gray-900 px-3 py-2 rounded border border-gray-300 dark:border-gray-600">
                    {{ $record->mt_server }}
                </code>
                <button 
                    onclick="copyText('{{ $record->mt_server }}', 'Server')"
                    class="px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded"
                >
                    Copy
                </button>
            </div>
        </div>

        {{-- Password --}}
        <div>
            <div class="text-xs text-red-600 dark:text-red-400 font-bold mb-1">PASSWORD (SENSITIVE)</div>
            <div class="flex items-center gap-2">
                <code class="flex-1 text-base font-mono font-bold text-gray-900 dark:text-white bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded border-2 border-red-300 dark:border-red-700">
                    {{ $record->mt_password }}
                </code>
                <button 
                    onclick="copyText('{{ $record->mt_password }}', 'Password')"
                    class="px-3 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded font-medium"
                >
                    Copy
                </button>
            </div>
        </div>
    </div>

    {{-- Account Details --}}
    <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div>
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium mb-1">Platform</div>
                <div class="text-gray-900 dark:text-white font-bold uppercase">{{ $record->platform_type->value }}</div>
            </div>
            <div>
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium mb-1">Risk Level</div>
                <div class="text-gray-900 dark:text-white font-bold capitalize">{{ $record->risk_level->value }}</div>
            </div>
            <div>
                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium mb-1">Initial Deposit</div>
                <div class="text-gray-900 dark:text-white font-bold">${{ number_format($record->initial_deposit, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Audit Notice --}}
    <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-3">
        <div class="text-xs text-blue-700 dark:text-blue-300">
            <span class="font-semibold">Audit Log:</span> This credential access has been logged for security purposes.
        </div>
    </div>
</div>

<script>
function copyText(text, label) {
    navigator.clipboard.writeText(text).then(() => {
        alert(label + ' copied to clipboard!');
    }).catch(err => {
        console.error('Copy failed:', err);
    });
}
</script>
