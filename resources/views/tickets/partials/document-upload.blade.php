<div class="document-upload-section mb-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Documentos Adjuntos</h5>
        </div>
        <div class="card-body">
            <form id="documentUploadForm" class="mb-3" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="document" class="form-label">Seleccionar Archivo</label>
                    <input type="file" class="form-control" id="document" name="document">
                    <div class="form-text">Tamaño máximo: 10MB. Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</div>
                </div>
                <div class="mb-3">
                    <label for="document_description" class="form-label">Descripción (opcional)</label>
                    <input type="text" class="form-control" id="document_description" name="description" placeholder="Breve descripción del documento">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Subir Documento
                </button>
            </form>

            <div id="documentsList" class="mt-3">
                @if(isset($ticket) && $ticket->documents->count() > 0)
                    <h6>Documentos Adjuntos:</h6>
                    <div class="list-group">
                        @foreach($ticket->documents as $document)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file me-2"></i>
                                    {{ $document->file_name }}
                                    @if($document->description)
                                        <small class="text-muted d-block">{{ $document->description }}</small>
                                    @endif
                                </div>
                                <div class="btn-group">
                                    <a href="{{ route('tickets.documents.download', $document) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin() || Auth::user()->isSuperadmin())
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-document"
                                                data-document-id="{{ $document->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('documentUploadForm');
    const documentsList = document.getElementById('documentsList');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const ticketId = '{{ $ticket->id ?? "" }}';
        
        fetch(`/tickets/${ticketId}/documents`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recargar la lista de documentos
                location.reload();
            } else {
                alert('Error al subir el documento');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al subir el documento');
        });
    });

    // Manejar eliminación de documentos
    document.querySelectorAll('.delete-document').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que deseas eliminar este documento?')) {
                const documentId = this.dataset.documentId;
                
                fetch(`/tickets/documents/${documentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.list-group-item').remove();
                    } else {
                        alert('Error al eliminar el documento');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el documento');
                });
            }
        });
    });
});
</script>
@endpush 