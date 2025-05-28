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
            'document' => 'required|image|max:15240', // Solo imágenes, máximo 10MB
            'description' => 'nullable|string|max:255'
        ]);

        $file = $request->file('document');
        $path = $file->store('ticket-documents/' . $ticket->id, 'public');

        $document = TicketDocument::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description
        ]);

        if ($request->expectsJson()) {
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

        Storage::disk('public')->delete($document->file_path);
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

        $path = $document->file_path;
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return $disk->download($path, $document->file_name);
    }
} 