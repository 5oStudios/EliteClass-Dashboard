<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Terms;
use Spatie\Permission\Models\Role;

class TermsController extends Controller
{
    public function __construct()
    {
    
        $this->middleware('permission:terms-condition.manage', ['only' => ['show','update','about_us']]);
        $this->middleware('permission:privacy-policy.manage', ['only' => ['showpolicy', 'updatepolicy', 'update']]);
    
    }
    public function show()
    {
    	$items = Terms::first();
		return view('admin.terms.terms',compact('items'));
    }
    public function about_us()
    {
    	$items = Terms::first();
		return view('admin.terms.about_us',compact('items'));
    }

    public function update(Request $request)
    {
       
    	$data = Terms::first();
    	$input = $request->all();

        if(isset($data))
        {
            $data->update($input);
        }
        else
        {
            $data = Terms::create($input);
          
            $data->save();
        }

    	return back()->with('success',trans('flash.UpdatedSuccessfully'));
    }

    public function showpolicy()
    {
        $items = Terms::first();
        return view('admin.terms.policy',compact('items'));
    }

    public function updatepolicy(Request $request)
    {
       
        $data = Terms::first();
        $input = $request->all();

        if(isset($data))
        {
            $data->update($input);
        }
        else
        {
            $data = Terms::create($input);
          
            $data->save();
        }

        return back()->with('success',trans('flash.UpdatedSuccessfully'));
    }
}
