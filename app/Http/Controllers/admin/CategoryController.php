<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\Hotel;

class CategoryController extends Controller
{



    public function getList()
    {
        $categories = ServiceCategory::orderBy('id', 'DESC')->paginate(10);
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
            return redirect()->route('admin.category.list')->with('success', 'Category added successfully.');
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
            return redirect()->route('admin.category.list')->with('success', 'Category updated successfully.');
        }

        $hotels = Hotel::all();
        return view('admin.category.edit', compact('category', 'hotels'));
    }

    public function delete($id)
    {
        $category = ServiceCategory::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.category.list')->with('success', 'Category deleted successfully.');
    }
}
