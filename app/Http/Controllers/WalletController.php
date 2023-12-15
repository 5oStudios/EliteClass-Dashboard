<?php

namespace App\Http\Controllers;

use URL;
use Auth;
use Crypt;
use Session;
use App\User;
use Redirect;
use App\Wallet;
use App\Setting;
use PaytmWallet;
use App\Currency;
use PayPal\Api\Item;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use App\WalletSettings;
use PayPal\Api\Payment;
use PayPal\Api\ItemList;
use App\WalletTransactions;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Support\Facades\Notification;

class WalletController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | WalletController
    |--------------------------------------------------------------------------
    |
    | This controller holds the logics and functionality of Wallet Recharge system.
    |
     */

    /**
     * This functions holds the functionality of get paypal api keys
     */

    public function __construct()
    {
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    /**
     * This functions @return the user wallet view
     */

    public function index()
    {

        /* If user wallet is found then create it */

        if(auth()->user()->wallet == NULL)
        {
            auth()->user()->wallet()->create();
        }
        

        $user = auth()->user();
        return view('front.wallet.mywallet', compact('user'));
       
    }

    public function insert(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric|min:1|max:5000',
            'reason' => 'required|max:300',
        ],[
            "amount.required"=>__("Amount is required"),
            "amount.numeric"=>__("Amount must be a numeric value"),
            "amount.min"=>__("Amount should not be zero or a negative value"),
            "amount.max"=>__("Amount max range is 5000"),
            "reason.required"=>__("Reason is required"),
            "reason.max"=>__("Reason should not be more than 300 characters"),
        ]);

        /** Get the default currency */
        $currency = Currency::where('default', '=', '1')->first();

        /** Get logged in user wallet */
        $user = User::find($request->user_id);
        $wallet = Wallet::where('user_id', $user->id)->first();

        // Generate unique transaction id
        $today = date('Ymd');
        $transactions = WalletTransactions::where('transaction_id', 'like', $today.'%')->pluck('transaction_id');
        do{
            $transaction_id = $today . rand(1000000, 9999999);
        } while ($transactions->contains($transaction_id));


        if (isset($wallet)) {
            /** Check if user wallet status is active or not */
            if ($wallet->status == 1) {

                /** Update the wallet balance */
                $user->wallet()->update([
                    'balance' => $wallet->balance + $request->amount,
                ]);

                /** Create wallet transcation history */
                $wallet_transaction = WalletTransactions::create([
                    'wallet_id' => $user->wallet->id,
                    'user_id' => $user->id,
                    'admin_id' => auth()->id()?? NULL,
                    'transaction_id' => $transaction_id,
                    'payment_method' => 'Direct',
                    'total_amount' => $request->amount,
                    'currency' => $currency->code,
                    'currency_icon' => $currency->symbol,
                    'type' => 'Credit',
                    'detail' => __('TopUp to wallet by Admin'),
                    'reason' => $request->reason,
                    ]
                );
            }
        } else {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => $request->amount,
                'status' => 1
            ]);

            /** Create wallet transcation history */
            $wallet_transaction = WalletTransactions::create([
                'wallet_id' => $user->wallet->id,
                'user_id' => $user->id,
                'admin_id' => auth()->id()?? NULL,
                'transaction_id' =>  $transaction_id,
                'payment_method' => 'Direct',
                'total_amount' => $request->amount,
                'currency' => $currency->code,
                'currency_icon' => $currency->symbol,
                'type' => 'Credit',
                'detail' => __('Added to wallet by Admin'),
                'reason' => $request->reason,
            ]);
        }

        if(env('ONE_SIGNAL_NOTIFICATION_ENABLED') == '1'){

            if(count($wallet->user->device_tokens) > 0 && $wallet->user->notifications){
                Notification::send($wallet->user,new \App\Notifications\WalletTopUp($wallet_transaction));
            }
        }

        return back()->with('success', __('Wallet Topup Successfully'));
       
    }

    public function deduct(Request $request)
    {
        /** Get logged in user wallet */
        $user = User::findOrFail($request->user_id);
        $wallet = Wallet::where('user_id', $user->id)->first();

        
        if (isset($wallet)) {
            $request->validate([
                'amount' => 'required|numeric|min:1|lte:'.$wallet->balance,
                'answer' => 'max:300',
            ],[
                "amount.required"=>__("Amount is required"),
                "amount.numeric"=>__("Amount must be a numeric value"),
                "amount.min"=>__("Amount should not be zero or a negative value"),
                "amount.lte"=>__("Amount must be less than or equal to user current balance"),
                "reason.max"=>__("Reason should not be more than 300 characters"),
            ]);
            
            /** Get the default currency */
            $currency = Currency::where('default', '=', '1')->first();
            
            // Generate unique transaction id
            $today = date('Ymd');
            $transactions = WalletTransactions::where('transaction_id', 'like', $today.'%')->pluck('transaction_id');
            do{
                $transaction_id = $today . rand(1000000, 9999999);
            } while ($transactions->contains($transaction_id));

            /** Check if user wallet status is active or not */
            if ($wallet->status == 1) {

                /** Update the wallet balance */
                $user->wallet()->update([
                    'balance' => $wallet->balance - $request->amount,
                ]);

                /** Create wallet transcation history */
                WalletTransactions::create([
                    'wallet_id' => $user->wallet->id,
                    'user_id' => $user->id,
                    'admin_id' => auth()->id()?? NULL,
                    'transaction_id' =>  $transaction_id,
                    'payment_method' => 'Direct',
                    'total_amount' => $request->amount,
                    'currency' => $currency->code,
                    'currency_icon' => $currency->symbol,
                    'type' => 'Debit',
                    'detail' => __('Removed amount from wallet by Admin'),
                    'reason' => $request->reason?? NULL,
                    ]
                );
            }
        }else{
            return back()->with('error', __('User Wallet does not exist'));
        }

        return back()->with('success', __('Removed amount from Wallet, Successfully'));
       
    }

    public function view()
    {
        $users =  User::all();

        return view('front.wallet.alluser_wallet', compact('users'));
    }

    /**
     * To display wallet add money view to user
     */

    public function checkout(Request $request)
    {
        $wallet_settings = WalletSettings::first();
        $amount = strip_tags($request->amount);
        return view('front.wallet.wallet_checkout', compact('amount', 'wallet_settings'));
    }

    /**
     * Initializing wallet checkout using Paypal payment 
     */

    public function walletPayPal(Request $request)
    {
        $user_wallet = Wallet::where('user_id', Auth::user()->id)->first();

        $currency = Currency::where('default', '=', '1')->first();
        $gsettings = Setting::first();
        $currency_code = strtoupper($currency->code);

        $pay = Crypt::decrypt(strip_tags($request->amount));
        Session::put('payment',$pay);
        $payer = new Payer();
                $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName('Item 1') /** item name **/
                    ->setCurrency($currency_code)
                    ->setQuantity(1)
                    ->setPrice($pay); /** unit price **/
        $item_list = new ItemList();
                $item_list->setItems(array($item_1));
        $amount = new Amount();
                $amount->setCurrency($currency_code)
                    ->setTotal($pay);
        $transaction = new Transaction();
                $transaction->setAmount($amount)
                    ->setItemList($item_list)
                    ->setDescription('Your transaction description');
        $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(URL::route('wallet.paypal.success')) /** Specify return URL **/
                    ->setCancelUrl(URL::route('status'));
        $payment = new Payment();
                $payment->setIntent('Sale')
                    ->setPayer($payer)
                    ->setRedirectUrls($redirect_urls)
                    ->setTransactions(array($transaction));
                
        try {
            $payment->create($this->_api_context);
        } 
        catch (\PayPal\Exception\PayPalConnectionException $ex) {
            if (\Config::get('app.debug')) {
                \Session::flash('delete', $ex->getMessage());
                return redirect('/');
            } else {
                \Session::flash('delete', $ex->getMessage());
                return redirect('/');
            }
        }

        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }


        /** add payment ID to session **/
        Session::put('paypal_payment_id', $payment->getId());

        Session::put('user_wallet', $user_wallet->id);

        if (isset($redirect_url)) {
        /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }

        \Session::put('error', __('Unknown error occurred'));
                return redirect('/');
    }


    /**
     * Wallet success function using Paypal payment 
     */

    public function walletpaypalSuccess(Request $request)
    {
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');
        $amount = Session::get('payment');
        /** clear the session payment ID **/
                Session::forget('paypal_payment_id');
                if (empty($request->get('PayerID')) || empty($request->get('token')))

                 {
        \Session::flash('delete', trans('flash.PaymentFailed'));
                    return Redirect('/');
        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->get('PayerID'));
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);


        if ($result->getState() == 'approved') {

             $transactions = $payment->getTransactions();
            $relatedResources = $transactions[0]->getRelatedResources();
            $sale = $relatedResources[0]->getSale();
            $saleId = $sale->getId();

            $currency = Currency::where('default', '=', '1')->first();

            $instructor_plan_id = Session::get('user_wallet');
            
            $wallet = Wallet::where('user_id',Auth::user()->id)->first();
           
             
            if (isset($wallet)) {

                /**update money if already wallet exist **/
                if ($wallet->status == 1) {

                    auth()->user()->wallet()->update([
                        'balance' => $wallet->balance + $amount,
                    ]);

                    }
                }


                $wallet_transaction = WalletTransactions::create([
                    'wallet_id' => auth()->user()->wallet->id,
                    'user_id' => Auth::User()->id,
                    'transaction_id' => $payment_id,
                    'payment_method' => 'PayPal',
                    'total_amount' => $amount,
                    'currency' => $currency->code,
                    'currency_icon' => $currency->symbol,
                    'type' => 'Credit',
                    'detail' => 'Added to wallet via PayPal',

                    ]
                );

            Session::forget('user_wallet');
            

            \Session::flash('success', trans('flash.PaymentSuccess'));
            return redirect('/wallet');

        }
        

        Session::forget('instructor_plan');

        \Session::flash('delete', trans('flash.PaymentFailed'));
            return redirect('/wallet');
    }


    /**
     * This function holds the funncality to recharge wallet using paytm.  
     */

    public function paytm(Request $request)
    {

        $user_wallet = Wallet::where('user_id', Auth::user()->id)->first();

        Session::put('user_wallet', $user_wallet->id);


        $appurl = env('APP_URL');

        $payment = PaytmWallet::with('receive');
        $payment->prepare([
          'order' => uniqid(),
          'user' => Auth::User()->id,
          'mobile_number' => strip_tags($request->mobile),
          'email' => strip_tags($request->email),
          'amount' => strip_tags($request->amount),
          'callback_url' => url('/wallet/status/paytm')
        ]);
        return $payment->receive();

    }


    /**
     * This function holds the funncality to capture paytm payment and recharge wallet .  
     */

    public function paymentwallet(Request $request)
    {

        $transaction = PaytmWallet::with('receive');

        $response = $transaction->response();
        $order_id = $transaction->getOrderId();

        $gsettings = Setting::first();

        if($transaction->isSuccessful()){

            /** Get the default currency */

            $currency = Currency::where('default', '=', '1')->first();

             /** Get the logged in user wallet */

            $wallet = Wallet::where('user_id',Auth::user()->id)->first();

            if (isset($wallet)) {

                /** Check if user wallet status is active or not */
                
                if ($wallet->status == 1) {

                    /** Update the wallet balance */

                    auth()->user()->wallet()->update([
                        'balance' => $wallet->balance + $response['TXNAMOUNT'],
                    ]);

                    /** Create wallet transcation history */


                    $wallet_transaction = WalletTransactions::create([
                        'wallet_id' => auth()->user()->wallet->id,
                        'user_id' => Auth::User()->id,
                        'transaction_id' => $response['TXNID'],
                        'payment_method' => 'PayTM',
                        'total_amount' => $response['TXNAMOUNT'],
                        'currency' => $currency->code,
                        'currency_icon' => $currency->symbol,
                        'type' => 'Credit',
                        'detail' => 'Added to wallet via PayTM',

                        ]
                    );
               

                }

            }
            

            \Session::flash('success', __('flash.PaymentSuccess'));
            return redirect('/wallet');



        }else if($transaction->isFailed()){

            /** If payment failed @return back to previous location */

            Session::forget('instructor_plan');
        
          \Session::flash('delete', __('flash.PaymentFailed'));
            return redirect('/wallet');
        }

    }


    /**
     * This function holds the funncality to recharge wallet using stripe.  
     */

    public function payStripe(Request $request)
    {
        
        $stripe = Stripe::make(env('STRIPE_SECRET'));
        
        try {

            $token = $stripe->tokens()->create([
                'card' => [
                    'number'    => strip_tags($request->get('card_no')),
                    'exp_month' => $request->get('expiry_month'),
                    'exp_year'  => $request->get('expiry_year'),
                    'cvc'       => strip_tags($request->get('cvv')),
                ],
            ]);

            if (!isset($token['id'])) {
                return Redirect::to('strips')->with(__('Token is not generate correct'));
            }

            $charge = $stripe->charges()->create([
                'card' => $token['id'],
                'currency' => __('USD'),
                'amount'   => strip_tags($request->amount),
                'description' => __('Register Event'),
            ]);

            /** Get the default currency */

            $currency = Currency::where('default', '=', '1')->first();

            /** Get logged in user wallet */

            $wallet = Wallet::where('user_id',Auth::user()->id)->first();

            if (isset($wallet)) {

                /** Check if user wallet status is active or not */
                
                if ($wallet->status == 1) {

                    /** Update the wallet balance */
                    

                    auth()->user()->wallet()->update([
                        'balance' => $wallet->balance + $charge['amount']/100,
                    ]);

                    /** Create wallet transcation history */

                    $wallet_transaction = WalletTransactions::create([

                        'wallet_id' => auth()->user()->wallet->id,
                        'user_id' => auth()->id(),
                        'transaction_id' => $charge['id'],
                        'payment_method' => __('Stripe'),
                        'total_amount' => $charge['amount']/100,
                        'currency' => $currency->code,
                        'currency_icon' => $currency->symbol,
                        'type' => 'Credit',
                        'detail' => __('Added to wallet via Stripe'),

                        ]
                    );

                }

            }
            
         
            \Session::flash('success', __('Payment success'));
            return redirect('/wallet');

            } catch (\Exception $e) {

                /** If payment failed return with exception */
               
                \Session::flash('delete', $e->getMessage());

                return redirect('/wallet');
            }
            
    }  
}
