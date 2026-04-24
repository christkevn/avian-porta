<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Konfirmasi Hapus Data</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				Apakah Anda yakin ingin menghapus data ini?
			</div>
			<div class="modal-footer">
				<form id="formDelete" method="POST">
					@csrf
					@method('DELETE')
					<button type="submit" class="btn btn-danger">Hapus</button>
				</form>				
				<button type="button" class="btn btn-warning" data-bs-dismiss="modal">&nbsp;Batal&nbsp;</button>
			</div>
		</div>
	</div>
</div>