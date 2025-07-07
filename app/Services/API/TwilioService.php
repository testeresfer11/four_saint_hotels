<?php
namespace App\Services\API;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        );
    }

    /**
     * Make a call to a phone number using TwiML URL
     */
    public function makeCall($to, $twimlUrl)
    {
        return $this->client->calls->create(
            $to,
            config('services.twilio.from'),
            ['url' => $twimlUrl]
        );
    }
}
