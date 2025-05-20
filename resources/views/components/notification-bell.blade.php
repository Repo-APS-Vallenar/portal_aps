<!-- Campana de notificaciones flotante Bootstrap -->
<div id="noti-bell-bootstrap" class="bell-floating">
    <button type="button"
        class="btn position-relative shadow rounded-circle p-0 d-flex align-items-center justify-content-center border-0"
        style="width: 56px; height: 56px; background: #e3f0ff; transition: box-shadow 0.2s;" data-bs-toggle="modal"
        data-bs-target="#notificacionesModal"
        onmouseover="this.style.background='#d0e7ff'; this.classList.add('shadow-lg')"
        onmouseout="this.style.background='#e3f0ff'; this.classList.remove('shadow-lg')">
        <i class="bi bi-bell-fill fs-2 text-primary"></i>
        <span id="noti-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="font-size: 0.8rem; display: none;">
            0
        </span>
    </button>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="notificacionesModal" tabindex="-1" aria-labelledby="notificacionesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="notificacionesModalLabel"><i class="bi bi-bell"></i> Notificaciones</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="list-group list-group-flush" id="notificaciones-lista">
                    <li class="list-group-item d-flex flex-column align-items-center justify-content-center text-center text-muted py-5"
                        id="noti-vacio" style="min-height: 180px;">
                        <i class="bi bi-bell fs-1 mb-2"></i>
                        <div>¡No tienes notificaciones nuevas!</div>
                    </li>
                </ul>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-primary btn-sm" id="marcar-todas-leidas" disabled>Marcar
                    todas como leídas</button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Polling cada 15 segundos
    function fetchNotificaciones() {
        fetch('/notifications?ajax=1')
            .then(res => res.json())
            .then(data => {
                const lista = document.getElementById('notificaciones-lista');
                const badge = document.getElementById('noti-badge');
                const btnTodas = document.getElementById('marcar-todas-leidas');
                lista.innerHTML = '';
                if (data.notifications.length === 0) {
                    badge.style.display = 'none';
                    lista.innerHTML = `<li class='list-group-item d-flex flex-column align-items-center justify-content-center text-center text-muted py-5' id='noti-vacio' style='min-height: 180px;'>
                    <i class='bi bi-bell fs-1 mb-2'></i>
                    <div>¡No tienes notificaciones nuevas!</div>
                </li>`;
                    btnTodas.disabled = true;
                } else {
                    badge.style.display = 'inline-flex';
                    badge.textContent = data.notifications.filter(n => !n.is_read).length;
                    data.notifications.forEach(noti => {
                        // Formatear fecha y hora
                        const fecha = new Date(noti.created_at);
                        const fechaStr = fecha.toLocaleDateString('es-CL', { day: '2-digit', month: '2-digit', year: 'numeric' });
                        const horaStr = fecha.toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' });
                        lista.innerHTML += `
                    <li class='list-group-item noti-item d-flex flex-column align-items-start gap-2 p-3 mb-2 rounded-3 shadow-sm ${!noti.is_read ? 'noti-unread' : ''}'>
                        <div class='d-flex justify-content-between w-100'>
                            <span class='text-muted small'>${fechaStr} ${horaStr}</span>
                            <span class='badge bg-warning text-dark px-2 py-1 small fw-bold'>${(noti.title || '').replace(/\(para:.*\)/, '')}</span>
                        </div>
                        <div class='text-secondary small mb-1' style='margin-left:auto; margin-right:0; width:100%; text-align:right;'>${noti.data && noti.data.remitente ? 'Por: ' + noti.data.remitente : ''}</div>
                        <div class='noti-message mb-2'>${noti.message}</div>
                        <button class='btn btn-outline-danger btn-sm btn-eliminar-noti' data-id='${noti.id}' title='${!noti.is_read ? 'Debes marcar como leída antes de eliminar' : 'Eliminar notificación'}' ${!noti.is_read ? 'disabled' : ''}><i class='bi bi-trash'></i></button>
                    </li>`;
                    });
                    if (data.notifications.length === 0 || data.notifications.filter(n => !n.is_read).length === 0) {
                        btnTodas.disabled = true;
                    } else {
                        btnTodas.disabled = false;
                    }
                }
            });
    }

    // Inicializar polling
    fetchNotificaciones();
    setInterval(fetchNotificaciones, 15000);

    document.addEventListener('DOMContentLoaded', function () {
        // Marcar como leída
        document.body.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-marcar-leida')) {
                const id = e.target.getAttribute('data-id');
                fetch(`/notifications/${id}/read`, { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    } 
                })
                .then(() => fetchNotificaciones());
            }
            // Eliminar notificación
            if (e.target.classList.contains('btn-eliminar-noti') || (e.target.closest && e.target.closest('.btn-eliminar-noti'))) {
                const btn = e.target.closest('.btn-eliminar-noti');
                const id = btn.getAttribute('data-id');
                const notiItem = btn.closest('.noti-item');
                // Animación fade-out (opcional)
                notiItem.classList.add('fade-out');
                setTimeout(() => {
                    notiItem.remove();
                }, 300); // Quitar del DOM tras la animación
                // Llamada al backend, pero no esperamos la respuesta para quitar del DOM
                fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(() => fetchNotificaciones());
            }
        });
        // Marcar todas como leídas
        document.getElementById('marcar-todas-leidas').addEventListener('click', function () {
            fetch('/notifications/mark-all-read', { 
                method: 'POST', 
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                } 
            })
            .then(() => fetchNotificaciones());
        });
    });

    // Después de renderizar notificaciones:
    if (window.bootstrap) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.forEach(function (el) {
            if (!el._tooltip) {
                el._tooltip = new bootstrap.Tooltip(el);
            }
        });
    }
