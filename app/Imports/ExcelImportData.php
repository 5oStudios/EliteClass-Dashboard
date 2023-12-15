<?php

namespace App\Imports;

use App\BBL;
use App\User;
use App\Order;
use App\Course;
use App\Currency;
use Carbon\Carbon;
use App\CourseClass;
use App\BundleCourse;
use App\CourseProgress;
use App\OrderInstallment;
use App\WalletTransactions;
use App\Imports\ExportUsers;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImportData implements ToCollection
{   
    // @param Collection $collection
    
    public function collection(Collection $collection)
    {
        // dd($collection);

        foreach ($collection->toArray() as $key => $value)  
        { 
            
            if($key>0)
            {
                $currency = Currency::where('default', '=', '1')->first();
                $user = User::where('email', $value[0])->get();

                $lastOrder = Order::orderBy('created_at', 'desc')->where('status', '1')->first();

                if (!$lastOrder) {
                    $number = 0;
                } else {
                    $number = substr($lastOrder->order_id, 3);
                }

                if($value[2] == 'course'){
                    $order = Course::where('title', '{"en":"'.$value[1].'"}')->get();
                    if(count($order) < 0){

                        $pay_detail = 'Course Purchased by Manual';
                        
                        $course_id = $order->id;
                        $bundle_id = NULL;
                        $meeting_id = NULL;
    
                        $duration = $order->duration;
                        $bundle_course_id = NULL;
    
                        $todayDate = $order->start_date;
                        $expireDate = $order->end_date;
                    }

                }
                elseif($value[2] == 'bundle'){
                    $order = BundleCourse::where('title', '{"en":"'.$value[1].'"}')->get();
                    if(count($order) < 0){

                        $pay_detail = 'Package Purchased by Manual';
                        
                        $course_id = NULL;
                        $bundle_id = $order->id;
                        $meeting_id = NULL;
    
                        $bundle_course_id = $order->course_id;
                        $duration = NULL;
                        
                        $todayDate = $order->start_date;
                        $expireDate = $order->end_date;
                    }
                }
                elseif($value[2] == 'meeting'){
                    $order = BBL::where('meetingname', '{"en":"'.$value[1].'"}')->get();
                    if(count($order) < 0){

                        $pay_detail = 'Live Streaming Purchased by Manual';
                        $course_id = NULL;
                        $bundle_id = NULL;
                        $meeting_id = $order->id;
                        $duration = $order->duration;
                        $bundle_course_id = NULL;
    
    
                        $todayDate = $order->start_time;
                        $expireDate = $order->start_time;
                    }
                }

                if(count($user) < 0){

                    $wallet_transaction = WalletTransactions::create([
                        'wallet_id' => $user->wallet->id,
                        'user_id' => $user->id,
                        'transaction_id' => '',
                        'payment_method' => 'manual',
                        'total_amount' => $order->discount_price,
                        'currency' => $currency->code,
                        'currency_icon' => $currency->symbol,
                        'type' => 'Debit',
                        'detail' => $pay_detail,
                    
                    ]);

                    $or = [
                        'title' => $order->_title(),
                        'price' => $order->price,
                        'discount_price' => $order->discount_price,
                        'course_id' => $order->id,
                        'user_id' => $user->id,
                        'instructor_id' => $order->user->id,
                        'order_id' => '#' . sprintf("%08d", intval($number) + 1),
                        'transaction_id' => $txn_id ?? $wallet_transaction->id,
                        'payment_method' => 'manual',
                        'total_amount' => $order->discount_price,
                        'paid_amount' => $order->discount_price,
                        'installments' => 0,
                        'coupon_discount' => null,
                        'coupon_id' => null,
                        'currency' => $currency->code,
                        'currency_icon' => $currency->symbol,
                        'duration' => $duration,
                        'enroll_start' => $todayDate,
                        'enroll_expire' => $expireDate,
                        'instructor_revenue' => NULL,
                        'bundle_id' => $bundle_id,
                        'meeting_id' => $meeting_id,
                        'bundle_course_id' => $bundle_course_id,
                        'sale_id' => NULL,
                        'status' => 1,
                        'proof' => NULL,
                    ];
            
                    $created_order = Order::create($or);

                    if ($created_order) {
                        if ($course_id || $bundle_course_id) {
                            $courses = $course_id ? [$course_id] : $bundle_course_id;
                            foreach ($courses as $c) {
                                $p = CourseProgress::where([
                                    'course_id' => $c,
                                    'user_id' => $user->id])->first();
                                if (!isset($p)) {
                                    $chapters = CourseClass::where('status', 1)->where('course_id', $c)->get(['id'])->pluck('id');
                                    CourseProgress::create([
                                        'course_id' => $c,
                                        'user_id' => $user->id,
                                        'progress' => 0,
                                        'mark_chapter_id' => [],
                                        'all_chapter_id' => $chapters,
                                    ]);
                                }
                            }
                        }
            
                        OrderInstallment::create([
                            'order_id' => $created_order->id,
                            'user_id' => $user->id,
                            'transaction_id' => $wallet_transaction->id,
                            'payment_method' => 'manual',
                            'total_amount' => $order->discount_price,
                            'coupon_discount' => 0,
                            'coupon_id' => null,
                            'currency' => $currency->code,
                            'currency_icon' => $currency->symbol,
                        ]);
                    }

                }
                else{

                    $resp[] = $value[0];
                    
                }
                                
                
            }   
        }

        if($resp){

            // dd($resp);
            Excel::download(new ExportUsers($resp), 'exportusers.xlsx');
            return back();
        }


    }

}
