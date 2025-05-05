<?php


namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a list of all services.
     *
     * This method retrieves a paginated list of services from the database and displays them in the 
     * admin panel. You can add pagination and filters based on user input to customize the list of services.
     * The method also handles any potential errors that may occur while fetching the services.
     *
     * @param  \Illuminate\Http\Request  $request  The request object that can contain filter parameters.
     * @return \Illuminate\View\View  The view displaying the list of services.
     * @throws \Exception If the service list cannot be fetched.
     */
    public function getList(Request $request)
    {
        try {
            // Fetch the list of services, you can add pagination or filters if needed
            $services = Service::paginate(10);
            
            return view('admin.service.list', compact('services'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error fetching services: ' . $e->getMessage());
        }
    }

    /**
     * View a specific service.
     *
     * This method retrieves a specific service by its ID and displays the service details in the admin panel.
     * It handles any potential errors, such as when the service is not found, and redirects to the service list
     * with an error message if an issue occurs.
     *
     * @param  int  $id  The ID of the service to view.
     * @return \Illuminate\View\View  The view displaying the details of the specific service.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the service is not found.
     */
    public function view($id)
    {
        try {
            // Fetch the service by ID
            $service = Service::findOrFail($id);

            return view('admin.service.view', compact('service'));
        } catch (\Exception $e) {
            return redirect()->route('service.list')->with('error', 'Service not found: ' . $e->getMessage());
        }
    }
}