</script>

<style>
.noti-item {
    border-left: 5px solid #0d6efd;
    background: #fff;
    margin-bottom: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    animation: fadeIn 0.5s ease;
}
.noti-unread {
    background: #fffbe6 !important;
    border-left: 5px solid #0d6efd !important;
}
.noti-badge-title .badge, .noti-item .badge {
    font-size: 0.85em !important;
    border-radius: 6px;
    background: #ffe066 !important;
    color: #856404 !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    transition: all 0.2s ease;
}
.noti-message {
    font-size: 1em;
    color: #333;
    transition: all 0.2s ease;
}
#noti-badge {
    min-width: 22px;
    height: 18px;
    padding: 0 4px;
    font-size: 0.82rem;
    border-radius: 10px;
    display: flex !important;
    align-items: center;
    justify-content: center;
    background: #e74c3c !important;
    color: #fff !important;
    font-weight: 700;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    z-index: 1000;
    line-height: 1;
    position: absolute;
    right: -8px;
    top: -8px;
    left: auto !important;
}
#notificaciones-lista {
    max-height: 350px;
    overflow-y: auto;
    padding-right: 4px;
}
#noti-bell-bootstrap {
    z-index: 99999;
}
@media (max-width: 600px) {
    .modal-dialog {
        max-width: 98vw !important;
        margin: 0 auto !important;
        display: flex;
        align-items: center;
        min-height: 100vh;
    }
    .modal-content {
        border-radius: 0 !important;
        overflow: hidden;
        padding: 0.5rem 0.2rem;
        margin-top: 0 !important;
    }
    .modal-header.bg-primary {
        border-top-left-radius: 0 !important;
        border-top-right-radius: 0 !important;
    }
    #notificaciones-lista {
        max-height: 45vh;
        padding-right: 0;
    }
    #noti-bell-bootstrap {
        left: 10px !important;
        top: 60px !important;
    }
    #noti-badge {
        min-width: 22px;
        height: 18px;
        font-size: 0.78rem;
        right: -6px;
        top: -6px;
        left: auto !important;
        padding: 0 5px;
    }
    body, .main, main {
        display: flex !important;
        flex-direction: column;
        align-items: center !important;
        justify-content: flex-start;
        min-height: 100vh;
        width: 100vw;
        margin: 0;
        padding: 0;
        background: #f8fafc;
    }
}
.fade-out {
    opacity: 0 !important;
    transform: translateX(20px) !important;
    transition: all 0.3s ease;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>