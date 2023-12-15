<?php

namespace App\Exports;

use App\Order;
use Carbon\Carbon;
use App\WalletTransactions;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionsExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    use Exportable;

    private $transaction_type, $credit_type, $debit_type, $from_date, $to_date;

    private $fileName = 'transactions.xlsx';
    private $writerType = Excel::XLSX;


    private $headers = [
        'Content-Type' => 'text/csv',
    ];


    public function __construct($data)
    {
        $this->transaction_type = $data['transaction_type'];
        $this->credit_type = $data['credit_type'];
        $this->debit_type = $data['debit_type'];
        $this->from_date = $data['from_date'];
        $this->to_date = $data['to_date'];
    }


    public function headings(): array
    {
        return [
            'User ID', 'First Name', 'Last Name', 'Email', 'Mobile', 'Type', 'Amount', 'Payment Method', 'Detail', 'Reason', 'Date', 'Order Items', 'Amount'
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12)->setBold(true);
            },
        ];
    }


    public function map($transaction): array
    {
        $orderItems = [];
        $amount = [];
        $installmentAmount = [];

        
        if (strtolower($transaction->detail) == 'installment paid') {
            foreach ($transaction->paidPaymentPlans as $i => $paid) {
                $itemName = $paid->order->course_id ? "Course: " : ($paid->order->bundle_id ? "Package: " : ($paid->order->chapter_id ? "Chapter: " : ($paid->order->meeting_id ? "Live Streaming: " : ($paid->order->offline_session_id ? "In-Person Session: " : ""))));
                if ($paid->order) {
                    $orderItems[] = $i + 1 . "- $itemName" . $paid->order->title . " => Instructor: " . $paid->order->instructor->fname . ' ' . $paid->order->instructor->lname;
                    $couponDiscount = $paid->paidInstallment->coupon_discount != null ? $paid->paidInstallment->coupon_discount : "0.000";

                    $installmentAmount[] = $i + 1 . "- Installment 0$paid->installment_no: Total Amount: " . $paid->amount . ' => Paid Amount: ' . $paid->paidInstallment->total_amount . ' => Coupon Discount: ' . $couponDiscount;
                } else {
                    $order = Order::withTrashed()->find($paid->id);
                    $orderItems[] = '[DELETED]' . $i + 1 . "- $itemName" . $order->title . " => Instructor: " . $order->instructor->fname . ' ' . $order->instructor->lname;
                    $couponDiscount = $paid->paidInstallment->coupon_discount != null ? $paid->paidInstallment->coupon_discount : "0.000";

                    $installmentAmount[] = '[DELETED]' . $i + 1 . "- Installment 0$paid->installment_no: Total Amount: " . $paid->amount . ' => Paid Amount: ' . $paid->paidInstallment->total_amount . ' => Coupon Discount: ' . $couponDiscount;
                }
            }
            $amount[] = implode("\n", $installmentAmount);
        } else {
            if ($transaction->orders->isNotEmpty()) {
                foreach ($transaction->orders as $key => $order) {
                    $itemName = $order->course_id ? "Course: " : ($order->bundle_id ? "Package: " : ($order->chapter_id ? "Chapter: " : ($order->meeting_id ? "Live Streaming: " : ($order->offline_session_id ? "In-Person Session: " : ""))));
                    $orderItems[] = $key + 1 . "- $itemName" . $order->title . " => Instructor: " . $order->instructor->fname . ' ' . $order->instructor->lname;

                    if ($order->installments == 1) {
                        $installmentAmount = [];
                        foreach ($order->payment_plan as $j => $plan) {
                            $j += 1;
                            $paidInstallment = ($plan->order_installment_id && $plan->wallet_trans_id == $transaction->id) ? $plan->paidInstallment : null;
                            $paidAmount = $paidInstallment ? $paidInstallment->total_amount : 'Not Paid';
                            $couponDiscount = ($paidInstallment && $paidInstallment->coupon_discount != null) ? $paidInstallment->coupon_discount : "0.000";
                            $installmentAmount[] = $key + 1 . "- Installment 0$j: Total Amount: " . $plan->amount . ' => Paid Amount: ' . $paidAmount . ' => Coupon Discount: ' . $couponDiscount;
                        }

                        $amount[] = implode("\n", $installmentAmount);
                    } else {
                        $couponDiscount = $order->coupon_discount != null ? $order->coupon_discount : "0.000";
                        $amount[] = $key + 1 . "- Total Amount: " . $order->total_amount . " => Paid Amount: " . $order->paid_amount . " => Coupon Discount: " . $couponDiscount;
                    }
                }
            } else {
                $orders = Order::withTrashed()->where('transaction_id', $transaction->id)->get();
                foreach ($orders as $key => $order) {
                    $itemName = $order->course_id ? "Course: " : ($order->bundle_id ? "Package: " : ($order->chapter_id ? "Chapter: " : ($order->meeting_id ? "Live Streaming: " : ($order->offline_session_id ? "In-Person Session: " : ""))));
                    $orderItems[] = '[DELETED]' . $key + 1 . "- $itemName" . $order->title . " => Instructor: " . $order->instructor->fname . ' ' . $order->instructor->lname;

                    if ($order->installments == 1) {
                        $installmentAmount = [];
                        foreach ($order->payment_plan as $j => $plan) {
                            $j += 1;
                            $paidInstallment = ($plan->order_installment_id && $plan->wallet_trans_id == $transaction->id) ? $plan->paidInstallment : null;
                            $paidAmount = $paidInstallment ? $paidInstallment->total_amount : 'Not Paid';
                            $couponDiscount = ($paidInstallment && $paidInstallment->coupon_discount != null) ? $paidInstallment->coupon_discount : "0.000";
                            $installmentAmount[] = '[DELETED]' . $key + 1 . "- Installment 0$j: Total Amount: " . $plan->amount . ' => Paid Amount: ' . $paidAmount . ' => Coupon Discount: ' . $couponDiscount;
                        }

                        $amount[] = implode("\n", $installmentAmount);
                    } else {
                        $couponDiscount = $order->coupon_discount != null ? $order->coupon_discount : "0.000";
                        $amount[] = '[DELETED]' . $key + 1 . "- Total Amount: " . $order->total_amount . " => Paid Amount: " . $order->paid_amount . " => Coupon Discount: " . $couponDiscount;
                    }
                }
            }
        }

        return [
            [
                $transaction->user_id,
                $transaction->user->fname,
                $transaction->user->lname,
                $transaction->user->email,
                $transaction->user->mobile,
                $transaction->type,
                $transaction->total_amount,
                $transaction->payment_method,
                $transaction->detail,
                $transaction->reason,
                getUserTimeZoneDateTime($transaction->created_at->toDateTimeString()),
                implode("\n", $orderItems),
                implode("\n\n", $amount),
            ],
        ];
    }


    public function query()
    {
        $query = WalletTransactions::query()
            ->select('id', 'user_id', 'type', 'total_amount', 'payment_method', 'detail', 'reason', 'created_at')
            ->where(strtolower('payment_method'), '<>', 'manual enrollment')
            ->whereHas('user', function ($query) {
                $query->exceptTestUser();
            })
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'fname', 'lname', 'email', 'mobile');
                },
                'orders' => function ($query) {
                    $query->select('id', 'course_id', 'chapter_id', 'bundle_id', 'meeting_id', 'offline_session_id', 'transaction_id', 'title', 'instructor_id', 'installments', 'total_amount', 'paid_amount', 'coupon_id', 'coupon_discount')
                        ->with([
                            'instructor' => function ($query) {
                                $query->select('id', 'fname', 'lname');
                            },
                            'payment_plan' => function ($query) {
                                $query->with('paidInstallment');
                            }
                        ]);
                },
                'paidPaymentPlans' => function ($query) {
                    $query->with(['paidInstallment', 'order']);
                },
            ])
            ->latest('id');


        if ($this->transaction_type) {
            $query->where('type', $this->transaction_type);
        }
        if ($this->credit_type) {
            $query->where('detail', $this->credit_type);
        } elseif ($this->debit_type) {
            $query->where('detail', 'like', "%{$this->debit_type}%");
        }

        if ($this->from_date && $this->to_date) {
            $startDate = Carbon::createFromFormat('Y-m-d', $this->from_date)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $this->to_date)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($this->from_date) {
            $query->where('created_at', '>=', $this->from_date);
        } elseif ($this->to_date) {
            $query->where('created_at', '<=', $this->to_date);
        }

        return $query;
    }
}