<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\Hotel;

class CategoryController extends Controller
{



   public function getList(Request $request)
{
    $categories = ServiceCategory::query()
        ->when($request->filled('search_keyword'), function ($query) use ($request) {
            $query->where('title', 'like', '%' . $request->search_keyword . '%');
        })
        ->when($request->filled('start_date'), function ($query) use ($request) {
            $query->whereDate('created_at', '>=', $request->start_date);
        })
        ->when($request->filled('end_date'), function ($query) use ($request) {
            $query->whereDate('created_at', '<=', $request->end_date);
        })
        ->orderBy('id', 'DESC')
        ->paginate(10);

    return view('admin.category.list', compact('categories'));
}



    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'title' => 'required|string|max:255',
                'icon' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
                'description' => 'nullable|string',
            ]);

            $data = $request->only('hotel_id', 'title', 'description');

            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('uploads/icons', 'public');
                $data['icon'] = $iconPath;
            }

            ServiceCategory::create($data);
            return redirect()->route('admin.category.list')->with('success', 'Feature added successfully.');
        }

        $hotels = Hotel::all();
        return view('admin.category.add', compact('hotels'));
    }

    public function edit(Request $request, $id)
    {
        $category = ServiceCategory::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'title' => 'required|string|max:255',
                'icon' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
                'description' => 'nullable|string',
            ]);

            $category->hotel_id = $request->hotel_id;
            $category->title = $request->title;
            $category->description = $request->description;

            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('uploads/icons', 'public');
                $category->icon = $iconPath;
            }

            $category->save();
            return redirect()->route('admin.category.list')->with('success', 'Feature updated successfully.');
        }

        $hotels = Hotel::all();
        return view('admin.category.edit', compact('category', 'hotels'));
    }

   public function delete($id)
{
    $category = ServiceCategory::find($id);

    if (!$category) {
        return response()->json([
            'status' => 'error',
            'message' => 'Category not found'
        ], 404);
    }

    // Delete category
    $category->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Feature deleted successfully.',
        'count' => 0 // or you can return remaining count if needed
    ]);
}

}
