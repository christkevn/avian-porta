<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ url('admin/data-noo/import') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Kolom yang diperlukan:
                        <strong>kode avid, id sfa, ImageTokoAvian, ImageTokoAvian1</strong>
                    </p>

                    <div class="mb-3">
                        <label class="form-label">File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                    </div>

                    <a href="{{ url('admin/data-noo/template') }}" class="text-decoration-none small">
                        <i class="ri ri-download-line"></i> Download Template Excel
                    </a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary w-200"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success w-200">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
