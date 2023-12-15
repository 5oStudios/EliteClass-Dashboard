<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\WalletSettings;
use App\WalletTransactions;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Exports\TransactionsExport;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class WalletSettingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | WalletSettingController
    |--------------------------------------------------------------------------
    |
    | This controller holds the logics and functionality of wallet settings.
    |
    */

    public function __construct()
    {
        $this->middleware('permission:wallet-setting.manage', ['only' => ['index', 'update']]);
        $this->middleware('permission:wallet-transactions.manage', ['only' => ['transactions', 'exportTransactions']]);
    }


    public function index()
    {
        $settings = WalletSettings::first();
        $wallet_transactions = WalletTransactions::get();

        return view('admin.wallet.index', compact('settings', 'wallet_transactions'));
    }


    /**
     * This function holds the funncality to update wallet settings.
     */

    public function update(Request $request)
    {
        try {

            /** Get the wallet settings */
            $settings = WalletSettings::first();
            $input = $request->all();

            if ($settings) {
                if (!isset($input['status'])) {
                    $input['status'] = 0;
                } else {
                    $input['status'] = 1;
                }

                if (!isset($input['paytm_enable'])) {
                    $input['paytm_enable'] = 0;
                } else {
                    $input['paytm_enable'] = 1;
                }

                if (!isset($input['paypal_enable'])) {
                    $input['paypal_enable'] = 0;
                } else {
                    $input['paypal_enable'] = 1;
                }

                if (!isset($input['stripe_enable'])) {
                    $input['stripe_enable'] = 0;
                } else {
                    $input['stripe_enable'] = 1;
                }

                $settings->update($input);
            } else {
                /** Create new wallet settings if not exist */
                $settings = new WalletSettings();

                if (!isset($input['status'])) {
                    $input['status'] = 0;
                } else {
                    $input['status'] = 1;
                }

                if (!isset($input['paytm_enable'])) {
                    $input['paytm_enable'] = 0;
                } else {
                    $input['paytm_enable'] = 1;
                }

                if (!isset($input['paypal_enable'])) {
                    $input['paypal_enable'] = 0;
                } else {
                    $input['paypal_enable'] = 1;
                }

                if (!isset($input['stripe_enable'])) {
                    $input['stripe_enable'] = 0;
                } else {
                    $input['stripe_enable'] = 1;
                }

                $settings->create($input);
            }

            Session::flash('success', __('flash.UpdatedSuccessfully'));
            return back()->with('success', __('Saved successfully'));
        } catch (\Exception $e) {

            /** Catch the error and @return back to previous location with error message */
            Session::flash('delete', $e->getMessage());
            return back();
        }
    }


    /**
     * This function holds the funncality to get all wallet transcations.
     */
    public function transactions(Request $request)
    {
        $transactions = WalletTransactions::query()
            ->select('id', 'user_id', 'type', 'total_amount', 'payment_method', 'detail', 'created_at')
            ->where(strtolower('payment_method'), '<>', 'manual enrollment')
            ->whereHas('user', function ($query) {
                $query->exceptTestUser();
            })
            ->with('user', function ($query) {
                $query->select('id', 'fname', 'lname', 'mobile');
            });

        if ($request->ajax()) {
            return Datatables::of($transactions)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($request->get('transaction_type') <> null) {
                        $query->where('type', $request->transaction_type);
                    }

                    if ($request->get('transaction_type') == 'Credit' && $request->get('credit_type') <> null) {
                        $query->where('detail', $request->credit_type);
                    } elseif ($request->get('transaction_type') == 'Debit' && $request->get('debit_type') <> null) {
                        $query->where('detail', 'like', "%{$request->debit_type}%");
                    }

                    if ($request->get('to_date') <> null && $request->get('from_date')) {
                        $startDate = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
                        $endDate = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();

                        $query->whereBetween('wallet_transactions.created_at', [$startDate, $endDate]);
                    } elseif ($request->get('from_date') <> null) {
                        $query->where('wallet_transactions.created_at', '>=', $request->from_date);
                    } elseif ($request->get('to_date') <> null) {
                        $query->where('wallet_transactions.created_at', '<=', $request->to_date);
                    }
                })
                ->editColumn('payment_method', function ($query) {
                    return $query->payment_method ?? __('N/A');
                })
                ->editColumn('created_at', function ($query) {
                    return getUserTimeZoneDateTime(($query->created_at));
                })
                ->editColumn('action', 'admin.wallet.datatables.action')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.wallet.transactions', compact('transactions'));
    }


    public function exportTransactions(Request $request)
    {
        $request = $request->except('_token');

        ob_end_clean();
        ob_start();

        return (new TransactionsExport($request))->download();
    }
}