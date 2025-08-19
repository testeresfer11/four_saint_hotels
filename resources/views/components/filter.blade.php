@php
    $today = date('Y-m-d');
@endphp

<form id="filter">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <input type="text" class="form-control" placeholder="Search" 
                   name="search_keyword" 
                   value="{{ request()->search_keyword ?? '' }}">
        </div>

        <div class="col-md-3">
            <input type="date" class="form-control" 
                   name="start_date" 
                   value="{{ request()->start_date }}"
                   max="{{ $today }}">
        </div>

        <div class="col-md-3">
            <input type="date" class="form-control" 
                   name="end_date" 
                   value="{{ request()->end_date }}"
                   max="{{ $today }}">
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
            @if(request()->filled('search_keyword') || request()->filled('start_date') || request()->filled('end_date') || request()->filled('category_id'))
                <button class="btn btn-danger w-100" id="clear_filter">Clear</button>
            @endif
        </div>
    </div>
</form>


