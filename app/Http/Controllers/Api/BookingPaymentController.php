<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

// Modern PayPal SDK imports
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

use App\Services\API\SabeePaymentService;

class BookingPaymentController extends Controller
{
    protected $sabeePaymentService;
    private $paypalClient;

    public function __construct(SabeePaymentService $sabeePaymentService)
    {
        $this->sabeePaymentService = $sabeePaymentService;
        $this->paypalClient = $this->getPayPalClient();
    }

    /**
     * Initialize PayPal Client
     */
    private function getPayPalClient()
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.secret'); 
        $environment = config('services.paypal.mode', 'sandbox');

        if ($environment === 'live') {

            $environment = new ProductionEnvironment($clientId, $clientSecret);
        } else {
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        }

        return new PayPalHttpClient($environment);
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
            $reservationCode = $request->resveration_code;

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
    private function payWithPaypal(Request $request)
    {
        try {
            // Validate inputs
            if (!is_numeric($request->amount) || $request->amount <= 0) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Invalid amount provided'
                ], 400);
            }

            $request_body = new OrdersCreateRequest();
            $request_body->prefer('return=representation');
            
            $amountValue = number_format((float)$request->amount, 2, '.', '');
            
            $request_body->body = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => 'hotel_booking_' . $request->resveration_code,
                        'description' => "Hotel Booking - Payment by {$request->customer_name}",
                        'amount' => [
                            'currency_code' => $request->currency,
                            'value' => $amountValue
                        ]
                    ]
                ],
                'application_context' => [
                    'cancel_url' => url('/api/payment-cancel'),
                    'return_url' => url('/api/payment-success'),
                    'brand_name' => 'Hotel Booking',
                    'landing_page' => 'BILLING',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW'
                ]
            ];

            $response = $this->paypalClient->execute($request_body);
            
            // Store order details temporarily (you can use session or database)
            session(['paypal_order_data' => [
                'order_id' => $response->result->id,
                'customer_name' => $request->customer_name,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'hotel_id' => $request->hotel_id,
                'resveration_code' => $request->resveration_code
            ]]);

            // Find approval URL
            $approvalUrl = null;
            foreach ($response->result->links as $link) {
                if ($link->rel === 'approve') {
                    $approvalUrl = $link->href;
                    break;
                }
            }

            return response()->json([
            'data' => [
                'status' => 'redirect',
                'method' => 'paypal',
                'order_id' => $response->result->id,
                'redirect_url' => $approvalUrl,
                'message' => 'Redirecting to PayPal...'
            ]
        ]);

        } catch (\Exception $e) {
            \Log::error('PayPal Create Payment Error: ' . $e->getMessage());
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
        $orderId = $request->get('token'); // PayPal returns order ID as 'token' in new SDK
        
        if (!$orderId) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Order ID not found'
            ], 400);
        }

        try {
            // Capture the payment
            $captureRequest = new OrdersCaptureRequest($orderId);
            $captureRequest->prefer('return=representation');
            $response = $this->paypalClient->execute($captureRequest);

            // Get stored order data
            $orderData = session('paypal_order_data');
            
            if (!$orderData || $orderData['order_id'] !== $orderId) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Order data not found or mismatch'
                ], 400);
            }

            // Submit to Sabee
            $payments = [
                [
                    'customer_name' => $orderData['customer_name'],
                    'price' => $orderData['amount'],
                    'payment_date_time' => now()->format('Y-m-d H:i:s'),
                    'payment_method' => 'PayPal',
                    'description' => 'Payment with PayPal',
                ]
            ];

            $hotelId = $orderData['hotel_id'];
            $reservationCode = $orderData['resveration_code'];

            $sabeeResponse = $this->sabeePaymentService->submitPaymentToSabee($hotelId, $reservationCode, $payments);

            // Clear session data
            session()->forget('paypal_order_data');

            $transactionId = $response->result->purchase_units[0]->payments->captures[0]->id ?? $orderId;

            return response()->json([
                'status' => 'success',
                'method' => 'paypal',
                'transaction_id' => $transactionId,
                'order_id' => $orderId,
                'message' => 'Payment completed via PayPal'
            ]);

        } catch (\Exception $e) {
            \Log::error('PayPal Capture Error: ' . $e->getMessage());
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
    public function paypalCancel(Request $request)
    {
        // Clear session data if exists
        session()->forget('paypal_order_data');
        
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
        $reservationCode = $request->resveration_code;

        $sabeeResponse = $this->sabeePaymentService->submitPaymentToSabee($hotelId, $reservationCode, $payments);
        
        return response()->json([
            'status' => 'pending',
            'method' => 'cash_on_arrival',
            'message' => 'Marked as Pay on Arrival. No online transaction done.'
        ]);
    }
}