<script>
	var deleteModal = document.getElementById('deleteModal');
	deleteModal.addEventListener('show.bs.modal', function (event) {
		var button = event.relatedTarget;
		var url = button.getAttribute('data-url');
		
		var form = document.getElementById('formDelete');
		form.setAttribute('action', url);
	});  
</script>