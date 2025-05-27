import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '5726e87eb9b4749dfa32', // Tu PUSHER_APP_KEY
    cluster: 'sa1',             // Tu PUSHER_APP_CLUSTER
    forceTLS: true
});
