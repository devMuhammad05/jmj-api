<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JMJ API — Broadcast Console</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #0f172a; color: #e2e8f0; font-family: 'Courier New', monospace; margin: 0; padding: 0; }
        .header { background: #1e293b; padding: 16px 24px; border-bottom: 1px solid #334155; display: flex; align-items: center; gap: 12px; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 700; color: #f1f5f9; }
        .dot { width: 10px; height: 10px; border-radius: 50%; background: #64748b; transition: background 0.3s; }
        .dot.connected { background: #22c55e; box-shadow: 0 0 8px #22c55e88; }
        .dot.error { background: #ef4444; box-shadow: 0 0 8px #ef444488; }
        .status-text { font-size: 12px; color: #94a3b8; margin-left: 4px; }
        .setup { padding: 20px 24px; background: #1e293b; border-bottom: 1px solid #334155; display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
        .setup label { display: block; font-size: 11px; color: #94a3b8; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em; }
        .setup input { background: #0f172a; border: 1px solid #334155; color: #e2e8f0; padding: 8px 12px; border-radius: 6px; font-family: monospace; font-size: 13px; width: 320px; outline: none; }
        .setup input:focus { border-color: #6366f1; }
        .setup input.small { width: 120px; }
        .btn { padding: 8px 18px; border-radius: 6px; border: none; cursor: pointer; font-size: 13px; font-weight: 600; transition: opacity 0.2s; }
        .btn-primary { background: #6366f1; color: white; }
        .btn-primary:hover { opacity: 0.85; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { opacity: 0.85; }
        .btn-ghost { background: #334155; color: #94a3b8; }
        .btn-ghost:hover { opacity: 0.85; }
        .channels-bar { padding: 10px 24px; background: #1e293b; border-bottom: 1px solid #334155; display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .channels-bar span { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-right: 4px; }
        .channel-tag { background: #1e3a5f; color: #60a5fa; font-size: 11px; padding: 3px 10px; border-radius: 12px; border: 1px solid #2563eb44; }
        .log-area { padding: 16px 24px; }
        .log-entry { display: flex; gap: 12px; padding: 8px 12px; margin-bottom: 4px; border-radius: 6px; font-size: 13px; line-height: 1.5; border-left: 3px solid transparent; }
        .log-entry.info { border-left-color: #6366f1; background: #1e1b4b22; }
        .log-entry.success { border-left-color: #22c55e; background: #14532d22; }
        .log-entry.warning { border-left-color: #f59e0b; background: #78350f22; }
        .log-entry.error { border-left-color: #ef4444; background: #7f1d1d22; }
        .log-entry.event { border-left-color: #a78bfa; background: #2e106522; }
        .log-time { color: #475569; min-width: 90px; font-size: 11px; margin-top: 2px; }
        .log-badge { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; min-width: 60px; text-align: center; margin-top: 2px; }
        .badge-info { background: #4338ca; color: white; }
        .badge-event { background: #7c3aed; color: white; }
        .badge-success { background: #15803d; color: white; }
        .badge-error { background: #b91c1c; color: white; }
        .badge-warning { background: #b45309; color: white; }
        .log-content { flex: 1; }
        .log-channel { color: #818cf8; font-size: 11px; }
        .log-payload { color: #a5f3fc; margin-top: 4px; white-space: pre-wrap; word-break: break-all; }
        .empty-state { text-align: center; padding: 60px 24px; color: #475569; }
        .empty-state .icon { font-size: 40px; margin-bottom: 12px; }
        .toolbar { display: flex; gap: 8px; align-items: center; padding: 10px 24px; border-bottom: 1px solid #1e293b; }
        .toolbar span { font-size: 12px; color: #475569; }
        #log-count { color: #818cf8; font-weight: 700; }
    </style>
</head>
<body>

<div class="header">
    <div class="dot" id="conn-dot"></div>
    <h1>JMJ API — Broadcast Console</h1>
    <span class="status-text" id="conn-status">Disconnected</span>
</div>

<div class="setup">
    <div>
        <label>Auth Token (Bearer)</label>
        <input type="text" id="auth-token" placeholder="Paste your Sanctum token here..." />
    </div>
    <div>
        <label>User ID</label>
        <input type="text" id="user-id" class="small" placeholder="e.g. 1" />
    </div>
    <div style="display:flex;gap:8px;">
        <button class="btn btn-primary" onclick="connect()">Connect</button>
        <button class="btn btn-danger" onclick="disconnect()">Disconnect</button>
        <button class="btn btn-ghost" onclick="clearLog()">Clear Log</button>
        <button class="btn btn-ghost" onclick="fireTest()" title="GET /test-broadcast">⚡ Fire Test</button>
    </div>
</div>

<div class="channels-bar" id="channels-bar" style="display:none;">
    <span>Listening on:</span>
</div>

<div class="toolbar">
    <span>Events captured: <span id="log-count">0</span></span>
    <span style="margin-left:auto;font-size:11px;color:#334155;">All events are also logged to the browser console (F12)</span>
</div>

<div class="log-area" id="log-area">
    <div class="empty-state" id="empty-state">
        <div class="icon">📡</div>
        <div>Enter your token and user ID, then click <strong>Connect</strong></div>
        <div style="margin-top:8px;font-size:12px;">Broadcast notifications will appear here in real time</div>
    </div>
</div>

<script>
    let echoInstance = null;
    let subscribedChannels = [];
    let logCount = 0;

    // Restore saved token/ID from localStorage, then auto-subscribe to public channel
    document.addEventListener('DOMContentLoaded', () => {
        const savedToken = localStorage.getItem('auth_token');
        const savedUserId = localStorage.getItem('broadcast_user_id');
        if (savedToken) document.getElementById('auth-token').value = savedToken;
        if (savedUserId) document.getElementById('user-id').value = savedUserId;

        subscribePublicChannels();
    });

    function timestamp() {
        return new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }

    function addLog(type, badge, channel, message, payload = null) {
        document.getElementById('empty-state')?.remove();
        logCount++;
        document.getElementById('log-count').textContent = logCount;

        const entry = document.createElement('div');
        entry.className = `log-entry ${type}`;

        const payloadHtml = payload
            ? `<div class="log-payload">${JSON.stringify(payload, null, 2)}</div>`
            : '';

        const channelHtml = channel
            ? `<div class="log-channel"># ${channel}</div>`
            : '';

        entry.innerHTML = `
            <div class="log-time">${timestamp()}</div>
            <div class="log-badge badge-${type === 'event' ? 'event' : type === 'success' ? 'success' : type === 'error' ? 'error' : type === 'warning' ? 'warning' : 'info'}">${badge}</div>
            <div class="log-content">
                ${channelHtml}
                <div>${message}</div>
                ${payloadHtml}
            </div>
        `;

        document.getElementById('log-area').prepend(entry);
    }

    function connect() {
        const token = document.getElementById('auth-token').value.trim();
        const userId = document.getElementById('user-id').value.trim();

        if (!token) { alert('Please enter your auth token.'); return; }
        if (!userId) { alert('Please enter your user ID.'); return; }

        // Save for next visit
        localStorage.setItem('auth_token', token);
        localStorage.setItem('broadcast_user_id', userId);

        // Disconnect existing connection
        if (echoInstance) disconnect(false);

        setStatus('connecting', 'Connecting...');
        addLog('info', 'INIT', null, `Connecting to Reverb as user #${userId}…`);
        console.info('[Broadcast Console] Connecting to Reverb...', { userId });

        // Re-initialize Echo with the provided token
        echoInstance = new Echo({
            broadcaster: 'reverb',
            key: '{{ env("VITE_REVERB_APP_KEY", env("REVERB_APP_KEY")) }}',
            wsHost: '{{ env("REVERB_HOST", "localhost") }}',
            wsPort: {{ env("REVERB_PORT", 8080) }},
            wssPort: {{ env("REVERB_PORT", 8080) }},
            forceTLS: '{{ env("REVERB_SCHEME", "http") }}' === 'https',
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/api/broadcasting/auth',
            auth: {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: 'application/json',
                },
            },
        });

        const connector = echoInstance.connector;

        connector.pusher.connection.bind('connected', () => {
            setStatus('connected', 'Connected');
            addLog('success', 'CONN', null, 'WebSocket connected to Reverb ✓');
            console.info('[Broadcast Console] Connected to Reverb ✓');
            subscribeChannels(userId);
        });

        connector.pusher.connection.bind('disconnected', () => {
            setStatus('disconnected', 'Disconnected');
            addLog('warning', 'DISC', null, 'WebSocket disconnected');
            console.warn('[Broadcast Console] Disconnected');
        });

        connector.pusher.connection.bind('error', (err) => {
            setStatus('error', 'Error');
            addLog('error', 'ERR', null, 'Connection error', err);
            console.error('[Broadcast Console] Connection error', err);
        });

        connector.pusher.connection.bind('failed', () => {
            setStatus('error', 'Failed');
            addLog('error', 'FAIL', null, 'Connection failed — check Reverb is running on port {{ env("REVERB_PORT", 8080) }}');
            console.error('[Broadcast Console] Connection failed');
        });
    }

    function subscribeChannels(userId) {
        const channelsBar = document.getElementById('channels-bar');
        channelsBar.style.display = 'flex';

        // 1. Private notification channel for the user
        const privateChannel = `App.Models.User.${userId}`;
        echoInstance.private(privateChannel).notification((notification) => {
            addLog('event', 'NOTIF', privateChannel, `Notification: ${notification.type || 'unknown'}`, notification);
            console.log(`[Broadcast Console][${privateChannel}] Notification:`, notification);
        });
        addChannelTag(privateChannel, 'private');
        addLog('info', 'SUB', null, `Subscribed to private channel: ${privateChannel}`);
        subscribedChannels.push(privateChannel);

        // 2. Catch-all: listen to ALL events on the private channel
        echoInstance.private(privateChannel).listenToAll((event, data) => {
            // Skip internal Pusher/Echo events
            if (event.startsWith('pusher:') || event.startsWith('pusher_internal:')) return;
            addLog('event', 'EVENT', privateChannel, `Event: ${event}`, data);
            console.log(`[Broadcast Console][${privateChannel}] Event: ${event}`, data);
        });

        console.info('[Broadcast Console] Subscribed to channels:', subscribedChannels);
    }

    function addChannelTag(channel, type) {
        const bar = document.getElementById('channels-bar');
        const tag = document.createElement('span');
        tag.className = 'channel-tag';
        tag.textContent = `${type}:${channel}`;
        bar.appendChild(tag);
    }

    function disconnect(log = true) {
        if (echoInstance) {
            echoInstance.disconnect();
            echoInstance = null;
        }
        subscribedChannels = [];
        document.getElementById('channels-bar').style.display = 'none';
        document.getElementById('channels-bar').querySelectorAll('.channel-tag').forEach(el => el.remove());
        setStatus('disconnected', 'Disconnected');
        if (log) {
            addLog('warning', 'DISC', null, 'Manually disconnected');
            console.warn('[Broadcast Console] Manually disconnected');
        }
    }

    function clearLog() {
        logCount = 0;
        document.getElementById('log-count').textContent = 0;
        document.getElementById('log-area').innerHTML = `
            <div class="empty-state" id="empty-state">
                <div class="icon">📡</div>
                <div>Log cleared. Waiting for events…</div>
            </div>`;
    }

    function subscribePublicChannels() {
        const publicEcho = new Echo({
            broadcaster: 'reverb',
            key: '{{ env("REVERB_APP_KEY") }}',
            wsHost: '{{ env("REVERB_HOST", "127.0.0.1") }}',
            wsPort: {{ env("REVERB_PORT", 8080) }},
            wssPort: {{ env("REVERB_PORT", 8080) }},
            forceTLS: '{{ env("REVERB_SCHEME", "http") }}' === 'https',
            enabledTransports: ['ws', 'wss'],
        });

        publicEcho.connector.pusher.connection.bind('connected', () => {
            addLog('success', 'CONN', null, 'Public channel connected to Reverb ✓');
            console.info('[Broadcast Console] Public channel connected ✓');
            document.getElementById('channels-bar').style.display = 'flex';
            addChannelTag('test-broadcast', 'public');
            addChannelTag('announcements', 'public');
        });

        publicEcho.channel('test-broadcast').listen('.test.message', (data) => {
            addLog('event', 'PUBLIC', 'test-broadcast', 'test.message received', data);
            console.log('[Broadcast Console][test-broadcast] test.message:', data);
        });

        publicEcho.channel('announcements').listenToAll((event, data) => {
            if (event.startsWith('pusher:') || event.startsWith('pusher_internal:')) return;
            addLog('event', 'ANNOUNCE', 'announcements', `${event}`, data);
            console.log('[Broadcast Console][announcements] Event:', event, data);
        });
    }

    async function fireTest() {
        addLog('info', 'FIRE', 'test-broadcast', 'Firing test broadcast via GET /test-broadcast…');
        const res = await fetch('/test-broadcast');
        const json = await res.json();
        addLog('success', 'HTTP', null, `Response: ${JSON.stringify(json)}`);
        console.info('[Broadcast Console] /test-broadcast response:', json);
    }

    function setStatus(state, text) {
        const dot = document.getElementById('conn-dot');
        const statusText = document.getElementById('conn-status');
        dot.className = 'dot';
        if (state === 'connected') dot.classList.add('connected');
        if (state === 'error') dot.classList.add('error');
        statusText.textContent = text;
    }
</script>

</body>
</html>
