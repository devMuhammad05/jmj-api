<style>
    /* ── KYC Viewer Variables ── */
    .kyc-viewer {
        --kyc-accent: #6366f1;
        --kyc-accent-light: #818cf8;
        --kyc-success: #22c55e;
        --kyc-warning: #f59e0b;
        --kyc-danger: #ef4444;
        --kyc-radius: 14px;
        --kyc-radius-sm: 8px;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* ── Info Bar ── */
    .kyc-info-bar {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        padding: 18px 20px;
        border-radius: var(--kyc-radius);
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1e1b4b 100%);
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .kyc-info-bar::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(99, 102, 241, .35) 0%, transparent 60%);
        pointer-events: none;
    }

    .kyc-info-item .label {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgba(199, 210, 254, .7);
        margin-bottom: 4px;
    }

    .kyc-info-item .value {
        font-size: 14px;
        font-weight: 700;
        color: #e0e7ff;
    }

    .kyc-info-item .value.mono {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        letter-spacing: .04em;
        color: #a5b4fc;
    }

    /* ── Status Badge in info bar ── */
    .kyc-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
    }

    .kyc-status-badge.pending {
        background: rgba(245, 158, 11, .15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, .3);
    }

    .kyc-status-badge.approved {
        background: rgba(34, 197, 94, .15);
        color: #4ade80;
        border: 1px solid rgba(34, 197, 94, .3);
    }

    .kyc-status-badge.rejected {
        background: rgba(239, 68, 68, .15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, .3);
    }

    .kyc-status-badge .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    /* ── Tab Strip ── */
    .kyc-tabs {
        display: flex;
        gap: 4px;
        background: rgba(99, 102, 241, .06);
        padding: 5px;
        border-radius: var(--kyc-radius);
        margin-bottom: 20px;
        border: 1px solid rgba(99, 102, 241, .12);
    }

    .kyc-tab-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 9px 14px;
        border-radius: var(--kyc-radius-sm);
        font-size: 13px;
        font-weight: 600;
        color: #9ca3af;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all .22s ease;
        white-space: nowrap;
    }

    .kyc-tab-btn svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    .kyc-tab-btn:hover {
        color: #c7d2fe;
        background: rgba(99, 102, 241, .1);
    }

    .kyc-tab-btn.active {
        color: #fff;
        background: linear-gradient(135deg, var(--kyc-accent), #4f46e5);
        box-shadow: 0 4px 14px rgba(99, 102, 241, .45);
    }

    /* ── Panel ── */
    .kyc-panel {
        display: none;
    }

    .kyc-panel.active {
        display: block;
    }

    /* ── Document Card ── */
    .kyc-doc-card {
        border-radius: var(--kyc-radius);
        overflow: hidden;
        background: rgba(99, 102, 241, .04);
        border: 1px solid rgba(99, 102, 241, .13);
        transition: border-color .2s;
    }

    .kyc-doc-card:hover {
        border-color: rgba(99, 102, 241, .3);
    }

    .kyc-doc-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 18px;
        border-bottom: 1px solid rgba(99, 102, 241, .1);
    }

    .kyc-doc-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .kyc-doc-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: linear-gradient(135deg, rgba(99, 102, 241, .2), rgba(79, 70, 229, .3));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #a5b4fc;
    }

    .kyc-doc-icon svg {
        width: 17px;
        height: 17px;
    }

    .kyc-doc-title {
        font-size: 13px;
        font-weight: 700;
        color: #c7d2fe;
        letter-spacing: .01em;
    }

    .kyc-doc-sub {
        font-size: 11px;
        color: #6b7280;
        margin-top: 1px;
    }

    /* Open link button */
    .kyc-open-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 13px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #a5b4fc;
        background: rgba(99, 102, 241, .12);
        border: 1px solid rgba(99, 102, 241, .2);
        text-decoration: none;
        transition: all .18s ease;
        cursor: pointer;
    }

    .kyc-open-btn:hover {
        background: rgba(99, 102, 241, .25);
        color: #e0e7ff;
        border-color: rgba(99, 102, 241, .4);
        text-decoration: none;
    }

    .kyc-open-btn svg {
        width: 12px;
        height: 12px;
    }

    /* Image wrapper */
    .kyc-img-wrapper {
        position: relative;
        overflow: hidden;
        cursor: zoom-in;
        background: #0f0f1a;
    }

    .kyc-img-wrapper img {
        width: 100%;
        height: auto;
        max-height: 420px;
        object-fit: contain;
        display: block;
        transition: transform .32s ease;
    }

    .kyc-img-wrapper:hover img {
        transform: scale(1.02);
    }

    .kyc-img-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, .5) 0%, transparent 40%);
        opacity: 0;
        transition: opacity .22s;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 16px;
    }

    .kyc-img-wrapper:hover .kyc-img-overlay {
        opacity: 1;
    }

    .kyc-img-zoom-hint {
        font-size: 12px;
        font-weight: 600;
        color: white;
        background: rgba(255, 255, 255, .15);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255, 255, 255, .2);
        padding: 5px 14px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .kyc-img-zoom-hint svg {
        width: 13px;
        height: 13px;
    }

    /* ── Selfie card specific ── */
    .kyc-panel[data-tab="selfie"] .kyc-doc-card {
        max-width: 480px;
        margin: 0 auto;
    }

    .kyc-panel[data-tab="selfie"] .kyc-img-wrapper img {
        max-height: 520px;
    }

    /* ── Footer bar ── */
    .kyc-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        border-radius: var(--kyc-radius-sm);
        background: rgba(99, 102, 241, .05);
        border: 1px solid rgba(99, 102, 241, .1);
        margin-top: 16px;
        font-size: 12px;
    }

    .kyc-footer-item {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #6b7280;
    }

    .kyc-footer-item svg {
        width: 14px;
        height: 14px;
        color: #818cf8;
    }

    .kyc-footer-item strong {
        color: #a5b4fc;
    }

    /* ── Lightbox ── */
    .kyc-lightbox {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(0, 0, 0, .9);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s ease;
    }

    .kyc-lightbox.open {
        opacity: 1;
        pointer-events: all;
    }

    .kyc-lightbox img {
        max-width: 90vw;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 12px;
        box-shadow: 0 30px 80px rgba(0, 0, 0, .8);
        transform: scale(.92);
        transition: transform .3s cubic-bezier(.34, 1.56, .64, 1);
    }

    .kyc-lightbox.open img {
        transform: scale(1);
    }

    .kyc-lightbox-close {
        position: fixed;
        top: 20px;
        right: 24px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .1);
        border: 1px solid rgba(255, 255, 255, .2);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .18s;
        z-index: 100000;
    }

    .kyc-lightbox-close:hover {
        background: rgba(255, 255, 255, .2);
    }

    .kyc-lightbox-close svg {
        width: 18px;
        height: 18px;
    }
