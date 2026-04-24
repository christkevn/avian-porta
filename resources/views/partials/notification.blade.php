@if (Session::has('message'))
    <div class="alert alert-{{ Session::get('mode', 'success') }} alert-dismissible fade show">
        {{ Session::get('message') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
