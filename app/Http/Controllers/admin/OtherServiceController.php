<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\OtherServiceCategory;
use App\Models\{Hotel,HotelRoom,HotelRoomOtherServiceCategory};
use Illuminate\Http\Request;

class OtherServiceController extends Controller
{
    
   public function getList(Request $request){

    $categories = OtherServiceCategory::query()
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
        ->paginate(10);

    return view('admin.other_services.list', compact('categories'));
}



   public function add(Request $request)
{
    if ($request->isMethod('post')) {
        $request->validate([
            'hotel_room_id' => 'required|exists:hotel_rooms,room_id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'description' => 'nullable|string',
        ]);

        $category = new OtherServiceCategory();
        $category->name = $request->name;
        $category->price = $request->price;
        $category->description = $request->description;

        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('uploads/icons', 'public');
            $category->icon = $iconPath;
        }

        $category->save();

        // Save relation in pivot table
        HotelRoomOtherServiceCategory::create([
            'hotel_room_id' => $request->hotel_room_id,
            'other_service_category_id' => $category->id,
        ]);

        return redirect()->route('admin.other_services.list')
                         ->with('success', 'Other service added successfully.');
    }

    $hotel_rooms = HotelRoom::all();
    return view('admin.other_services.add', compact('hotel_rooms'));
}


public function edit(Request $request, $id)
{
    try {
        $category = OtherServiceCategory::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'hotel_room_id' => 'required|exists:hotel_rooms,room_id',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'description' => 'nullable|string',
            ]);

            $category->name = $request->name;
            $category->price = $request->price;
            $category->description = $request->description;

            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('uploads/icons', 'public');
                $category->icon = $iconPath;
            }

            $category->save();

            // Update pivot table
            HotelRoomOtherServiceCategory::updateOrCreate(
                ['other_service_category_id' => $category->id],
                ['hotel_room_id' => $request->hotel_room_id]
            );

            return redirect()->route('admin.other_services.list')
                             ->with('success', 'Other service updated successfully.');
        }

        $hotel_rooms = HotelRoom::all();
        return view('admin.other_services.edit', compact('category', 'hotel_rooms'));
    } catch (\Exception $e) {
        
        return redirect()->back()
                         ->withInput()
                         ->with('error', 'An error occurred: ' . $e->getMessage());
    }
}

    public function delete($id)
    {
        $category = OtherServiceCategory::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.other_services.list')->with('success', 'Other services deleted successfully.');
    }
}
