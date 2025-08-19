<form id="filter">
    <div class="d-flex align-items-center  g-2 align-items-end" style="column-gap: 10px;">
        {{-- <div class="col-md-3"> --}}
            <input type="text" class="form-control w-100" placeholder="Search" name="search_keyword" value="{{ request()->search_keyword ?? '' }}">
        {{-- </div> --}}

        {{-- <div class="col-md-3"> --}}
            <input type="date" class="form-control" name="start_date" value="{{ request()->start_date }}">
        {{-- </div> --}}

        {{-- <div class="col-md-3"> --}}
            <input type="date" class="form-control" name="end_date" value="{{ request()->end_date }}">
        {{-- </div> --}}

        {{-- <div class="col-md-3 d-flex gap-2"> --}}
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->filled('search_keyword') || request()->filled('start_date') || request()->filled('end_date') || request()->filled('category_id'))
                <button class="btn btn-danger" id="clear_filter">Clear</button>
            @endif
        {{-- </div> --}}
    </div>
</form>

