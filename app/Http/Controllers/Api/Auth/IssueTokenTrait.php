<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

trait IssueTokenTrait{

	public function issueToken(Request $request, $grantType, $scope = "*"){

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('lms');
            return response()->json(['token' => $token], 200);
        } else {
            Log::info('User is not authorized!!!!!!');
            return response()->json(array("errors" => ["message" => [__("Invalid Login")]]), 400);
        }


        //todo: remove this once login is confirmed as working fine
		$params = [
    		'grant_type' => $grantType,
    		'client_id' => $request->client_id,
    		'client_secret' => $request->client_secret,
            'username' => $request->email,
    		'scope' => $scope
    	];
    	$request->request->add($params);
        $proxy = Request::create('oauth/token', 'POST', $params, [], [], ['HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded']);
    	return Route::dispatch($proxy);

	}

}
