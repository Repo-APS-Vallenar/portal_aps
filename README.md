Portal APS - Sistema de Gestión de Plataformas y Tickets para los usuarios de APS


🚀 Sobre el Proyecto
Portal APS es una aplicación web que centraliza el acceso a todas las plataformas utilizadas por los usuarios de la institución. 
Ofrece una interfaz moderna y amigable para navegar entre diferentes sistemas, con un buscador integrado y un sistema de tickets para gestionar solicitudes de soporte.


✨ # Características Principales

### Sistema de Tickets
- Gestión completa de tickets con estados personalizables
- Prioridades: baja, media, alta, urgente
- Seguimiento de cambios con bitácora detallada
- Notificaciones en tiempo real para usuarios y administradores
- Comentarios públicos e internos (solo visibles para staff)

### Seguridad
- Autenticación con bloqueo automático tras 3 intentos fallidos
- Roles: superadmin, admin, user
- Validación de contraseñas robusta (8+ caracteres, mayúscula, minúscula, número, símbolo)
- Bitácora de auditoría para acciones sensibles
- Protección contra edición de tickets cerrados

### Características Técnicas Destacadas

#### Gestión de Estados
```php
// Estados personalizables con colores
$statuses = TicketStatus::where('is_active', true)
    ->orderBy('name')
    ->select('id', 'name', 'color')
    ->distinct()
    ->get();
```

#### Sistema de Notificaciones
- Notificaciones bidireccionales (usuario → admin, admin → usuario)
- Diferentes tipos de notificaciones (nuevo ticket, actualización, comentarios)
- Notificaciones internas para el staff
- Cierre automático de alertas tras 5 segundos

#### Bitácora de Auditoría
- Registro detallado de cambios en tickets
- Trazabilidad de acciones de usuarios
- Registro de IP y timestamp
- Exportación a PDF y Excel

#### Exportación de Datos
- Exportación selectiva de tickets
- Reportes en PDF y Excel
- Filtros personalizados
- Formato consistente en exportaciones

### Flujos de Trabajo

#### Creación de Ticket
1. Usuario crea ticket con estado "Solicitado"
2. Notificación automática a admins
3. Asignación de prioridad y categoría
4. Registro en bitácora

#### Actualización de Ticket
1. Validación de permisos
2. Registro de cambios en bitácora
3. Notificación al usuario si hay cambios relevantes
4. Actualización de estado y comentarios

#### Gestión de Usuarios
- Creación con validación de email único
- Asignación de roles por superadmin
- Bloqueo/desbloqueo manual
- Historial de cambios en perfil

### Consideraciones Técnicas

#### Base de Datos
- Uso de soft deletes para tickets y comentarios
- Índices optimizados para búsquedas
- Relaciones bien definidas entre modelos

#### Frontend
- Diseño responsivo con Bootstrap
- Modales para acciones rápidas
- Validación en tiempo real
- Feedback visual inmediato

#### Seguridad
- Protección CSRF en todas las rutas
- Validación de datos en servidor
- Sanitización de inputs
- Control de acceso basado en roles

### Comandos Útiles
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

### Notas de Desarrollo
- Sistema desarrollado con Laravel 10
- Uso de Bootstrap 5 para el frontend
- Implementación de colas para notificaciones
- Integración con servicios de correo


🎯 Próximas Implementaciones
Sistema de Tickets Mejorado:

Estados dinámicos (Solicitado, Pendiente, En Proceso, Resuelto, Cerrado, Cancelado)

Prioridades configurables

Notificaciones por email

Historial de cambios

Adjuntar archivos a los tickets

Creacion de cards para la gestion de plataformas



💻 Tecnologías

PHP

Laravel

Bootstrap

Font Awesome

CSS

PostgreSQL

👥 Equipo
<<<<<<< HEAD
Jorge Trigo Barrera

Desarrollado por el equipo de TI de APS para mejorar la experiencia de los usuarios internos.

(Versión inicial del Portal APS)
=======

Desarrollado por el equipo de TI de APS para mejorar la experiencia de los usuarios internos.
=======

