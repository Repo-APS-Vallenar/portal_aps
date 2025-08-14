<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SecurityLogger
{
    /**
     * Log de eventos de seguridad críticos
     */
    public static function logSecurityEvent(string $event, string $description, array $context = [])
    {
        $user = Auth::user();
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();
        
        // Log en archivo específico de seguridad
        Log::channel('security')->warning("SECURITY_EVENT: {$event}", [
            'description' => $description,
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'user_role' => $user?->role,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'timestamp' => now()->toISOString(),
            'context' => $context
        ]);

        // También en auditoría de BD
        AuditLog::create([
            'user_id' => $user?->id,
            'action' => "SECURITY: {$event}",
            'description' => $description,
            'ip_address' => $ipAddress,
            'model' => 'Security',
            'record_id' => null,
            'data' => json_encode(array_merge($context, [
                'user_agent' => $userAgent,
                'timestamp' => now()->toISOString()
            ]))
        ]);
    }

    /**
     * Log de intentos de acceso no autorizado
     */
    public static function logUnauthorizedAccess(string $resource, string $action = 'access', array $context = [])
    {
        self::logSecurityEvent(
            'UNAUTHORIZED_ACCESS',
            "Intento de acceso no autorizado a {$resource} con acción {$action}",
            array_merge($context, [
                'resource' => $resource,
                'action' => $action,
                'url' => request()->fullUrl(),
                'method' => request()->method()
            ])
        );
    }

    /**
     * Log de cambios de privilegios
     */
    public static function logPrivilegeChange(string $targetUser, string $oldRole, string $newRole)
    {
        self::logSecurityEvent(
            'PRIVILEGE_CHANGE',
            "Cambio de rol de usuario {$targetUser}: {$oldRole} -> {$newRole}",
            [
                'target_user' => $targetUser,
                'old_role' => $oldRole,
                'new_role' => $newRole
            ]
        );
    }

    /**
     * Log de múltiples intentos fallidos
     */
    public static function logMultipleFailedAttempts(string $action, int $attempts)
    {
        self::logSecurityEvent(
            'MULTIPLE_FAILED_ATTEMPTS',
            "Múltiples intentos fallidos de {$action}: {$attempts} intentos",
            [
                'action' => $action,
                'attempts' => $attempts
            ]
        );
    }

    /**
     * Log de uploads de archivos
     */
    public static function logFileUpload(string $filename, string $mimeType, int $size, string $context = '')
    {
        self::logSecurityEvent(
            'FILE_UPLOAD',
            "Archivo subido: {$filename} ({$mimeType}, {$size} bytes) - {$context}",
            [
                'filename' => $filename,
                'mime_type' => $mimeType,
                'size' => $size,
                'context' => $context
            ]
        );
    }

    /**
     * Log de cambios de datos sensibles
     */
    public static function logSensitiveDataChange(string $model, $recordId, array $changes)
    {
        self::logSecurityEvent(
            'SENSITIVE_DATA_CHANGE',
            "Cambio en datos sensibles de {$model} ID:{$recordId}",
            [
                'model' => $model,
                'record_id' => $recordId,
                'changes' => $changes
            ]
        );
    }
}
