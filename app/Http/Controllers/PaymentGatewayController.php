<?php

namespace App\Http\Controllers;

use App\PaymentGateway;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:payment-charges.manage', ['only' => ['index', 'update']]);
    }

    public function index()
    {
        $visa_payment = PaymentGateway::where('payment_method', 'VISA/MASTER')->first();
        $knet_payment = PaymentGateway::where('payment_method', 'KNET')->first();

        return view('admin.payment_gateway.index', compact('visa_payment', 'knet_payment'));
    }


    public function store(Request $request)
    {
        
    }


    public function show(PaymentGateway $paymentGateway)
    {
        
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:VISA/MASTER,KNET',
            'type' => 'required|in:fixed,percentage',
            'charges' => ['required','numeric','min:0',
                            function($attribute, $value, $fail) use($request) {
                                if($request->type == 'percentage'){
                                    if($value > 100){
                                        $fail(__('Percentage amount should not be greater than 100'));
                                    } 
                                }
                            }],
        ],[
            'payment_method.required' => 'Payment method is required',
            'payment_method.in' => 'Payment method is invalid',
            'type.required' => 'Charges type is required',
            'type.in' => 'Charges type is invalid',
            'charges.required' => 'Charges is required',
            'charges.numeric' => 'Charges must be a numeric value',
            'charges.min' => 'Charges should not be negative value',
        ]);

        $paymentGateway = PaymentGateway::find($id);
        $paymentGateway->update([
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'charges' => $request->charges
        ]);


        return back()->with('success', trans('flash.UpdatedSuccessfully'));
    }


    public function destroy(PaymentGateway $paymentGateway)
    {
        //
    }
}
