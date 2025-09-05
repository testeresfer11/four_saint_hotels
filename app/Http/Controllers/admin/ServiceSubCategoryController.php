<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceSubCategory;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



class ServiceSubCategoryController extends Controller
{
    // List all subcategories
public function getList(Request $request)
{
    $subCategories = ServiceSubCategory::with('category')
        ->when($request->filled('search_keyword'), function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search_keyword . '%');
        })
        ->when($request->filled('start_date'), function ($query) use ($request) {
            $query->whereDate('created_at', '>=', $request->start_date);
        })
        ->when($request->filled('end_date'), function ($query) use ($request) {
            $query->whereDate('created_at', '<=', $request->end_date);
        })
        ->orderBy('id', 'DESC')
        ->paginate(15);

    return view('admin.sub_category.list', compact('subCategories'));
}


    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                
                $validated = $request->validate([
                    'category_id' => 'required|exists:service_categories,id',
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'image' => 'nullable|image|max:2048',
                ]);


                // Handle image upload if present
                if ($request->hasFile('image')) {
                    $path = $request->file('image')->store('service_sub_categories', 'public');
                    $validated['image'] = $path;
                }



                // Create the new service sub-category
                ServiceSubCategory::create($validated);

                return redirect()->route('admin.sub_category.list')
                    ->with('success', 'Sub-feature added successfully!');
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Error adding sub-feature: ' . $e->getMessage());

                // Redirect back with an error message
                return back()->withInput()
                    ->with('error', 'An error occurred while adding the sub-feature. Please try again.');
            }
        }

        // For GET requests, retrieve all categories to populate the dropdown
        $categories = ServiceCategory::all();

        return view('admin.sub_category.add', compact('categories'));
    }


    public function edit(Request $request, $id)
    {
        $subCategory = ServiceSubCategory::findOrFail($id);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'category_id' => 'required|exists:service_categories,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048',
                'status' => 'sometimes|boolean',
            ]);

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($subCategory->image && Storage::disk('public')->exists($subCategory->image)) {
                    Storage::disk('public')->delete($subCategory->image);
                }
                $path = $request->file('image')->store('service_sub_categories', 'public');
                $validated['image'] = $path;
            }

            $validated['status'] = $request->has('status');

            $subCategory->update($validated);

            return redirect()->route('admin.sub_category.list')->with('success', 'Sub-feature updated successfully!');
        }

        $categories = ServiceCategory::all();

        return view('admin.sub_category.edit', compact('subCategory', 'categories'));
    }

    public function delete($id){
        try {
            $subCategory = ServiceSubCategory::findOrFail($id);

            if ($subCategory->image && Storage::disk('public')->exists($subCategory->image)) {
                Storage::disk('public')->delete($subCategory->image);
            }

            $subCategory->delete();

            // If it's an AJAX request, return JSON
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sub-feature deleted successfully!'
                ]);
            }

            // Otherwise, redirect (normal delete)
            return redirect()->route('admin.sub_category.list')
                ->with('success', 'Sub-feature deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting sub-feature: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while deleting the sub-feature. Please try again.'
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'An error occurred while deleting the sub-feature. Please try again.');
        }
    }

}
