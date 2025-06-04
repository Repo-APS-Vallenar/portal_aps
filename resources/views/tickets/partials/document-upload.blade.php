<div class="document-upload-section mb-4 mt-4">
    <div class="card" style="box-shadow: 0 2px 8px rgba(1,163,213,0.07); border-radius: 16px; border: 1px solid #e3e8ee; background: #fafdff;">
        <div class="card-header" style="padding-bottom: 0.5rem; background: none; border-bottom: 1px solid #e3e8ee;">
            <h5 class="mb-0" style="font-size: 1.18rem; font-weight: 600;">Adjuntar Documentos</h5>
        </div>
        <div class="card-body pt-3 pb-2">
            <form id="documentUploadForm" class="mb-0 d-flex flex-wrap align-items-center gap-2 gap-md-3" enctype="multipart/form-data" method="POST" action="{{ route('tickets.documents.store', $ticket) }}">
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
                        <button type="submit" class="btn btn-primary btn-sm d-none d-md-flex align-items-center justify-content-center upload-btn-minimal ms-2" id="btnUploadDocument" style="height: 38px; width: 40px; border-radius: 10px; flex-shrink: 0; margin-bottom: 0.2rem;">
                            <i class="fas fa-upload"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm d-flex d-md-none align-items-center justify-content-center upload-btn-minimal mt-2" id="btnUploadDocumentMobile" style="height: 38px; width: 100%; border-radius: 10px;">
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

<!-- Barra de progreso de carga -->
<div id="uploadProgressBarContainer" style="display:none; width:100%; margin-bottom: 10px;">
    <div class="progress" style="height: 18px;">
        <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%; font-weight:600; font-size:1em;">0%</div>
    </div>
</div>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('documentUploadForm');
    const progressBarContainer = document.getElementById('uploadProgressBarContainer');
    const progressBar = document.getElementById('uploadProgressBar');
    const btnUpload = document.getElementById('btnUploadDocument');
    const btnUploadMobile = document.getElementById('btnUploadDocumentMobile');
    if (form && progressBarContainer && progressBar) {
        // Mostrar barra de progreso al hacer submit
        form.addEventListener('submit', function(e) {
            progressBarContainer.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.innerText = '0%';
            // Simular progreso mientras se sube (ya que es submit clásico)
            let percent = 0;
            const interval = setInterval(() => {
                if (percent < 95) {
                    percent += 5;
                    progressBar.style.width = percent + '%';
                    progressBar.innerText = percent + '%';
                } else {
                    clearInterval(interval);
                }
            }, 100);
        });
        // Ocultar barra de progreso cuando la sección de documentos se actualiza en vivo
        window.updateDocumentsSection = (function(orig) {
            return function() {
                if (progressBarContainer) {
                    progressBarContainer.style.display = 'none';
                }
                if (orig) orig.apply(this, arguments);
            };
        })(window.updateDocumentsSection);
    }
});
</script> 