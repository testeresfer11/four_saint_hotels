<form id="filter">
    <div class="row align-items-center justify-content-end">
        <div class="col-6 d-flex gap-2">
            <input type="text" class="form-control"  placeholder="Search" name="search_keyword" value="{{request()->filled('search_keyword') ? request()->search_keyword : ''}}">            
        </div>
        <div class="col-3">
            <select class="form-control" name="status" style="width:100%">
                <option value="">All</option>
                <option value="1" {{(request()->filled('status') && request()->status == "1")? 'selected' : ''}}>Active</option>
                <option value="0" {{(request()->filled('status') && request()->status == "0")? 'selected' : ''}}>In Active</option>
            </select>
        </div>
        <div class="col-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->filled('search_keyword') || request()->filled('status') || request()->filled('category_id'))
                <button class="btn btn-danger" id="clear_filter">Clear Filter</button>
            @endif
        </div>
    </div>
</form>
