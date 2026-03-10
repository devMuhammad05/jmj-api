<div style="display: flex; flex-direction: column; gap: 16px;">

    {{-- Security Warning Banner --}}
    <div style="background: linear-gradient(to right, #f59e0b, #f97316); border-radius: 12px; padding: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: flex-start; gap: 12px;">
            <div style="flex-shrink: 0;">
                <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h4 style="font-size: 14px; font-weight: 700; color: white; margin: 0 0 4px 0;">Security Warning</h4>
                <p style="font-size: 13px; color: rgba(255,255,255,0.9); margin: 0; line-height: 1.5;">
                    These credentials provide full access to the trading account. Handle with extreme care and only access when necessary for trade execution.
                </p>
            </div>
        </div>
    </div>

    {{-- Account Owner Card --}}
    <div style="border-radius: 12px; border: 1px solid #e5e7eb; background: white; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="flex-shrink: 0;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #4f46e5); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                    {{ strtoupper(substr($record->user->full_name, 0, 1)) }}
                </div>
            </div>
            <div>
                <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px;">Account Owner</div>
                <div style="font-size: 17px; font-weight: 700; color: #111827;">{{ $record->user->full_name }}</div>
                <div style="font-size: 13px; color: #6b7280;">{{ $record->user->email }}</div>
            </div>
        </div>
    </div>

    {{-- Trading Credentials --}}
    <div style="border-radius: 12px; border: 1px solid #e5e7eb; background: white; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
        <h4 style="font-size: 12px; font-weight: 700; color: #111827; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px; margin: 0 0 16px 0;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            Trading Credentials
        </h4>

        {{-- Account Number --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Account Number</label>
            <div style="display: flex; align-items: center; gap: 8px;">
                <code style="flex: 1; display: block; font-size: 14px; font-family: monospace; font-weight: 600; color: #111827; background: #f9fafb; padding: 12px 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    {{ $record->mt_account_number }}
                </code>
                <button onclick="copyToClipboard('{{ $record->mt_account_number }}', 'Account number')"
                    style="flex-shrink: 0; padding: 12px 16px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copy
                </button>
            </div>
        </div>

        {{-- Server --}}
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Server</label>
            <div style="display: flex; align-items: center; gap: 8px;">
                <code style="flex: 1; display: block; font-size: 14px; font-family: monospace; font-weight: 600; color: #111827; background: #f9fafb; padding: 12px 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    {{ $record->mt_server }}
                </code>
                <button onclick="copyToClipboard('{{ $record->mt_server }}', 'Server')"
                    style="flex-shrink: 0; padding: 12px 16px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copy
                </button>
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #dc2626; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                Password (Sensitive)
            </label>
            <div style="display: flex; align-items: center; gap: 8px;">
                <code style="flex: 1; display: block; font-size: 14px; font-family: monospace; font-weight: 700; color: #111827; background: #fef2f2; padding: 12px 16px; border-radius: 8px; border: 2px solid #fca5a5;">
                    {{ $record->mt_password }}
                </code>
                <button onclick="copyToClipboard('{{ $record->mt_password }}', 'Password')"
                    style="flex-shrink: 0; padding: 12px 16px; background: #dc2626; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(220,38,38,0.3);">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copy
                </button>
            </div>
        </div>
    </div>

    {{-- Account Details Grid --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">

        <div style="border-radius: 12px; border: 1px solid #e5e7eb; background: white; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Platform</div>
            <span style="display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase;
                {{ $record->platform_type->value === 'mt5' ? 'background: #dcfce7; color: #166534;' : 'background: #dbeafe; color: #1e40af;' }}">
                {{ $record->platform_type->value }}
            </span>
        </div>

        <div style="border-radius: 12px; border: 1px solid #e5e7eb; background: white; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Risk Level</div>
            @php
                $riskStyles = [
                    'conservative' => 'background: #dcfce7; color: #166534;',
                    'moderate'     => 'background: #fef9c3; color: #854d0e;',
                ];
                $style = $riskStyles[$record->risk_level->value] ?? 'background: #f3f4f6; color: #374151;';
            @endphp
            <span style="display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: capitalize; {{ $style }}">
                {{ $record->risk_level->value }}
            </span>
        </div>

        <div style="border-radius: 12px; border: 1px solid #e5e7eb; background: white; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Initial Deposit</div>
            <div style="font-size: 20px; font-weight: 700; color: #111827;">
                ${{ number_format($record->initial_deposit, 2) }}
            </div>
        </div>

    </div>

    {{-- Audit Footer --}}
    <div style="border-radius: 12px; background: #eff6ff; border: 1px solid #bfdbfe; padding: 16px;">
        <div style="display: flex; align-items: flex-start; gap: 12px;">
            <svg style="width: 20px; height: 20px; color: #2563eb; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="#2563eb" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p style="font-size: 13px; color: #1e40af; margin: 0;">
                <span style="font-weight: 700;">Audit Log:</span> This credential access has been logged for security purposes.
            </p>
        </div>
    </div>

</div>

<script>
function copyToClipboard(text, label) {
    // Log for debugging
    console.log('Attempting to copy:', label, 'Value:', text);

    // Try modern clipboard API first
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            console.log('Copy successful via Clipboard API');
            showSuccessToast(label + ' copied!');
        }).catch(err => {
            console.error('Clipboard API failed:', err);
            fallbackCopy(text, label);
        });
    } else {
        console.log('Using fallback copy method');
        fallbackCopy(text, label);
    }
}

function fallbackCopy(text, label) {
    // Create temporary textarea
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.left = '-999999px';
    textarea.style.top = '-999999px';
    textarea.setAttribute('readonly', '');
    document.body.appendChild(textarea);

    try {
        textarea.focus();
        textarea.select();
        textarea.setSelectionRange(0, 99999); // For mobile devices

        const successful = document.execCommand('copy');
        console.log('Fallback copy result:', successful);

        if (successful) {
            showSuccessToast(label + ' copied!');
        } else {
            showErrorToast('Failed to copy. Please copy manually.');
        }
    } catch (err) {
        console.error('Fallback copy failed:', err);
        showErrorToast('Copy failed. Please copy manually.');
    } finally {
        document.body.removeChild(textarea);
    }
}

function showSuccessToast(message) {
    const toast = document.createElement('div');
    toast.className = 'mt-copy-toast';
    toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#16a34a;color:white;padding:12px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);display:flex;align-items:center;gap:8px;z-index:99999;font-size:14px;font-weight:500;animation:mtFadeIn 0.3s ease-out;';
    toast.innerHTML = `
        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        ${message}
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }, 3000);
}

function showErrorToast(message) {
    const toast = document.createElement('div');
    toast.className = 'mt-copy-toast';
    toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#dc2626;color:white;padding:12px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);display:flex;align-items:center;gap:8px;z-index:99999;font-size:14px;font-weight:500;animation:mtFadeIn 0.3s ease-out;';
    toast.innerHTML = `
        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        ${message}
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }, 3000);
}

// Log clipboard availability on load
console.log('Clipboard API available:', !!navigator.clipboard);
console.log('Secure context:', window.isSecureContext);
console.log('Protocol:', window.location.protocol);
</script>

<style>
@keyframes mtFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
