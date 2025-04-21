<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiftVoucher;
use Illuminate\Support\Str;
use App\Traits\SendResponseTrait;

class GiftVoucherController extends Controller
{
    use SendResponseTrait;

    /**
     * functionName : getList
     * createdDate  : 21-04-2025
     * purpose      :Fetch the voucher gift
     */
    public function getList(Request $request)
    {
        $vouchers = GiftVoucher::when($request->filled('search_keyword'), function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('title', 'like', "%$request->search_keyword%")
                    ->orWhere('voucher_code', 'like', "%$request->search_keyword%");
            });
        })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy("id", "desc")->paginate(10);
        return view('admin.voucher.list', compact('vouchers'));
    }

    /** End Method getList **/

    /**
     * functionName : add
     * createdDate  : 21-04-2025
     * purpose      : Add Gift voucher
     */
    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:1',
                'expiry_date' => 'required|date|after:today',
                'description' => 'nullable|string',
            ]);

            GiftVoucher::create([
                'title' => $request->title,
                'voucher_code' => strtoupper(Str::random(10)),
                'amount' => $request->amount,
                'expiry_date' => $request->expiry_date,
                'description' => $request->description,
                'created_by_user_id' => auth()->id(),
            ]);

            return redirect()->route('admin.vouchers.list')->with('success', 'Voucher created successfully.');
        }

        return view('admin.voucher.add');
    }

    /** End Method add **/

    /**
     * functionName : edit
     * createdDate  : 21-04-2025
     * purpose      : edit Gift voucher
     */
    public function edit(Request $request, $id)
    {
        $voucher = GiftVoucher::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:1',
                'expiry_date' => 'required|date|after:today',
                'description' => 'nullable|string',
            ]);

            $voucher->update([
                'title' => $request->title,
                'amount' => $request->amount,
                'expiry_date' => $request->expiry_date,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.vouchers.list')->with('success', 'Voucher updated successfully.');
        }

        return view('admin.voucher.edit', compact('voucher'));
    }

    /** End Method edit **/

    /**
     * functionName : delete
     * createdDate  : 21-04-2025
     * purpose      : delete Gift voucher
     */
    public function delete($id)
    {
        $voucher = GiftVoucher::findOrFail($id);
        $voucher->delete();

        return response()->json(["status" => "success", "message" => "Voucher " . config('constants.SUCCESS.DELETE_DONE')], 200);
    }

    /** End Method delete **/
    /**
     * functionName : changeStatus
     * createdDate  : 21-04-2025
     * purpose      : change Status of Gift voucher
     */
    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:gift_vouchers,id',
            'status' => 'required',
        ]);

        $voucher = GiftVoucher::findOrFail($request->id);
        $voucher->status = $request->status;
        $voucher->save();

        return response()->json(["status" => "success", "message" => "Voucher status " . config('constants.SUCCESS.CHANGED_DONE')], 200);
    }

    /** End Method changeStatus **/
}