</style>

<div class="kyc-viewer">

    {{-- ── Info Bar ── --}}
    <div class="kyc-info-bar">
        <div class="kyc-info-item">
            <div class="label">Full Name</div>
            <div class="value">{{ $record->user->full_name }}</div>
        </div>
        <div class="kyc-info-item">
            <div class="label">ID Type &amp; Number</div>
            <div class="value">{{ ucfirst(str_replace('_', ' ', $record->id_type->value)) }}</div>
            <div class="value mono">{{ $record->id_number }}</div>
        </div>
        <div class="kyc-info-item">
            <div class="label">Status</div>
            <div class="value">
                @php
                    $statusVal = $record->status->value ?? strtolower($record->status->name ?? '');
                    $statusLabel = ucfirst($statusVal);
                @endphp
                <span class="kyc-status-badge {{ $statusVal }}">
                    <span class="dot"></span>
                    {{ $statusLabel }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Tab Strip ── --}}
    <div class="kyc-tabs">
        <button type="button" class="kyc-tab-btn active" onclick="kycSwitchTab(this, 'front')">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="5" width="18" height="14" rx="2" />
                <path d="M7 9h5M7 13h3" />
            </svg>
            ID Front
        </button>
        @if ($record->id_card_back_img_url)
            <button type="button" class="kyc-tab-btn" onclick="kycSwitchTab(this, 'back')">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="5" width="18" height="14" rx="2" />
                    <path d="M12 9h5M12 13h3" />
                </svg>
                ID Back
            </button>
        @endif
        <button type="button" class="kyc-tab-btn" onclick="kycSwitchTab(this, 'selfie')">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
            </svg>
            Selfie
        </button>
    </div>

    {{-- ── Front Panel ── --}}
    <div class="kyc-panel active" data-tab="front" id="kyc-tab-front">
        <div class="kyc-doc-card">
            <div class="kyc-doc-header">
                <div class="kyc-doc-header-left">
                    <div class="kyc-doc-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                            <path d="M7 9h5M7 13h3" />
                        </svg>
                    </div>
                    <div>
                        <div class="kyc-doc-title">ID Card — Front</div>
                        <div class="kyc-doc-sub">Identity document front side</div>
                    </div>
                </div>
                <a href="{{ $record->id_card_front_img_url }}" target="_blank" class="kyc-open-btn">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6" />
                        <polyline points="15 3 21 3 21 9" />
                        <line x1="10" y1="14" x2="21" y2="3" />
                    </svg>
                    Open full
                </a>
            </div>
            <div class="kyc-img-wrapper" onclick="kycOpenLightbox('{{ $record->id_card_front_img_url }}')">
                <img src="{{ $record->id_card_front_img_url }}" alt="ID Card Front" loading="lazy" />
                <div class="kyc-img-overlay">
                    <span class="kyc-img-zoom-hint">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35M11 8v6M8 11h6" />
                        </svg>
                        Click to zoom
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Back Panel ── --}}
    @if ($record->id_card_back_img_url)
        <div class="kyc-panel" data-tab="back" id="kyc-tab-back">
            <div class="kyc-doc-card">
                <div class="kyc-doc-header">
                    <div class="kyc-doc-header-left">
                        <div class="kyc-doc-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <rect x="3" y="5" width="18" height="14" rx="2" />
                                <path d="M12 9h5M12 13h3" />
                            </svg>
                        </div>
                        <div>
                            <div class="kyc-doc-title">ID Card — Back</div>
                            <div class="kyc-doc-sub">Identity document back side</div>
                        </div>
                    </div>
                    <a href="{{ $record->id_card_back_img_url }}" target="_blank" class="kyc-open-btn">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6" />
                            <polyline points="15 3 21 3 21 9" />
                            <line x1="10" y1="14" x2="21" y2="3" />
                        </svg>
                        Open full
                    </a>
                </div>
                <div class="kyc-img-wrapper" onclick="kycOpenLightbox('{{ $record->id_card_back_img_url }}')">
                    <img src="{{ $record->id_card_back_img_url }}" alt="ID Card Back" loading="lazy" />
                    <div class="kyc-img-overlay">
                        <span class="kyc-img-zoom-hint">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35M11 8v6M8 11h6" />
                            </svg>
                            Click to zoom
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Selfie Panel ── --}}
    <div class="kyc-panel" data-tab="selfie" id="kyc-tab-selfie">
        <div class="kyc-doc-card">
            <div class="kyc-doc-header">
                <div class="kyc-doc-header-left">
                    <div class="kyc-doc-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
                        </svg>
                    </div>
                    <div>
                        <div class="kyc-doc-title">Selfie with ID</div>
                        <div class="kyc-doc-sub">User holding their identity document</div>
                    </div>
                </div>
                <a href="{{ $record->selfie_img_url }}" target="_blank" class="kyc-open-btn">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6" />
                        <polyline points="15 3 21 3 21 9" />
                        <line x1="10" y1="14" x2="21" y2="3" />
                    </svg>
                    Open full
                </a>
            </div>
            <div class="kyc-img-wrapper" onclick="kycOpenLightbox('{{ $record->selfie_img_url }}')">
                <img src="{{ $record->selfie_img_url }}" alt="Selfie with ID" loading="lazy" />
                <div class="kyc-img-overlay">
                    <span class="kyc-img-zoom-hint">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35M11 8v6M8 11h6" />
                        </svg>
                        Click to zoom
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Footer ── --}}
    <div class="kyc-footer">
        <div class="kyc-footer-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M12 8v4l3 3M3 12a9 9 0 1018 0 9 9 0 00-18 0z" />
            </svg>
            <span>Submitted <strong>{{ $record->created_at->format('M j, Y · g:i A') }}</strong></span>
        </div>
        <div class="kyc-footer-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16v16H4zM4 9h16M9 9v11" />
            </svg>
            <span>Record ID: <strong>#{{ $record->id }}</strong></span>
        </div>
    </div>

</div>

{{-- ── Lightbox ── --}}
<div class="kyc-lightbox" id="kyc-lightbox" onclick="kycCloseLightbox()">
    <button class="kyc-lightbox-close" onclick="kycCloseLightbox()">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path d="M18 6 6 18M6 6l12 12" />
        </svg>
    </button>
    <img id="kyc-lightbox-img" src="" alt="Document" onclick="event.stopPropagation()" />
</div>

<script>
    function kycSwitchTab(btn, tab) {
        document.querySelectorAll('.kyc-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.kyc-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        const panel = document.getElementById('kyc-tab-' + tab);
        if (panel) {
            panel.classList.add('active');
        }
    }

    function kycOpenLightbox(src) {
        const lb = document.getElementById('kyc-lightbox');
        document.getElementById('kyc-lightbox-img').src = src;
        lb.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function kycCloseLightbox() {
        document.getElementById('kyc-lightbox').classList.remove('open');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            kycCloseLightbox();
        }
    });
</script>
