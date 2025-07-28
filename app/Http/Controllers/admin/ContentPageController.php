<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{ContentPage, Contact};
use App\Models\ManagefAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContentPageController extends Controller
{
    /**
     * functionName : contentPage
     * createdDate  : 13-06-2024
     * purpose      : get and update the content page detail
     */
    public function contentPageDetail(Request $request, $slug)
    {
        try {
            if ($request->isMethod('get')) {
                $content_detail =  ContentPage::where('slug', $slug)->first();
                return view("admin.contentPage.update", compact('content_detail'));
            } elseif ($request->isMethod('post')) {
                $rules = [
                    'title'         => 'required|string|max:255',
                    'content'       => 'required',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }


                ContentPage::where('slug', $slug)->update([
                    'title'     => $request->title,
                    'content'     => $request->content,
                ]);

                return redirect()->back()->with('success', ucfirst(str_replace('-', ' ', $slug)) . ' ' . config('constants.SUCCESS.UPDATE_DONE'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method add**/

    /**
     * functionName : contentPage
     * createdDate  : 02-09-2024
     * purpose      : get content page detail for web
     */
    public function contentPage($slug)
    {
        try {

            if (in_array($slug, ['privacy-and-policy', 'about-us', 'terms-and-conditions', 'delete-account-steps'])) {
                $content_detail =  ContentPage::where('slug', $slug)->first();

                return view("admin.content-page", compact('content_detail'));
            } elseif ($slug == 'FAQ') {
                $content_detail = ManagefAQ::where('status', 1)->orderBy('id', 'desc')->get();

                return view("admin.content-page", compact('content_detail'));
            } else {
                return redirect()->back()->with("error", 'Not Found');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method contentPage**/


    public function storeContact(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string'
        ]);

        Contact::create($validated);

        return redirect()->back()->with('success', 'Thank you for reaching out. We will get back to you soon!');
    }


        public function getList(Request $request){
        try{
            $faq = ContentPage::when($request->filled('search_keyword'),function($query) use($request){
                $query->where(function($query) use($request){
                    $query->where('title','like',"%$request->search_keyword%")
                        ->orWhere('slug','like',"%$request->search_keyword%");
                });
            })
            ->when($request->filled('status'),function($query) use($request){
                $query->where('status',$request->status);
            })
            ->orderBy("id","desc")->paginate(10);
            return view("admin.contentPage.list",compact("faq"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}
