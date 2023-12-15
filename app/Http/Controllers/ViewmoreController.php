<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewmoreController extends Controller
{
    public function purchasedcourse(){
        return view('front.viewmore.purchase');
    }
}
