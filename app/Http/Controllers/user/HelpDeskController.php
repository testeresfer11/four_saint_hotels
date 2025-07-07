<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\{HelpDesk, QueryResponse, User, Contact};
use App\Models\CryptoSubscription;
use App\Notifications\NewMessageNotification;
use App\Notifications\NewTicketNotification;
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HelpDeskController extends Controller
{
    use SendResponseTrait;
    /**
     * functionName : add
     * createdDate  : 30-06-2024
     * purpose      : add the ticket
     */
    public function add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'             => 'required|string|max:255',
                // 'priority'          => 'required|in:Low,Medium,High',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            HelpDesk::Create([
                'user_id'           => authId(),
                'title'             => $request->title,
                'description'       => $request->description ? $request->description : '',
                'priority'          => $request->priority
            ]);

            User::find(getAdmimId())->notify(new NewTicketNotification(userNameById(authId()), $request->title));

            return $this->apiResponse('success', 200, 'Ticket ' . config('constants.SUCCESS.ADD_DONE'));
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }
    /**End Method Add */

    /**
     * functionName : list
     * createdDate  : 05-07-2024
     * purpose      : get the ticket listing
     */
    public function list()
    {
        try {
            $data = HelpDesk::where('user_id', authId())->get();

            return $this->apiResponse('success', 200, 'Ticket list ' . config('constants.SUCCESS.FETCH_DONE'), $data);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }
    /*end method list */



    /**
     * functionName : changeStatus
     * createdDate  : 30-07-2024
     * purpose      : Update the ticket status done mark as complete
     */
    public function changeStatus($id)
    {
        try {
            HelpDesk::where('id', $id)->update(['status' => 'Done']);

            return $this->apiResponse('success', 200, "Ticket status " . config('constants.SUCCESS.CHANGED_DONE'));
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }
    /**End method changeStatus**/

    /**
     * functionName : subscriptionTicket
     * createdDate  : 30-07-2024
     * purpose      : Update the ticket status done mark as complete
     */
    public function subscriptionTicket()
    {
        try {

            $response = HelpDesk::with('response')->where('type', 'subscription')->where('user_id', authId())->first();
            if (empty($response)) {
                $ticket = HelpDesk::Create([
                    'user_id'           => authId(),
                    'title'             => 'Purchase subscription',
                    'description'       => null,
                    'type'              => 'subscription',
                    'status'            => 'In Progress'
                ]);

                QueryResponse::create([
                    'help_id'   => $ticket->id,
                    'user_id'   => getAdmimId(),
                    'response'  => 'Hello ' . userNameById(authId()) . ', I am reaching out to offer my assistance with the payment process. If you encounter any difficulties or have any questions, please don’t hesitate to reach out, and I will be happy to guide you through the steps.',
                ]);

                User::find(getAdmimId())->notify(new NewTicketNotification(userNameById(authId()), $ticket->title));

                $response = HelpDesk::with('response')->where('type', 'subscription')->where('user_id', authId())->first();
            }


            return $this->apiResponse('success', 200, "Payment subscription ticket " . config('constants.SUCCESS.ADD_DONE'), $response->id);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }
    /**End method subscriptionTicket**/
}
