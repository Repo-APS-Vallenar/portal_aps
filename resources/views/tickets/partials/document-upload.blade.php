<div class="document-upload-section mb-4 mt-4">
    <div class="card" style="box-shadow: 0 2px 8px rgba(1,163,213,0.07); border-radius: 16px; border: 1px solid #e3e8ee; background: #fafdff;">
        <div class="card-header" style="padding-bottom: 0.5rem; background: none; border-bottom: 1px solid #e3e8ee;">
            <h5 class="mb-0" style="font-size: 1.18rem; font-weight: 600;">Adjuntar Documentos</h5>
        </div>
        <div class="card-body pt-3 pb-2">
            <form id="documentUploadForm" class="mb-0 d-flex flex-wrap align-items-center gap-2 gap-md-3" enctype="multipart/form-data">
                @csrf
                <div class="d-flex flex-column" style="min-width: 180px;">
                    <label for="document" class="form-label mb-1 small text-muted">Archivo</label>
                    <input type="file" class="form-control form-control-sm" id="document" name="document" accept="image/*" style="min-width: 180px;">
                    <div class="form-text small" style="font-size: 0.92em;">Máx: 20MB. JPG, PNG</div>
                </div>
                <div class="flex-grow-1 d-flex flex-column flex-md-row align-items-md-end" style="min-width: 140px;">
                    <div class="w-100 d-flex flex-row align-items-end" style="gap: 0.5rem;">
                        <div class="flex-grow-1">
                            <label for="document_description" class="form-label mb-1 small text-muted">Descripción (Opcional)</label>
                            <input type="text" class="form-control form-control-sm" id="document_description" name="description" placeholder="Breve descripción" style="width:100%; min-width: 120px;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm d-none d-md-flex align-items-center justify-content-center upload-btn-minimal ms-2" id="btnUploadDocument" disabled style="height: 38px; width: 40px; border-radius: 10px; flex-shrink: 0; margin-bottom: 0.2rem;">
                            <i class="fas fa-upload"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm d-flex d-md-none align-items-center justify-content-center upload-btn-minimal mt-2" id="btnUploadDocumentMobile" disabled style="height: 38px; width: 100%; border-radius: 10px;">
                        <i class="fas fa-upload"></i> <span class="ms-1">Subir</span>
                    </button>
                </div>
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
    const btnUploadMobile = document.getElementById('btnUploadDocumentMobile');
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
        btnUploadMobile.disabled = true;
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
                btnUploadMobile.disabled = true;
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
            btnUploadMobile.disabled = !fileInput.files.length;
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
        if (btnUpload) btnUpload.disabled = !fileInput.files.length;
        if (btnUploadMobile) btnUploadMobile.disabled = !fileInput.files.length;
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

<style>
.upload-btn-minimal {
    background: #2196f3;
    border: none;
    color: #fff;
    font-weight: 500;
    box-shadow: 0 2px 8px rgba(33,150,243,0.07);
    border-radius: 10px;
    transition: background 0.18s, color 0.18s;
    font-size: 1.15em;
    padding: 0;
}
.upload-btn-minimal:hover, .upload-btn-minimal:focus {
    background: #1769aa;
    color: #fff;
}
#documentUploadForm .form-control-sm {
    font-size: 1em;
    padding: 0.45rem 0.7rem;
}
#documentUploadForm .form-label {
    font-size: 0.98em;
    font-weight: 500;
    color: #888;
}
@media (max-width: 767.98px) {
    #documentUploadForm {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.7rem !important;
    }
    .upload-btn-minimal {
        width: 100% !important;
        min-width: 0 !important;
        border-radius: 8px !important;
        justify-content: center !important;
    }
    #btnUploadDocument span {
        display: inline !important;
    }
}
</style> 