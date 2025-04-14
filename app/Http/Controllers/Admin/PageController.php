<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Models
use App\Models\Page;
use App\Models\PageContent;

class PageController extends AdminBaseController
{
    /**
     * Display a list of pages along with their associated content.
     *
     * This method fetches all pages along with their related PageContent and 
     * passes them to the 'admin.pages.index' view for rendering.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pages = Page::with(['pageContent'])->get();
        return view('admin.pages.index',compact('pages'));
    }

    /**
     * Show the form for editing the content of a specific page.
     *
     * This method fetches a page and its associated PageContent based on 
     * the provided slug, and passes the data to the 'admin.pages.edit' view.
     *
     * @param string $slug The unique slug identifying the page to be edited.
     * @return \Illuminate\View\View
     */
    public function editPageContent($slug)
    {
        $page = Page::with('PageContent')->where('slug',$slug)->first();
        return view('admin.pages.edit',compact('page'));
    }

    /**
     * Update the content of a page.
     *
     * This method validates the incoming request, updates or creates a 
     * PageContent entry based on the provided page ID, and saves the changes 
     * to the database. After the update, a success message is flashed to the 
     * session, and the user is redirected to the page list view.
     *
     * @param \Illuminate\Http\Request $request The request containing the updated page content.
     * @return \Illuminate\Http\RedirectResponse Redirects to the 'page-list' route after updating.
     */
    public function update(Request $request)
    {
        // Retrieve the page ID from the request
        $page_id = $request->page_id;

        // Validate incoming request data
        $request->validate([
            'content_title' => 'required|string|max:255',
            'page_slug' => 'nullable|string|unique:pages,slug,' . $page_id . '|max:255',
            'content' => 'required|string',
        ]);
        
        // Find the page by ID
        $page = Page::find($page_id);
    
        // Create or update the PageContent for the page
        $pageContent = PageContent::updateOrCreate(
            ['page_id' => $page->id],
            ['name' => $request->content_title, 'page_content' => $request->content]
        );
    
        // Flash a success message to the session
        $request->session()->flash('success', 'Page updated successfully');

        // Redirect to the page list
        return redirect()->route('page-list');
    }

}
