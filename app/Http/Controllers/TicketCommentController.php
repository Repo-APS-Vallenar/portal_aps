<?php
namespace App\Http\Controllers;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
class TicketCommentController extends Controller
{
    function logAudit($action, $description)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
    public function edit(TicketComment $comment)
    {
        $this->authorizeAction($comment);
        return view('comments.edit', compact('comment'));
    }

    public function update(Request $request, TicketComment $comment)
    {
        $this->authorizeAction($comment);

        $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'nullable|boolean',
        ]);

        $comment->update([
            'comment' => $request->comment,
            'is_internal' => $request->has('is_internal'),
        ]);
        $this->logAudit('Editar Comentario', 'Comentario actualizado por: ' . Auth()->user()->name);
        return redirect()->route('tickets.show', $comment->ticket_id)
                         ->with('success', 'Comentario actualizado correctamente.');
    }

    public function destroy(TicketComment $comment)
    {
        $this->authorizeAction($comment);
        $ticketId = $comment->ticket_id;
        $comment->delete();
        $this->logAudit('Eliminar Comentario', 'Comentario eliminado por: ' . Auth()->user()->name);
        return redirect()->route('tickets.show', $ticketId)
                         ->with('success', 'Comentario eliminado correctamente.');
    }

    private function authorizeAction(TicketComment $comment)
    {
        if (Auth::id() !== $comment->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
    }
}
