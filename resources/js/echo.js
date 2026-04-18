import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Expose the Echo class so inline scripts can call `new Echo({...})`
window.Echo = Echo;
