<?php

namespace App\Traits;

use App\Models\{EmailTemplate,ConfigSetting, FirebaseNotification};
use Exception;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\Messaging\{InvalidArgument,NotFound};
use Illuminate\Support\Facades\Log;

trait SendResponseTrait
{
    /*
   Method Name:    apiResponse
   Purpose:        To send an api response
   Params:         [apiResponse,statusCode,message,data]
   */
   public function apiResponse($apiResponse, $statusCode = '404', $message = 'No records Found', $data = null) {
    $responseArray = [];
    if($apiResponse == 'success') {
        $responseArray['api_response'] = $apiResponse;
        $responseArray['status_code'] = $statusCode;
        $responseArray['message'] = $message;
        $responseArray['data'] = $data;
    } else {
        $responseArray['api_response'] = 'error';
        $responseArray['status_code'] = $statusCode;
        $responseArray['message'] = $message;
        $responseArray['data'] = $data;
    }

    return response()->json($responseArray, $statusCode);
   }
    /* End Method apiResponse*/

    /*
    Method Name:    getTemplateByName
    Purpose:        Get email template by name
    Params:         [name,id]
    */
    public function getTemplateByName($name, $id = 1) {
        $template = EmailTemplate::where('template_name', $name)->first(['id', 'template_name', 'subject', 'template']);
        return $template;
   }
   /* End Method getTemplateByName */
      /*
    Method Name:    mailData
    Purpose:        prepare email data
    Params:         [$to, $subject, $email_body, $templete_name, $templete_id, $logtoken , $remarks = null]
    */   
    public function mailData($to, $subject, $email_body, $templete_name, $templete_id, ){
        try{
            $stringToReplace = ['{{YEAR}}',  '{{$COMPANYNAME}}' ];
            $stringReplaceWith = [date("Y"), config('constants.COMPANYNAME') ]; 
            $email_body = str_replace( $stringToReplace , $stringReplaceWith , $email_body );
                    
            $data = [  
                'to'            => $to, 
                'subject'       => $subject,
                'html'          => $email_body, 
                'templete_name' => $templete_name,
                'templete_id'   => $templete_id,
            ]; 

            return $data;
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage( ) );
        }
    } 
    /* End Method mailData */

        /*
    Method Name:    mailSend
    Purpose:        Send email from node
    Params:         [data]
    */   
    public function mailSend($data)
    {
        try {
          
            $emailConfig = ConfigSetting::where('type', 'smtp')->pluck('value', 'key')->toArray();
            config([
                'mail.mailer'     => 'smtp',
                'mail.host'       => $emailConfig['host'],
                'mail.port'       => $emailConfig['port'],
                'mail.username'   => $emailConfig['username'],
                'mail.password'   => $emailConfig['password'],
                'mail.encryption' => $emailConfig['encryption'],
                'mail.from.address' => $emailConfig['from_email'],
                'mail.from.name' => $emailConfig['from_name'],
            ]);
            
            // Prepare the body content for the email
            $body = ['body' => $data['html']];
        
            Mail::send('email.sendEmail', $body, function ($message) use ($data,$emailConfig) {
                $message->to($data['to']) 
                        ->subject($data['subject']) 
                        ->from($emailConfig['from_email']); 
            });
           
            // If the email is sent successfully, it reaches here
            return true;
        } catch (\Exception $e) {
            // Log the error message if email sending fails
            \Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /* End Method mailSend */


    
    public function sendPushNotification($deviceToken, $title, $body, $type, $user_id, $redirect_data) {
        try {
            $firebase = (new Factory)
                ->withServiceAccount(public_path('firebase-service.json'));
            $messaging = $firebase->createMessaging();
    
            $data = [
                'type' => $type,
                'redirect_data' => $redirect_data
            ];
    
            $message = CloudMessage::fromArray([
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'token' => $deviceToken,
            ])->withData([
                'type' => $type,
                'redirect_data' => json_encode($redirect_data),
            ]);
    
            $response = $messaging->send($message);
            Log::info('Push notification sent successfully.', ['response' => $body]);
    
            FirebaseNotification::create([
                'user_id'   => $user_id,
                'title'     => $title,
                'body'      => $body,
                'data'      => json_encode($data),
            ]);
    
            return true;
        } catch (NotFound $e) {
            Log::warning('FCM token not found or invalid: ' . $e->getMessage());
            return true;
        } catch (InvalidArgument $e) {
            Log::warning('Invalid FCM token provided: ' . $e->getMessage());
            return true;
        } catch (\MessagingException $e) {
            Log::error('Messaging error occurred: ' . $e->getMessage());
            return true;
            // throw new \Exception($e->getMessage());
        } catch( Exception $e){
            Log::error('Exception : ' . $e->getMessage());
            return true;
        }
    }
}