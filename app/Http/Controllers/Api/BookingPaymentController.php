<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Payment as PayPalPayment;
use App\Services\API\SabeePaymentService;


class BookingPaymentController extends Controller
{

    protected $SabeePaymentService;

    public function __construct(SabeePaymentService $sabeePaymentService)
    {
        $this->sabeePaymentService = $sabeePaymentService;
    }
    /**
     * functionName : makePayment
     * createdDate  : 22-05-2025
     * purpose      : Initiate payment based on selected method (card, PayPal, or cash on arrival)
     */
    public function makePayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:card,paypal,cash_on_arrival',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'customer_name' => 'required|string',
            'hotel_id' => 'required',
            'resveration_code' => 'required'
        ]);

        $method = $request->payment_method;

        switch ($method) {
            case 'card':
                return $this->payWithCard($request);
            case 'paypal':
                return $this->payWithPaypal($request);
            case 'cash_on_arrival':
                return $this->payOnArrival($request);
            default:
                return response()->json(['error' => 'Invalid payment method'], 400);
        }
    }

    /**
     * functionName : payWithCard
     * createdDate  : 22-05-2025
     * purpose      : Process credit/debit card payment using Stripe
     */
    private function payWithCard($request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $charge = Charge::create([
                'amount' => $request->amount * 100,
                'currency' => $request->currency,
                'source' => $request->token,
                'description' => "Payment by {$request->customer_name}"
            ]);


            $payments = [
                [
                    'customer_name' => $request->customer_name,
                    'price' => $request->amount,
                    'payment_date_time' => now()->format('Y-m-d H:i:s'),
                    'payment_method' => 'Credit Card',
                    'description' => 'Payment with Credit Card',
                ]
            ];

            $hotelId = $request->hotel_id;
            $reservationCode = $request->resvation_code;

            $sabeeResponse = $this->sabeePaymentService->submitPaymentToSabee($hotelId, $reservationCode, $payments);

            return response()->json([
                'status' => 'success',
                'method' => 'card',
                'transaction_id' => $charge->id,
                'message' => 'Payment successful via Credit/Debit Card'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Stripe Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * functionName : payWithPaypal
     * createdDate  : 22-05-2025
     * purpose      : Initiate PayPal payment and return redirect URL
     */
    private function payWithPaypal($request)
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                env('PAYPAL_CLIENT_ID'),
                env('PAYPAL_SECRET')
            )
        );

        $apiContext->setConfig([
            'mode' => env('PAYPAL_MODE', 'sandbox'),
        ]);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $amount->setCurrency($request->currency)
            ->setTotal($request->amount);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription("Payment by {$request->customer_name}");

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(url('/api/payment-success'))
            ->setCancelUrl(url('/api/payment-cancel'));

        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($apiContext);

            return response()->json([
                'status' => 'redirect',
                'method' => 'paypal',
                'redirect_url' => $payment->getApprovalLink(),
                'message' => 'Redirecting to PayPal...'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'PayPal Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * functionName : paypalSuccess
     * createdDate  : 22-05-2025
     * purpose      : Handle successful return from PayPal and complete transaction
     */
    public function paypalSuccess(Request $request)
    {
        $paymentId = $request->get('paymentId');
        $payerId = $request->get('PayerID');

        if (!$paymentId || !$payerId) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid PayPal response'
            ], 400);
        }

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                env('PAYPAL_CLIENT_ID'),
                env('PAYPAL_SECRET')
            )
        );

        $apiContext->setConfig([
            'mode' => env('PAYPAL_MODE', 'sandbox'),
        ]);

        try {
            $payment = PayPalPayment::get($paymentId, $apiContext);
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);
            $result = $payment->execute($execution, $apiContext);

            $payments = [
                [
                    'customer_name' => $request->customer_name,
                    'price' => $request->amount,
                    'payment_date_time' => now()->format('Y-m-d H:i:s'),
                    'payment_method' => 'PayPal',
                    'description' => 'Payment with paypal',
                ]
            ];

            $hotelId = $request->hotel_id;
            $reservationCode = $request->resvation_code;

            $sabeeResponse = $this->sabeePaymentService->submitPaymentToSabee($hotelId, $reservationCode, $payments);

            return response()->json([
                'status' => 'success',
                'method' => 'paypal',
                'transaction_id' => $paymentId,
                'message' => 'Payment completed via PayPal'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'PayPal Execution Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * functionName : paypalCancel
     * createdDate  : 22-05-2025
     * purpose      : Handle PayPal payment cancellation
     */
    public function paypalCancel()
    {
        return response()->json([
            'status' => 'cancelled',
            'method' => 'paypal',
            'message' => 'Payment was cancelled by the user.'
        ]);
    }

    /**
     * functionName : payOnArrival
     * createdDate  : 22-05-2025
     * purpose      : Handle booking marked for cash payment on arrival
     */
    private function payOnArrival($request)
    {
        $payments = [
            [
                'customer_name' => $request->customer_name,
                'price' => $request->amount,
                'payment_date_time' => now()->format('Y-m-d H:i:s'),
                'payment_method' => 'Cash',
                'description' => 'Cash payment on arrival',
            ]
        ];

        $hotelId = $request->hotel_id;
        $reservationCode = $request->resvation_code;

        $sabeeResponse = $this->sabeePaymentService->submitPaymentToSabee($hotelId, $reservationCode, $payments);
        return response()->json([
            'status' => 'pending',
            'method' => 'cash_on_arrival',
            'message' => 'Marked as Pay on Arrival. No online transaction done.'
        ]);
    }
}
