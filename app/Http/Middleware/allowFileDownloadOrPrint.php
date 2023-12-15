<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Order;
use App\CourseClass;
use Illuminate\Http\Request;

class allowFileDownloadOrPrint
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('key')) {
            $queryStr = decryptData($request->key);
            parse_str($queryStr, $queryParams);

            $diffInSeconds = now()->diffInSeconds($queryParams['time']);

            if ($diffInSeconds <= 60) {
                $user = User::find($queryParams['user_id']);
                $courseclass = CourseClass::find($request->id);

                if ($courseclass) {
                    $order = Order::query()
                        ->where('user_id', $user->id)
                        ->where('course_id', $courseclass->course_id)
                        ->where('enroll_expire', '>=', date('Y-m-d'))
                        ->activeOrder()
                        ->first();

                    if (!$order) {
                        $order = Order::query()
                                    ->whereJsonContains('bundle_course_id', strval($courseclass->course_id))
                                    ->where('user_id', $user->id)
                                    ->where('enroll_expire', '>=', date('Y-m-d'))
                                    ->activeOrder()
                                    ->first();
                    }

                    if ($order) {
                        if ($order->total_amount == ($order->paid_amount + $order->coupon_discount)) {
                            $unlock = 4;
                        } elseif ($order->installments && (($order->paid_amount + $order->coupon_discount) > 0)) {
                            $paid = $order->paid_amount + $order->coupon_discount;
                            $pendingInstallment = $order->installments_list ? $order->installments_list->count() : 0;
                            $unlock = 0;

                            foreach ($order->payment_plan as $i) {
                                if ($paid > 0) {
                                    $unlock++;
                                }
                                $paid -= $i->amount;
                            }
                            if ($pendingInstallment > $unlock) {
                                $unlock = $pendingInstallment;
                            }
                        } else {
                            $unlock = 0;
                        }
                    } else {
                        $chapterOrder = Order::where('user_id', $user->id)->where('chapter_id', $courseclass->coursechapter_id)->activeOrder()->first();
                    }

                    if (($order && $unlock > 0 && $courseclass->coursechapters->unlock_installment <= $unlock) || $chapterOrder) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }

                abort('404');
            }

            abort('404');
        }

        abort('404');
    }
}
