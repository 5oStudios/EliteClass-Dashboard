<?php
namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Order;
use TwilioMsg;
use App\Course;
use Notification;
use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use App\Enums\OrderStatusEnum;
use App\Notifications\AdminOrder;
use App\Notifications\UserEnroll;


class TestController extends Controller
{
	public function test()
   	{
		
   	}

}
