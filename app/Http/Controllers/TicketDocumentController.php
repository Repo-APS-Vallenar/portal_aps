<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TicketDocumentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'document' => 'required|file|max:10240', // Máximo 10MB
            'description' => 'nullable|string|max:255'
        ]);

        $file = $request->file('document');
        $path = $file->store('ticket-documents/' . $ticket->id);

        $document = TicketDocument::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'document' => $document,
                'message' => 'Documento subido exitosamente'
            ]);
        }

        return redirect()->back()->with('success', 'Documento subido exitosamente');
    }

    public function destroy(TicketDocument $document)
    {
        $this->authorize('delete', $document->ticket);

        Storage::delete($document->file_path);
        $document->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Documento eliminado exitosamente'
            ]);
        }

        return redirect()->back()->with('success', 'Documento eliminado exitosamente');
    }

    public function download(TicketDocument $document)
    {
        $this->authorize('view', $document->ticket);

        return Storage::download(
            $document->file_path,
            $document->file_name
        );
    }
} 