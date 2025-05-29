<div class="document-upload-section mb-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Adjuntar Documentos</h5>
        </div>
        <div class="card-body">
            <form id="documentUploadForm" class="mb-3" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="document" class="form-label">Seleccionar Archivo</label>
                    <input type="file" class="form-control" id="document" name="document" accept="image/*">
                    <div class="form-text">Tamaño máximo: 20MB. Formatos permitidos:JPG, PNG</div>
                </div>
                <div class="mb-3">
                    <label for="document_description" class="form-label">Descripción (opcional)</label>
                    <input type="text" class="form-control" id="document_description" name="description" placeholder="Breve descripción del documento">
                </div>
                <button type="submit" class="btn btn-info w-100" id="btnUploadDocument" disabled>
                    <i class="fas fa-upload me-1"></i> Subir Documento
                </button>
            </form>
                                </div>
                                </div>
                            </div>

<!-- Toast de error para subida de documentos -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
    <div id="documentUploadErrorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="documentUploadErrorToastBody">
                <!-- Mensaje de error -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('documentUploadForm');
    const fileInput = document.getElementById('document');
    const btnUpload = document.getElementById('btnUploadDocument');
    const progressBarContainer = document.createElement('div');
    progressBarContainer.className = 'progress mb-2';
    progressBarContainer.style.display = 'none';
    const progressBar = document.createElement('div');
    progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated';
    progressBar.role = 'progressbar';
    progressBar.ariaValuenow = 0;
    progressBar.ariaValuemin = 0;
    progressBar.ariaValuemax = 100;
    progressBar.style.width = '0%';
    progressBar.innerText = '';
    progressBarContainer.appendChild(progressBar);
    form.insertBefore(progressBarContainer, btnUpload);

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const ticketId = '{{ $ticket->id ?? "" }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrfToken);
        btnUpload.disabled = true;
        progressBarContainer.style.display = 'block';
        progressBar.style.width = '0%';
        progressBar.innerText = '';
        axios.post(`/tickets/${ticketId}/documents`, formData, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'multipart/form-data'
            },
            withCredentials: true,
            onUploadProgress: function(progressEvent) {
                if (progressEvent.lengthComputable) {
                    let percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    progressBar.style.width = percent + '%';
                    progressBar.innerText = percent + '%';
                }
            }
        })
        .then(response => {
            if (response.data && response.data.success) {
                // Limpiar formulario
                form.reset();
                fileInput.value = '';
                btnUpload.disabled = true;
                fileInput.dispatchEvent(new Event('change'));
                progressBar.style.width = '100%';
                progressBar.innerText = '100%';
                setTimeout(() => {
                    progressBarContainer.style.display = 'none';
                    progressBar.style.width = '0%';
                    progressBar.innerText = '';
                }, 800);
                // Actualizar la lista de documentos adjuntos dinámicamente
                updateDocumentsList();
                showGlobalToast('Documento subido exitosamente', 'success');
            } else if (response.data && response.data.message) {
                showGlobalToast(response.data.message, 'error');
            } else {
                showGlobalToast('Error al subir la imagen.', 'error');
            }
        })
        .catch(error => {
            if (error.response && error.response.data && error.response.data.message) {
                showGlobalToast(error.response.data.message, 'error');
            } else {
                showGlobalToast('Error inesperado al subir la imagen.', 'error');
            }
        })
        .finally(() => {
            btnUpload.disabled = !fileInput.files.length;
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

    fileInput.addEventListener('change', function() {
        btnUpload.disabled = !fileInput.files.length;
    });

    // Función para actualizar la lista de documentos adjuntos
    function updateDocumentsList() {
        fetch(window.location.pathname, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newDocs = doc.querySelector('.card-body .row.g-3');
            const currentDocs = document.querySelector('.card-body .row.g-3');
            if (newDocs && currentDocs) {
                currentDocs.innerHTML = newDocs.innerHTML;
                // Reasignar eventos a miniaturas
                assignImageModalEvents();
            }
        });
    }

    // Función para asignar el evento de click a las miniaturas
    function assignImageModalEvents() {
        document.querySelectorAll('.doc-img-thumb').forEach(function(img) {
            img.addEventListener('click', function() {
                const modalImg = document.getElementById('modalImage');
                modalImg.src = this.getAttribute('data-img-src');
            });
        });
    }

    // Asignar eventos al cargar la página
    assignImageModalEvents();
});

// Toast de error para subida de documentos
function showDocumentUploadErrorToast(message) {
    const toastBody = document.getElementById('documentUploadErrorToastBody');
    toastBody.innerHTML = message;
    const toastEl = document.getElementById('documentUploadErrorToast');
    const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
    toast.show();
}
</script>
@endpush 