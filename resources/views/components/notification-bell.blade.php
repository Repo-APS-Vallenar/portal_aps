<!-- Campana de notificaciones flotante Bootstrap -->
<div id="noti-bell-bootstrap" class="bell-floating">
    <button type="button"
        class="btn position-relative shadow rounded-circle p-0 d-flex align-items-center justify-content-center border-0"
        style="width: 56px; height: 56px; background: #e3f0ff; transition: all 0.3s ease;" 
        data-bs-toggle="modal"
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
                <h5 class="modal-title" id="notificacionesModalLabel">
                    <i class="bi bi-bell me-2"></i> Notificaciones
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0">
                <div class="noti-header p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="noti-filters">
                            <button class="btn btn-sm btn-outline-primary active" data-filter="all">
                                Todas
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="marcar-todas-leidas" disabled>
                            <i class="bi bi-check2-all me-1"></i>Marcar todas como leídas
                        </button>
                    </div>
                </div>
                <div class="noti-container" id="notificaciones-lista" style="max-height: 400px; overflow-y: auto;">
                    <!-- Las notificaciones se cargarán aquí dinámicamente -->
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-danger btn-sm" id="limpiar-notificaciones">
                    <i class="bi bi-trash me-1"></i>Limpiar notificaciones leídas
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para limpiar notificaciones -->
<div class="modal fade" id="confirmLimpiarNotificacionesModal" tabindex="-1" aria-labelledby="confirmLimpiarNotificacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmLimpiarNotificacionesLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar todas las notificaciones leídas?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmLimpiarNotificacionesBtn">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notiContainer = document.getElementById('notificaciones-lista');
    const notiBadge = document.getElementById('noti-badge');
    const btnTodas = document.getElementById('marcar-todas-leidas');
    const btnLimpiar = document.getElementById('limpiar-notificaciones');
    let currentFilter = 'all';

    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        // Si es de hoy, mostrar la hora
        if (diff < 24 * 60 * 60 * 1000) {
            return `Hoy ${date.toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' })}`;
        }
        // Si es de ayer
        if (diff < 48 * 60 * 60 * 1000) {
            return `Ayer ${date.toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' })}`;
        }
        // Si es de esta semana
        if (diff < 7 * 24 * 60 * 60 * 1000) {
            return date.toLocaleDateString('es-CL', { weekday: 'long', hour: '2-digit', minute: '2-digit' });
        }
        // Para fechas más antiguas
        return date.toLocaleDateString('es-CL', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function renderNotification(noti) {
        console.log('NOTIFICACION DEBUG:', noti);
        const isUnread = !noti.is_read;
        let icon = '<i class="bi bi-bell"></i>';
        let color = 'primary';
        let typeLabel = 'Notificación';
        let extraInfo = '';
        let ticketNumber = noti.ticket_id || '';
        let notiTitle = noti.title;
        let notiMessage = noti.message;
        if (noti.type === 'ticket_commented') {
            let user = (noti.data && noti.data.commenter_name) || noti.commenter_name || (noti.data && (noti.data.creator || noti.data.commented_by)) || 'Desconocido';
            let comment = noti.comment || (noti.data && noti.data.comment) || '';
            if (comment && comment.length > 50) {
                comment = comment.substring(0, 50) + '...';
            }
            notiTitle = `Nuevo comentario en el ticket #${ticketNumber}`;
            notiMessage = comment ? `"${comment}"` : 'Nuevo comentario en el ticket';
            // Mostrar "Enviado por: NOMBRE" abajo SIEMPRE
            extraInfo += `<div class='noti-meta small text-secondary mt-1'>Enviado por: <b>${user}</b></div>`;
        } else if (noti.type === 'ticket_created') {
            notiTitle = `Nuevo ticket #${ticketNumber}`;
            notiMessage = (noti.data && noti.data.description) ? noti.data.description : noti.message;
        } else if (noti.type === 'ticket_updated') {
            notiTitle = `Ticket #${ticketNumber} actualizado`;
            notiMessage = noti.message;
        }
        // Badge de nuevo
        const newBadge = isUnread ? '<span class="badge bg-danger ms-2">Nuevo</span>' : '';
        // Mostrar cambios si es actualización
        if (noti.type === 'ticket_updated' && noti.data && noti.data.changes) {
            let cambios = '';
            for (const campo in noti.data.changes) {
                if (noti.data.changes.hasOwnProperty(campo)) {
                    const cambio = noti.data.changes[campo];
                    cambios += `<li><strong>${campo}:</strong> ${cambio.old} → ${cambio.new}</li>`;
                }
            }
            if (cambios) {
                extraInfo += `<ul class='noti-changes mb-1 mt-1'>${cambios}</ul>`;
            }
        }
        return `
            <div class="noti-item shadow-sm mb-2 rounded border border-${color} ${isUnread ? 'noti-unread' : ''}" data-id="${noti.id}" style="background: ${isUnread ? '#f8fafd' : '#fff'}; transition: background 0.3s;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    ${icon}
                    <span class="fw-bold text-${color}">${typeLabel}</span>
                    ${newBadge}
                    <small class="text-muted ms-auto">${formatDate(noti.created_at)}</small>
                </div>
                <div class="noti-content ps-1">
                    <h6 class="noti-title mb-1">${notiTitle}</h6>
                    <p class="noti-message mb-1">${notiMessage}</p>
                    ${extraInfo}
                    ${noti.data ? `
                        <div class="noti-meta small text-secondary">
                            ${noti.data.category ? `<span><i class='bi bi-tag'></i> ${noti.data.category}</span>` : ''}
                            ${noti.data.creator ? `<span><i class='bi bi-person'></i> ${noti.data.creator}</span>` : ''}
                        </div>
                    ` : ''}
                </div>
                <div class="noti-actions d-flex gap-2 mt-2">
                    ${(noti.link || noti.url) ? `
                        <a href="${noti.link || noti.url}" class="btn btn-sm btn-outline-${color} px-2 py-1">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Ver detalles
                        </a>
                    ` : ''}
                    ${isUnread ? `
                        <button class="btn btn-sm btn-outline-primary mark-read px-2 py-1" data-id="${noti.id}">
                            <i class="bi bi-check2 me-1"></i>Marcar como leída
                        </button>
                    ` : `

                    `}
                </div>
            </div>
        `;
    }

    function fetchNotificaciones() {
        fetch('/notifications?ajax=1')
            .then(res => res.json())
            .then(data => {
                const notifications = data.notifications;
                const unreadCount = notifications.filter(n => !n.is_read).length;
                
                // Actualizar badge
                notiBadge.textContent = unreadCount;
                notiBadge.style.display = unreadCount > 0 ? 'flex' : 'none';
                
                // Actualizar botón de marcar todas
                btnTodas.disabled = unreadCount === 0;
                
                // Mostrar todas las notificaciones
                if (notifications.length === 0) {
                    notiContainer.innerHTML = `
                        <div class="noti-empty">
                            <i class="fas fa-bell-slash"></i>
                            <p>No hay notificaciones</p>
                        </div>
                    `;
                } else {
                    notiContainer.innerHTML = notifications.map(renderNotification).join('');
                }
            });
    }

    // Event Listeners
    document.querySelectorAll('[data-filter]').forEach(btn => {
        if (btn.dataset.filter !== 'all') {
            btn.remove();
        }
    });
    currentFilter = 'all';

    btnTodas.addEventListener('click', function() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(() => fetchNotificaciones());
    });

    btnLimpiar.addEventListener('click', function() {
        // Mostrar modal de confirmación en vez de confirm()
        const modal = new bootstrap.Modal(document.getElementById('confirmLimpiarNotificacionesModal'));
        modal.show();
    });

    // Acción al confirmar en el modal
    document.getElementById('confirmLimpiarNotificacionesBtn').addEventListener('click', function() {
        fetch('/notifications/cleanup', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(() => {
            fetchNotificaciones();
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmLimpiarNotificacionesModal'));
            modal.hide();
        });
    });

    // Marcar notificación como leída por AJAX
    notiContainer.addEventListener('click', function(e) {
        if (e.target.closest('.mark-read')) {
            const btn = e.target.closest('.mark-read');
            const notiId = btn.getAttribute('data-id');
            btn.disabled = true;
            fetch(`/notifications/${notiId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Cambiar visualmente la notificación
                    const notiItem = btn.closest('.noti-item');
                    if (notiItem) {
                        notiItem.classList.remove('noti-unread');
                        notiItem.style.background = '#fff';
                        // Cambiar acciones: ocultar botón marcar como leída, mostrar eliminar
                        btn.remove();
                        const actions = notiItem.querySelector('.noti-actions');
                        if (actions) {
                            actions.insertAdjacentHTML('beforeend', `<button class="btn btn-sm btn-outline-danger delete-noti px-2 py-1" data-id="${notiId}"><i class="bi bi-trash me-1"></i>Eliminar</button>`);
                        }
                    }
                    // Actualizar badge
                    updateNotiBadge(-1);
                    showGlobalToast('Notificación marcada como leída', 'success');
                } else {
                    showGlobalToast(data.message || 'Error al marcar como leída', 'error');
                }
            })
            .catch(() => {
                showGlobalToast('Error al marcar como leída', 'error');
            })
            .finally(() => {
                btn.disabled = false;
            });
        }
    });

    // Función para actualizar el badge de no leídas
    function updateNotiBadge(delta) {
        let count = parseInt(notiBadge.textContent) || 0;
        count = Math.max(0, count + delta);
        notiBadge.textContent = count;
        notiBadge.style.display = count > 0 ? 'flex' : 'none';
    }

    // Eliminar notificación individual por AJAX
    notiContainer.addEventListener('click', function(e) {
        if (e.target.closest('.delete-noti')) {
            const btn = e.target.closest('.delete-noti');
            const notiId = btn.getAttribute('data-id');
            btn.disabled = true;
            fetch(`/notifications/${notiId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Eliminar del DOM
                    const notiItem = btn.closest('.noti-item');
                    if (notiItem) notiItem.remove();
                    showGlobalToast('Notificación eliminada', 'success');
                } else {
                    showGlobalToast(data.message || 'Error al eliminar notificación', 'error');
                }
            })
            .catch(() => {
                showGlobalToast('Error al eliminar notificación', 'error');
            })
            .finally(() => {
                btn.disabled = false;
            });
        }
    });

    // Inicializar
    fetchNotificaciones();

    // Polling de respaldo cada 15 segundos (Se refresca cada 15 segundos para que no se quede sin notificaciones)
    setInterval(fetchNotificaciones, 15000);

    // Suscribirse al canal privado de notificaciones del usuario con Echo
    if (window.Echo && window.Laravel && window.Laravel.userId) {
        window.Echo.private('App.Models.User.' + window.Laravel.userId)
            .notification((notification) => {
                console.log('NOTIFICACION EN VIVO:', notification);
                if (notiContainer) {
                    const html = renderNotification(notification);
                    notiContainer.insertAdjacentHTML('afterbegin', html);
                    updateNotiBadge(1);
                    showGlobalToast('¡Nueva notificación recibida!', 'info');
                }
            })
            .listen('.comment-notification', (data) => {
                // Renderizar la notificación usando los datos del evento personalizado
                const html = renderNotification({
                    type: 'ticket_commented',
                    ticket_id: data.ticket_id,
                    commenter_name: data.commenter_name,
                    comment: data.comment,
                    created_at: data.created_at,
                    is_read: false,
                    title: `Nuevo comentario en el ticket #${data.ticket_id}`,
                    message: `${data.commenter_name}: \"${data.comment}\"`
                });
                notiContainer.insertAdjacentHTML('afterbegin', html);
                updateNotiBadge(1);
                showGlobalToast('¡Nuevo comentario recibido!', 'info');
            });
    }

    // Al abrir el modal, marcar todas como leídas automáticamente
    const notificacionesModal = document.getElementById('notificacionesModal');
    notificacionesModal.addEventListener('shown.bs.modal', function () {
            fetch('/notifications/mark-all-read', { 
                method: 'POST', 
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                } 
        }).then(() => fetchNotificaciones());
    });

    // Ocultar el botón de 'Marcar todas como leídas'
    document.getElementById('marcar-todas-leidas').style.display = 'none';
});
</script>

