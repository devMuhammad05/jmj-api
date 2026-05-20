@php
    $initials = collect(explode(' ', $record->full_name))
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->join('');
@endphp

<div style="display: flex; flex-direction: column; gap: 12px;">

    {{-- Header --}}
    <div style="display: flex; align-items: center; gap: 16px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px;">
        <div style="flex-shrink: 0; width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #4f46e5); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; font-weight: 700; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
            {{ $initials }}
        </div>
        <div>
            <div style="font-size: 17px; font-weight: 700; color: #111827;">{{ $record->full_name }}</div>
            <div style="font-size: 13px; color: #6b7280; margin-top: 2px;">{{ $record->pool?->name ?? 'Pool Investor' }}</div>
        </div>
    </div>

    {{-- Bank Name --}}
    <div style="display: flex; align-items: center; gap: 14px; background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 10px; background: #dbeafe; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 20px; height: 20px; color: #2563eb;" fill="none" stroke="#2563eb" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 10c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
            </svg>
        </div>
        <div>
            <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 3px;">Bank Name</div>
            <div style="font-size: 15px; font-weight: 600; color: #111827;">{{ $record->bank_name ?: '—' }}</div>
        </div>
    </div>

    {{-- Account Name --}}
    <div style="display: flex; align-items: center; gap: 14px; background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 10px; background: #ede9fe; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="#7c3aed" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
        </div>
        <div>
            <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 3px;">Account Name</div>
            <div style="font-size: 15px; font-weight: 600; color: #111827;">{{ $record->account_name ?: '—' }}</div>
        </div>
    </div>

    {{-- Account Number --}}
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px;">Account Number</div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <code style="flex: 1; font-size: 18px; font-family: monospace; font-weight: 700; color: #111827; background: #f9fafb; padding: 10px 14px; border-radius: 8px; border: 1px solid #e5e7eb; letter-spacing: 0.08em;">
                {{ $record->account_number ?: '—' }}
            </code>
            @if ($record->account_number)
                <button
                    id="copy-acct-btn"
                    data-copy="{{ $record->account_number }}"
                    onclick="bankDetailsCopy(this)"
                    style="flex-shrink: 0; display: flex; align-items: center; gap: 6px; padding: 10px 16px; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600;"
                >
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copy
                </button>
            @endif
        </div>
    </div>

    {{-- Phone --}}
    <div style="display: flex; align-items: center; gap: 14px; background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 10px; background: #ffedd5; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="#ea580c" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
            </svg>
        </div>
        <div>
            <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 3px;">Phone</div>
            <div style="font-size: 15px; font-weight: 600; color: #111827;">{{ $record->phone_number ?: '—' }}</div>
        </div>
    </div>

</div>

<script>
function bankDetailsCopy(btn) {
    const text = btn.dataset.copy;
    navigator.clipboard.writeText(text).then(() => {
        btn.innerHTML = '<svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Copied!';
        btn.style.background = '#dcfce7';
        btn.style.color = '#16a34a';
        btn.style.borderColor = '#bbf7d0';
        setTimeout(() => {
            btn.innerHTML = '<svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Copy';
            btn.style.background = '#f3f4f6';
            btn.style.color = '#374151';
            btn.style.borderColor = '#e5e7eb';
        }, 2000);
    });
}
</script>
