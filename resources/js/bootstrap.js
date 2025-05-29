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

window.showAlert = function(message, type = 'success', parent = null, duration = 2000) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show mt-3`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    if (parent) {
        parent.insertBefore(alert, parent.firstChild);
    } else {
        document.body.appendChild(alert);
    }
    setTimeout(() => { alert.remove(); }, duration);
};

// Refrescar el token CSRF automáticamente cada 5 minutos
function refreshCsrfToken() {
    fetch('/refresh-csrf').then(async res => {
        const data = await res.json();
        if (data.csrfToken) {
            // Actualiza el meta tag
            let meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', data.csrfToken);
            // Actualiza Axios
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = data.csrfToken;
            }
        });
}
setInterval(refreshCsrfToken, 5 * 60 * 1000); // Cada 5 minutos
refreshCsrfToken(); // Al cargar