<style>
.noti-item {
    border-left: 5px solid #0d6efd;
    background: #fff;
    margin: 8px 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    animation: fadeIn 0.5s ease;
    padding: 16px;
    border-radius: 10px;
    position: relative;
}

.noti-unread {
    background: #f8fafd !important;
    border-left: 5px solid #0d6efd !important;
}

.noti-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.noti-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.noti-title {
    font-size: 1.1em;
    font-weight: 600;
    color: #212529;
    margin-bottom: 4px;
}

.noti-message {
    font-size: 0.97em;
    color: #495057;
    margin-bottom: 4px;
    line-height: 1.4;
}

.noti-meta {
    font-size: 0.85em;
    color: #6c757d;
    display: flex;
    gap: 12px;
    margin-top: 4px;
}

.noti-meta span {
        display: flex;
        align-items: center;
    gap: 4px;
    }

.noti-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
    }

.noti-empty {
    text-align: center;
    padding: 32px;
    color: #6c757d;
    }

.noti-empty i {
    font-size: 2.5em;
    margin-bottom: 16px;
    color: #dee2e6;
    }

.noti-filters {
    display: flex;
    gap: 8px;
}

.noti-filters .btn {
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 0.9em;
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

.fade-out {
    opacity: 0;
    transform: translateX(20px);
    transition: all 0.3s ease;
}

.noti-changes {
    font-size: 0.95em;
    color: #444;
    padding-left: 1.2em;
    margin-bottom: 0.5em;
}
.noti-changes li {
    margin-bottom: 2px;
}
</style>