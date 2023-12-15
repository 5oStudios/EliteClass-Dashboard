<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use File;
use Image;
use App\Affiliate;
use Session;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;



class AffiliateController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | AffiliateController
    |--------------------------------------------------------------------------
    |
    | This controller holds the logics and functionality of Affiliate system.
    |
     */

    /**
     * This function shows the affilate settings on admin dashboard.
     */
    public function __construct()
    {
       
        $this->middleware('permission:affiliate.manage', ['only' => ['index','update']]);
    
    }
    public function index()
    {

        $affilates = Affiliate::first();

        return view('admin.affiliates.index', compact('affilates'));
    }

    /**
     * This function holds the functionality to updates the affilate settings.
     */

    public function update(Request $request)
    {

        /* Retrieve first affiliate row*/

        $affilates = Affiliate::first();

        $input = $request->all();

        if ($affilates) {

            $this->validate($request,[
                'ref_length' => 'required|min:2',
            ],[
                "ref_length.required"=> __('Referral Length is required'),
                "ref_length.min" => __('Referral minimum length should not be less than 10'),
                
            ]);

            $input['point_per_referral'] = $request->point_per_referral;

            if (!isset($input['status'])) {
                $input['status'] = 0;
            } else {
                $input['status'] = 1;
            }

            $affilates->update($input);

        } else {

            /** Create row if not exist */
            $this->validate($request,[
                'ref_length' => 'required|min:2',
                ],[
                    "ref_length.required"=> __('Referral Length is required'),
                    "ref_length.min" => __('Referral minimum length should not be less than 10'),
                    
            ]);

            $affilates = new Affiliate;

            $input['point_per_referral'] = $request->point_per_referral;
            
            if (!isset($input['status'])) {
                $input['status'] = 0;
            } else {
                $input['status'] = 1;
            }

            $affilates->create($input);
        }

        Session::flash('success', __('flash.UpdatedSuccessfully'));
        // return back()->with(__('Saved successfully'));

    }

    /**
     * This functions holds the funcnality to show User's affiliate link.
     */

    public function getlink()
    {

        $affilates = Affiliate::first();

        return view('front.affiliate.show', compact('affilates'));
    }

    /**
     * This functions holds the funcnality to generate user's affiliate link.
     */

    public function generatelink()
    {

        $refercode = User::createReferCode();

        User::where('id', auth()->id())
            ->update(['affiliate_id' => $refercode]);

        return view('front.affiliate.show');
    }
}
