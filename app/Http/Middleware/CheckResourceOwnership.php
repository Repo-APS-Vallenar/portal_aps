<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\SecurityLogger;

class CheckResourceOwnership
{
    /**
     * Handle an incoming request.
     * Verifica que el usuario tenga permisos para acceder al recurso
     */
    public function handle(Request $request, Closure $next, string $resource): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            SecurityLogger::logUnauthorizedAccess($resource, 'access_unauthenticated');
            abort(401, 'No autenticado');
        }

        // Superadmin y admin tienen acceso completo
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return $next($request);
        }

        $resourceId = $request->route($resource);
        
        switch ($resource) {
            case 'ticket':
                $ticket = \App\Models\Ticket::findOrFail($resourceId);
                if ($ticket->created_by !== $user->id && $ticket->assigned_to !== $user->id) {
                    SecurityLogger::logUnauthorizedAccess(
                        "ticket:{$resourceId}",
                        $request->method(),
                        ['ticket_creator' => $ticket->created_by, 'ticket_assigned' => $ticket->assigned_to]
                    );
                    abort(403, 'No tienes permisos para acceder a este ticket');
                }
                break;
                
            case 'user':
                // Los usuarios solo pueden modificar su propio perfil
                if ($resourceId != $user->id) {
                    SecurityLogger::logUnauthorizedAccess(
                        "user:{$resourceId}",
                        $request->method(),
                        ['attempted_user_id' => $resourceId, 'current_user_id' => $user->id]
                    );
                    abort(403, 'Solo puedes modificar tu propio perfil');
                }
                break;
                
            case 'notification':
                $notification = \App\Models\User::find($user->id)
                    ->notifications()
                    ->where('id', $resourceId)
                    ->first();
                    
                if (!$notification) {
                    abort(403, 'Esta notificación no te pertenece');
                }
                break;
                
            case 'comment':
                $comment = \App\Models\TicketComment::findOrFail($resourceId);
                if ($comment->user_id !== $user->id) {
                    abort(403, 'Solo puedes modificar tus propios comentarios');
                }
                break;
                
            case 'document':
                $document = \App\Models\TicketDocument::findOrFail($resourceId);
                $ticket = $document->ticket;
                if ($ticket->created_by !== $user->id && 
                    $ticket->assigned_to !== $user->id && 
                    $document->user_id !== $user->id) {
                    abort(403, 'No tienes permisos para acceder a este documento');
                }
                break;
                
            default:
                abort(500, 'Recurso no reconocido para verificación de permisos');
        }

        return $next($request);
    }
}
