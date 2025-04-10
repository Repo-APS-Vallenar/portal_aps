<?php
namespace App\Http\Controllers;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketCommentController extends Controller
{
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

        return redirect()->route('tickets.show', $comment->ticket_id)
                         ->with('success', 'Comentario actualizado correctamente.');
    }

    public function destroy(TicketComment $comment)
    {
        $this->authorizeAction($comment);
        $ticketId = $comment->ticket_id;
        $comment->delete();

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
