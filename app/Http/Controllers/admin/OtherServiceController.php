<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\OtherServiceCategory;
use App\Models\{Hotel,HotelRoom,HotelRoomOtherServiceCategory,HotelRoomType};
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
            'hotel_room_id' => 'required|array|min:1',
            'hotel_room_id.*' => 'exists:hotel_room_types,room_type_id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'total_quantity' => 'required|numeric',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'description' => 'nullable|string',
        ]);

        // Pick first room to fetch hotel_id (assuming all selected rooms belong to same hotel)
        $firstRoom = HotelRoomType::where('room_type_id', $request->hotel_room_id[0])->first();

        $category = new OtherServiceCategory();
        $category->name = $request->name;
        $category->price = $request->price;
        $category->total_quantity = $request->total_quantity;
        $category->description = $request->description;
        $category->hotel_id = $firstRoom->hotel_id;

        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('uploads/icons', 'public');
            $category->icon = $iconPath;
        }

        $category->save();

        // Save pivot relations for each selected room
        foreach ($request->hotel_room_id as $roomTypeId) {
            HotelRoomOtherServiceCategory::create([
                'hotel_room_id' => $roomTypeId,
                'other_service_category_id' => $category->id,
            ]);
        }

        return redirect()->route('admin.other_services.list')
                         ->with('success', 'Other service added successfully.');
    }

    $hotel_rooms = HotelRoomType::all();
    return view('admin.other_services.add', compact('hotel_rooms'));
}



public function edit(Request $request, $id)
{
    try {
        $category = OtherServiceCategory::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'hotel_room_id'   => 'required|array',
                'hotel_room_id.*' => 'exists:hotel_rooms,room_type_id',
                'name'            => 'required|string|max:255',
                'price'           => 'required|numeric|min:0',
                'total_quantity'  => 'required|integer|min:0',
                'description'     => 'nullable|string',
                'icon'            => 'nullable|image|max:2048'
            ]);

            // Use the first room type to set `hotel_id` and `hotel_room_type_id`
            $firstRoomType = HotelRoomType::where('room_type_id', $request->hotel_room_id[0])->first();

            if (!$firstRoomType) {
                return back()->withInput()->with('error', 'Invalid hotel room selected.');
            }

            // Update category
            $category->name = $request->name;
            $category->price = $request->price;
            $category->description = $request->description;
            $category->total_quantity = $request->total_quantity;
            $category->hotel_room_type_id = $request->hotel_room_id[0];
            $category->hotel_id = $firstRoomType->hotel_id;

            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('uploads/icons', 'public');
                $category->icon = $iconPath;
            }

            $category->save();

            // 🔁 Remove old links & add new ones in the pivot table
            HotelRoomOtherServiceCategory::where('other_service_category_id', $category->id)->delete();

            foreach ($request->hotel_room_id as $roomTypeId) {
                $room = HotelRoomType::where('room_type_id', $roomTypeId)->first();

                if ($room) {
                    HotelRoomOtherServiceCategory::create([
                        'other_service_category_id' => $category->id,
                        'hotel_room_id'             => $roomTypeId,
                    ]);
                }
            }

            return redirect()->route('admin.other_services.list')
                             ->with('success', 'Other service updated successfully.');
        }

        $hotel_rooms = HotelRoom::all();
        $selectedRoomIds = HotelRoomOtherServiceCategory::where('other_service_category_id', $category->id)
                                                        ->pluck('hotel_room_id')
                                                        ->toArray();

        return view('admin.other_services.edit', compact('category', 'hotel_rooms', 'selectedRoomIds'));
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
