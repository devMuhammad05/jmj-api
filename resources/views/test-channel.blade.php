<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test-broadcast channel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-gray-100 font-mono p-8 min-h-screen">

    <h1 class="text-lg font-bold mb-1">test-broadcast channel</h1>
    <p class="text-gray-500 text-sm mb-6">Listening for <span class="text-purple-400">.test.message</span> events in real time.</p>

    <div class="inline-flex items-center gap-2 text-sm text-gray-400 mb-6">
        <span id="dot" class="w-2.5 h-2.5 rounded-full bg-gray-600"></span>
        <span id="status-text">Connecting…</span>
    </div>

    <div id="events" class="space-y-3 max-w-2xl"></div>

    <p id="empty" class="text-gray-600 text-sm mt-6">No events received yet.</p>

    <div class="mt-8">
        <button onclick="fireTest()"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
            ⚡ Fire test.message
        </button>
    </div>

    <script type="module">
        // window.Echo is the pre-configured instance from resources/js/echo.js
        const echo = window.Echo;

        echo.connector.pusher.connection.bind('connected', () => {
            document.getElementById('dot').className = 'w-2.5 h-2.5 rounded-full bg-green-500';
            document.getElementById('status-text').textContent = 'Connected — listening on test-broadcast & announcements';
            console.info('[test-channel] Connected to Reverb ✓');
        });

        echo.connector.pusher.connection.bind('failed', () => {
            document.getElementById('dot').className = 'w-2.5 h-2.5 rounded-full bg-red-500';
            document.getElementById('status-text').textContent = 'Connection failed — is Reverb running?';
        });

        echo.channel('test-broadcast')
            .listen('.test.message', (event) => {
                console.log('[test-channel] test.message:', event);
                document.getElementById('empty')?.remove();

                const card = document.createElement('div');
                card.className = 'bg-gray-900 border border-purple-800/40 rounded-lg p-4';
                card.innerHTML = `
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-purple-400 text-xs font-semibold">.test.message</span>
                        <span class="text-gray-600 text-xs">${new Date().toLocaleTimeString()}</span>
                    </div>
                    <div class="text-gray-200 text-sm mb-1">${event.message ?? '—'}</div>
                    <div class="text-gray-500 text-xs">source: ${event.source ?? '—'}</div>
                    <pre class="mt-3 text-xs text-cyan-400 bg-gray-950 rounded p-2 overflow-auto">${JSON.stringify(event, null, 2)}</pre>
                `;

                document.getElementById('events').prepend(card);
            });

        echo.channel('announcements')
            .listenToAll((eventName, event) => {
                if (eventName.startsWith('pusher:') || eventName.startsWith('pusher_internal:')) return;
                console.log('[test-channel] announcement:', eventName, event);
                document.getElementById('empty')?.remove();

                const card = document.createElement('div');
                card.className = 'bg-gray-900 border border-yellow-700/40 rounded-lg p-4';
                card.innerHTML = `
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-yellow-400 text-xs font-semibold">📢 announcement</span>
                        <span class="text-gray-600 text-xs">${new Date().toLocaleTimeString()}</span>
                    </div>
                    <div class="text-gray-200 text-sm font-semibold mb-1">${event.title ?? '—'}</div>
                    <div class="text-gray-400 text-sm mb-1">${event.message ?? '—'}</div>
                    <pre class="mt-3 text-xs text-cyan-400 bg-gray-950 rounded p-2 overflow-auto">${JSON.stringify(event, null, 2)}</pre>
                `;

                document.getElementById('events').prepend(card);
            });

        window.fireTest = async function () {
            const res = await fetch('/test-broadcast');
            console.log('[test-channel] fire response:', await res.json());
        };
    </script>

</body>
</html>
