# Portal APS - Sistema de Gestión de Plataformas y Tickets


🚀 **Sobre el Proyecto**
Portal APS es una aplicación web moderna que centraliza el acceso a todas las plataformas utilizadas por los usuarios de la institución y gestiona solicitudes de soporte mediante un sistema de tickets robusto, seguro y en tiempo real.


✨ **Características Principales**

## Sistema de Tickets
- Gestión completa de tickets con estados personalizables (Solicitado, Pendiente, En Proceso, Resuelto, Cerrado, Cancelado)
- Prioridades: baja, media, alta, urgente
- Seguimiento de cambios con bitácora detallada
- Notificaciones en tiempo real para usuarios y administradores
- Comentarios públicos e internos (solo visibles para staff)
- Adjuntos solo de imágenes (JPG, PNG), con visualización de quién subió y cuándo
- Visualización clara y responsiva de los adjuntos

## Seguridad
- Autenticación con bloqueo automático tras 3 intentos fallidos
- Roles: superadmin, admin, user
- Validación de contraseñas robusta
- Bitácora de auditoría para acciones sensibles (incluye subida de archivos)
- Protección contra edición de tickets cerrados
- Control de acceso basado en roles y políticas

## Notificaciones
- Notificaciones automáticas para:
  - Nuevo ticket
  - Cambio de asignado
  - Cambio de prioridad
  - Reapertura de ticket
  - Cambio de categoría
  - Adjuntos (subida/eliminación de imágenes)
  - Comentarios (públicos e internos)
- Notificaciones en tiempo real vía Pusher y Laravel Echo
- Acceso directo desde la notificación al ticket correspondiente
- No se permite eliminar notificaciones individuales (solo limpiar todas)

## Auditoría
- Registro detallado de cambios en tickets y subida de archivos
- Trazabilidad de acciones de usuarios (incluye IP y timestamp)
- Exportación de logs a PDF y Excel

## UX/UI
- Diseño responsivo y optimizado para móvil y escritorio
- Visualización clara de información adicional y adjuntos
- Feedback visual inmediato (alertas de éxito/error)
- Interfaz moderna y amigable

## Exportación de Datos
- Exportación selectiva de tickets
- Reportes en PDF y Excel
- Filtros personalizados

## Gestión de Usuarios
- Creación con validación de email único
- Asignación de roles por superadmin
- Bloqueo/desbloqueo manual
- Historial de cambios en perfil

## Base de Datos
- Uso de soft deletes para tickets y comentarios
- Índices optimizados para búsquedas
- Relaciones bien definidas entre modelos

## Frontend
- Bootstrap 5
- Font Awesome
- Modales para acciones rápidas
- Validación en tiempo real

## Comandos Útiles
```bash
# Migraciones
php artisan migrate

# Seeders
php artisan db:seed --class=AdminUserSeeder

# Limpieza de caché
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Tecnologías
- PHP
- Laravel 10
- Bootstrap 5
- Font Awesome
- CSS
- PostgreSQL
- Pusher (Laravel Echo)

## Equipo
Desarrollado por el equipo de TI de APS para mejorar la experiencia de los usuarios internos.


## 📡 Tiempo Real y Broadcasting (Pusher)

Este sistema utiliza **Pusher** y Laravel Echo para funcionalidades en tiempo real, mejorando la colaboración y la experiencia de usuario.

### Canales privados utilizados
- `private-ticket.{ticketId}`: Canal privado para comentarios y adjuntos en tiempo real de cada ticket. Solo usuarios con acceso al ticket pueden suscribirse.
- `private-user.{userId}`: Canal privado para notificaciones personales en tiempo real.

### Eventos broadcast principales
- `.comment-added`: Se emite cuando se agrega un comentario a un ticket.
- `.comment-deleted`: Se emite cuando se elimina un comentario.
- `.document-added`: Se emite cuando se sube una imagen adjunta.
- `.document-deleted`: Se emite cuando se elimina una imagen adjunta.
- `.new-notification`: Se emite cuando un usuario recibe una notificación relevante.

### Flujo de comentarios y adjuntos en tiempo real
1. El usuario envía un comentario o sube una imagen.
2. El backend guarda el registro y emite el evento correspondiente por Pusher.
3. Todos los usuarios conectados al canal del ticket reciben el evento y actualizan la lista automáticamente.
4. Al eliminar un comentario o adjunto, se emite el evento correspondiente y se actualiza la lista en todas las ventanas.

### Flujo de notificaciones en tiempo real
1. Cuando ocurre una acción relevante (nuevo ticket, comentario, cambio de estado, adjunto, etc.), se crea una notificación y se emite el evento `.new-notification` al canal privado del usuario.
2. El frontend actualiza el badge y la lista de notificaciones sin recargar la página.

### Buenas prácticas y recomendaciones
- **Seguridad:** Los canales privados usan lógica de autorización en `routes/channels.php` para asegurar que solo usuarios autorizados reciban los eventos.
- **Optimización:** El polling (setInterval) para respaldo se ejecuta cada 5 minutos, pero la actualización principal es en tiempo real vía Pusher.
- **Feedback visual:** Los mensajes de éxito y error se muestran de forma clara y se eliminan automáticamente tras unos segundos.
- **Escalabilidad:** El sistema está preparado para usar colas (`QUEUE_CONNECTION=database` o Redis) para manejar eventos de broadcasting en producción.

---

