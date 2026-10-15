<form method="GET" action="{{ url()->current() }}" class="row g-2 p-2">
    <input type="hidden" name="filter" value="1">
    <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="Cari Nama" value="{{ $filter['name'] ?? '' }}">
    </div>
    <div class="col-md-3">
        <select name="level" class="form-select">
            <option value="">Semua Level</option>
            @foreach ($levels as $lv)
                <option value="{{ $lv }}" @selected(($filter['level'] ?? '') === $lv)>{{ $lv }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-50">Terapkan Filter</button>
        <a href="{{ url()->current() }}?clear=1" class="btn btn-outline-secondary w-50">Clear</a>
    </div>
</form>
