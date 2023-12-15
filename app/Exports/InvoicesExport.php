<?php

namespace App\Exports;

use App\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromQuery;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvoicesExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    use Exportable;

    private $instructor_id, $type, $type_ids, $installments, $payment_status, $installment_no, $from_date, $to_date;

    private $fileName = 'invoices.xlsx';
    private $writerType = Excel::XLSX;


    private $headers = [
        'Content-Type' => 'text/csv',
    ];


    public function __construct($data)
    {
        $this->instructor_id = $data['instructor_id'];
        $this->type = $data['type'];
        $this->type_ids = $data['type_ids'] ?? null;
        $this->installments = $data['installments'];
        $this->payment_status = $data['payment_status'];
        $this->installment_no = $data['installment_no'];
        $this->from_date = $data['from_date'];
        $this->to_date = $data['to_date'];
    }

    public function headings(): array
    {
        return [
            'Order ID', 'College', 'Major', 'User ID', 'First Name', 'Last Name', 'Email', 'Phone Number', 'User Enrollment',
            'Instructor Name', 'Order Title', 'Order Type', 'Linked to Course', 'Start Date', 'End Date', 'Total Amount', 'Paid Amount', 'Coupon Discount',
            'Manual Enrollment', 'Manual Enrollment Date',
            'Payment Type',
            '1 Installment', '1 Installment Coupon Discount', '1 Installment Status', '1 Installment Paid',
            '2 Installment', '2 Installment Coupon Discount', '2 Installment Status', '2 Installment Paid',
            '3 Installment', '3 Installment Coupon Discount', '3 Installment Status', '3 Installment Paid',
            '4 Installment', '4 Installment Coupon Discount', '4 Installment Status', '4 Installment Paid',
            'Payment Status', 'Payment Method', 'Due Date', 'Payment Date', 'Wallet Topup', 'Topup Type', 'Topup Reason'
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {

                $columnIndexes = ['B', 'C', 'S', 'T',  'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK']; // Array of column indexes to apply the style to

                $style = [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'D8E4BC', // Yellow color
                        ],
                    ]
                ];

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'], // Border color (black)
                        ],
                    ],
                ];
    
                $sheet = $event->sheet;

                foreach ($columnIndexes as $column) {
                    $range = $column . '1:' . $column . $sheet->getHighestRow();
                    $sheet->getStyle($range)->applyFromArray($style);
                }

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $range = 'A1:' . $highestColumn . $highestRow;
                $sheet->getStyle($range)->applyFromArray($border);

                $cellRange = 'A1:AR1'; // All headers

                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12)->setBold(true);
            },
        ];
    }

    public function map($order): array
    {
        $installment1 = '';
        $installment1CouponDiscount = '';
        $installment1Status = '';
        $installment1Date = '';
        $installment2 = '';
        $installment2CouponDiscount = '';
        $installment2Status = '';
        $installment2Date = '';
        $installment3 = '';
        $installment3CouponDiscount = '';
        $installment3Status = '';
        $installment3Date = '';
        $installment4 = '';
        $installment4CouponDiscount = '';
        $installment4Status = '';
        $installment4Date = '';

        if ($order->installments == 1) {
            foreach ($order->payment_plan as $key => $plan) {
                $key += 1;
                $paymentStatus[] = "Installment 0$key: " . $plan->amount . '|' . $plan->status ?? 'Not Paid';
                $paymentMethod[] = "Installment 0$key: " . $plan->order_installment->payment_method;
                $paymentDueDate[] = "Installment 0$key: " . $plan->due_date;
                $paymentDate[] = "Installment 0$key: " . $plan->payment_date;

                if ($plan->installment_no == 1) {
                    $installment1 = $plan->order_installment_id ? ($plan->wallet_trans_id  == $order->payment_plan[1]->wallet_trans_id ? ($plan->amount - $plan->paidInstallment->coupon_discount) : $plan->paidInstallment->total_amount) : $plan->amount;
                    $installment1CouponDiscount = $plan->order_installment_id ? $plan->paidInstallment->coupon_discount : '0';
                    $installment1Status = $plan->status ?? 'Not Paid';
                    $installment1Date = $plan->payment_date ?? $plan->due_date;
                } elseif ($plan->installment_no == 2) {
                    $installment2 = $plan->order_installment_id ? $plan->paidInstallment->total_amount : ($plan->wallet_trans_id  == $order->payment_plan[0]->wallet_trans_id ? $plan->amount : $plan->amount);
                    $installment2CouponDiscount = $plan->order_installment_id ? $plan->paidInstallment->coupon_discount : '0';
                    $installment2Status = $plan->status ?? 'Not Paid';
                    $installment2Date = $plan->payment_date ?? $plan->due_date;
                } elseif ($plan->installment_no == 3) {
                    $installment3 = $plan->order_installment_id ? $plan->paidInstallment->total_amount : $plan->amount;
                    $installment3CouponDiscount = $plan->order_installment_id ? $plan->paidInstallment->coupon_discount : '0';
                    $installment3Status = $plan->status ?? 'Not Paid';
                    $installment3Date = $plan->payment_date ?? $plan->due_date;
                } elseif ($plan->installment_no == 4) {
                    $installment4 = $plan->order_installment_id ? $plan->paidInstallment->total_amount : $plan->amount;
                    $installment4CouponDiscount = $plan->order_installment_id ? $plan->paidInstallment->coupon_discount : '0';
                    $installment4Status = $plan->status ?? 'Not Paid';
                    $installment4Date = $plan->payment_date ?? $plan->due_date;
                }
            }
        }

        return [
            [
                $order->id,
                ucwords($order->user->institute, '-'),
                ucwords($order->user->major, '-'),
                $order->user_id,
                $order->user->fname,
                $order->user->lname,
                $order->user->email,
                $order->user->mobile,
                getUserTimeZoneDateTime($order->created_at->toDateTimeString()),
                $order->instructor->full_name,
                $order->title,
                $order->course_id ? 'course' : ($order->chapter_id ? 'chapter' : ($order->bundle_id ? 'package' : ($order->meeting_id ? 'live streaming' : ($order->offline_session_id ? 'in-person session' : '')))),
                $order->chapter_id ? $order->chapter->courses->title : '',
                $order->enroll_start,
                $order->enroll_expire,
                $order->total_amount,
                $order->paid_amount,
                $order->installments == 1 ? $order->installments_list->sum('coupon_discount') : $order->coupon_discount,
                $order->installments == 0 && strtolower($order->payment_method) == 'manual enrollment' ? $order->payment_method : '',
                $order->installments == 0 && strtolower($order->payment_method) == 'manual enrollment' ? getUserTimeZoneDateTime($order->created_at->toDateTimeString()) : '',
                $order->installments == 1 ? 'installments' : 'full',
                $order->installments == 1 ? $installment1 : '',
                $order->installments == 1 ? $installment1CouponDiscount : '',
                $order->installments == 1 ? $installment1Status : '',
                $order->installments == 1 ? $installment1Date : '',
                $order->installments == 1 ? $installment2 : '',
                $order->installments == 1 ? $installment2CouponDiscount : '',
                $order->installments == 1 ? $installment2Status : '',
                $order->installments == 1 ? $installment2Date : '',
                $order->installments == 1 ? $installment3 : '',
                $order->installments == 1 ? $installment3CouponDiscount : '',
                $order->installments == 1 ? $installment3Status : '',
                $order->installments == 1 ? $installment3Date : '',
                $order->installments == 1 ? $installment4 : '',
                $order->installments == 1 ? $installment4CouponDiscount : '',
                $order->installments == 1 ? $installment4Status : '',
                $order->installments == 1 ? $installment4Date : '',
                $order->installments == 1 ? implode(PHP_EOL, $paymentStatus) : 'Paid',
                $order->installments == 1 ? implode(PHP_EOL, $paymentMethod) : $order->payment_method,
                $order->installments == 1 ? implode(PHP_EOL, $paymentDueDate) : '',
                $order->installments == 1 ? implode(PHP_EOL, $paymentDate) : getUserTimeZoneDateTime($order->transaction->created_at->toDateTimeString()),
                $order->user->topup_transactions ? (date('Y-m-d', strtotime($order->user->topup_transactions->created_at)) == date('Y-m-d', strtotime($order->updated_at))  ? $order->user->topup_transactions->total_amount : '') : '',
                $order->user->topup_transactions ? (date('Y-m-d', strtotime($order->user->topup_transactions->created_at)) == date('Y-m-d', strtotime($order->updated_at))  ? $order->user->topup_transactions->type : '') : '',
                $order->user->topup_transactions ? (date('Y-m-d', strtotime($order->user->topup_transactions->created_at)) == date('Y-m-d', strtotime($order->updated_at))  ? $order->user->topup_transactions->reason : '') : '',
            ],
        ];
    }

    public function query()
    {
        $query = Order::query()
            ->select('id', 'title', 'user_id', 'instructor_id', 'course_id', 'chapter_id', 'bundle_id', 'meeting_id', 'offline_session_id', 'installments', 'total_amount', 'paid_amount', 'coupon_discount', 'transaction_id', 'payment_method', 'enroll_start', 'enroll_expire', 'created_at', 'updated_at')
            ->allActiveInactiveOrder()
            ->whereHas('user', function ($q) {
                $q->exceptTestUser();
            })
            ->where(
                DB::raw("case when `orders`.installments != 0 then exists(select * from order_payment_plan as pp where `orders`.id = pp.order_id and ((pp.payment_date between '$this->from_date' and '$this->to_date') or (pp.due_date between date('$this->from_date') and date('$this->to_date'))) )
                                    else `orders`.created_at between '$this->from_date' and '$this->to_date' end"),
                DB::raw('1')
            )
            ->with('user:id,fname,lname,email,mobile,institute,major')
            ->with('instructor:id,fname,lname')
            ->with('transaction:id,payment_method,created_at')
            ->with('payment_plan:id,order_id,order_installment_id,due_date,installment_no,payment_date,amount,status,wallet_trans_id')
            ->with('installments_list:id,order_id,payment_method,coupon_discount,total_amount')
            ->latest('id');


        if ($this->instructor_id) {
            $query->where('instructor_id', $this->instructor_id);
        }
        if ($this->type) {
            $query->whereNotNull($this->type);
        }
        if ($this->type && $this->type_ids) {
            if ($this->type == 'course_id') {
                $query->whereIn('course_id', $this->type_ids);
            } elseif ($this->type == 'chapter_id') {
                $query->whereIn('chapter_id', $this->type_ids);
            } elseif ($this->type == 'bundle_id') {
                $query->whereIn('bundle_id', $this->type_ids);
            } elseif ($this->type == 'meeting_id') {
                $query->whereIn('meeting_id', $this->type_ids);
            } elseif ($this->type == 'offline_session_id') {
                $query->whereIn('offline_session_id', $this->type_ids);
            }
        }


        if ($this->installments && $this->installments == 0) {
            $query->where('installments', 0);
        } elseif ($this->installments && $this->installments == 1) {
            $query->where('installments', 1);
        }


        if ($this->installment_no == null && $this->payment_status == 'paid') {
            $query->whereRaw('orders.total_amount = (orders.paid_amount+orders.coupon_discount)')
                ->where(
                    DB::raw("case when `orders`.installments != 0 then exists (select * from order_payment_plan as pp where `orders`.id = pp.order_id and ((pp.payment_date between '$this->from_date' and '$this->to_date')) )
                                else `orders`.created_at between '$this->from_date' and '$this->to_date' end"),
                    DB::raw('1')
                );
        } elseif ($this->installment_no == null && $this->payment_status == 'unpaid') {
            $query->whereRaw('orders.total_amount <> (orders.paid_amount+orders.coupon_discount)')
                ->whereHas('payment_plan', function ($q) {
                    $q->whereNull('payment_date')->whereBetween('due_date', [$this->from_date, $this->to_date]);
                });
        }


        if ($this->installments == '1' && $this->installment_no <> null && $this->payment_status == null) {
            $query->whereHas('payment_plan', function ($q) {
                $q->where('installment_no', $this->installment_no)
                    ->where(function ($query) {
                        $query->whereBetween('due_date', [$this->from_date, $this->to_date])
                            ->orWhereBetween('payment_date', [$this->from_date, $this->to_date]);
                    });
            });
        }


        if ($this->installments == 1 && $this->installment_no == null && $this->payment_status == 'unpaid') {
            $query->whereHas('payment_plan', function ($q) {
                $q->where('installment_no', 1)->whereNull('payment_date');
            });
        } elseif ($this->installments == 1 && $this->installment_no <> null && $this->payment_status == 'unpaid') {
            $query->whereHas('payment_plan', function ($q) {
                $q->where('installment_no', $this->installment_no)->whereNull('payment_date')->whereBetween('due_date', [$this->from_date, $this->to_date]);;
            });
        }


        if ($this->installments == 1 && $this->installment_no == null && $this->payment_status == 'paid') {
            $query->whereHas('payment_plan', function ($q) {
                $q->whereBetween('payment_date', [$this->from_date, $this->to_date]);
            });
        } elseif ($this->installments == 1 && $this->installment_no <> null && $this->payment_status == 'paid') {
            $query->whereHas('payment_plan', function ($q) {
                $q->where('installment_no', $this->installment_no)->whereBetween('payment_date', [$this->from_date, $this->to_date]);
            });
        }

        return $query;
    }
}
