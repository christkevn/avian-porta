<script>
    $(document).on('click', '.btn-preview-image', function() {
        const id = $(this).data('id');

        $('#previewImg1, #previewImg2').hide().attr('src', '');
        $('#previewImg1Empty, #previewImg2Empty').text('Tidak ada gambar').show();
        $('#previewImageModal').modal('show');

        $.getJSON(`{{ url('admin/data-noo') }}/${id}/images`)
            .done(function(res) {
                function setPreview(imgEl, emptyEl, src) {
                    if (src) {
                        imgEl.attr('src', src).show();
                        emptyEl.hide();
                    } else {
                        imgEl.hide();
                        emptyEl.show();
                    }
                }

                setPreview($('#previewImg1'), $('#previewImg1Empty'), res.img1);
                setPreview($('#previewImg2'), $('#previewImg2Empty'), res.img2);
            })
            .fail(function() {
                $('#previewImg1Empty, #previewImg2Empty')
                    .text('Gagal memuat gambar')
                    .show();
            });
    });
</script>
