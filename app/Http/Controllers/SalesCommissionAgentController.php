<?php

namespace App\Http\Controllers;

use App\CommissionPayment;
use App\User;
use App\Utils\Util;
use DataTables;
use DB;
use App\Utils\TransactionUtil;
use App\Utils\ProductUtil;
use Illuminate\Http\Request;
use App\Transaction;

class SalesCommissionAgentController extends Controller
{
     protected $transactionUtil;
    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('user.view') && ! auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $users = User::where('business_id', $business_id)
                    ->where('is_cmmsn_agnt', 1)
                    ->withSum('commissionAgentInvoice', 'final_total')
                    ->withCount([
                        'commissionAgentInvoice as total_payment' => function ($query) {
                            $query->join('commission_payments', 'transactions.id', '=', 'commission_payments.transaction_id')
                                ->select(DB::raw('SUM(commission_payments.amount)'));
                        },
                    ])
                        ->addSelect(['id',
                            DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                            'email', 'contact_no', 'address', 'cmmsn_percent' ]);
            return Datatables::of($users)
                ->addColumn(
                    'action',
                    '@can("user.update")
                    <button type="button" data-href="{{action(\'App\Http\Controllers\SalesCommissionAgentController@edit\', [$id])}}" data-container=".commission_agent_modal" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  btn-modal tw-dw-btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp;
                    @endcan
                    @can("user.delete")
                    <button data-href="{{action(\'App\Http\Controllers\SalesCommissionAgentController@destroy\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_commsn_agnt_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan
                    <a href="{{action(\'App\Http\Controllers\SalesCommissionAgentController@invoice\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent"><i class="fa fa-eye"></i>Invoice</a>'
                )->addColumn('sales_commission',function ($query){
                    return $query->commission_agent_invoice_sum_final_total * ($query->cmmsn_percent / 100);
                })
                ->addColumn('balance',function ($query){
                    return (($query->commission_agent_invoice_sum_final_total ?? 0) * ($query->cmmsn_percent / 100)) - $query->total_payment;
                })
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->editColumn(
                    'commission_agent_invoice_sum_final_total',
                    '@format_currency($commission_agent_invoice_sum_final_total)'
                )
                ->editColumn(
                    'sales_commission',
                    '@format_currency($sales_commission)'
                )
                ->editColumn(
                    'total_payment',
                    '@format_currency($total_payment)'
                )
                ->editColumn(
                    'balance',
                    '@format_currency($balance)'
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('sales_commission_agent.index');
    }

    public function invoice( $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $user = User::where('business_id', $business_id)->findOrFail($id);
        if (empty($user)){
            abort(403, 'User not found.');
        }
        if (\request()->ajax()){
            $total_commission_percent = $user->cmmsn_percent;
            $transactions = Transaction::withSum('commissionPayments','amount')
                ->where('business_id', $business_id)
                ->where('commission_agent', $id);
            return Datatables::of($transactions)
                ->addColumn('total_commission',function ($query) use($total_commission_percent){
                    return $query->final_total * ($total_commission_percent / 100);
                })
                ->addColumn('sales_commission_percentage',function ($query) use($user){
                    return $user->cmmsn_percent;
                })
                ->addColumn('total_payment',function ($query) use($user){
                    return $query->commission_payments_sum_amount ?? 0;
                })
                ->addColumn('total_balance', function ($query) use ($total_commission_percent) {
                    $total_commission = $query->final_total * ($total_commission_percent / 100);
                    $total_payment = $query->commission_payments_sum_amount ?? 0;
                    return $total_commission - $total_payment;
                })
                ->addColumn('action', function ($query) use ($total_commission_percent){
                    $total_commission = $query->final_total * ($total_commission_percent / 100);
                    $total_payment = $query->commission_payments_sum_amount ?? 0;
                    $balance =  $total_commission - $total_payment;
                    $html = '';
                    if ($balance > 0){
                        $html .='<a href="#" data-max="'.$balance.'" data-id="'.$query->id.'" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline add-payment tw-dw-btn-accent"><i class="fa fa-plus"></i>Add Payment</a>';
                    }
                    $html .='<a href="#"  data-id="'.$query->id.'" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline print-payment  tw-dw-btn-warning"><i class="fa fa-print"></i>Print Payment</a>';
                    $html .='<a href="#"  data-id="'.$query->id.'" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline view-payment  tw-dw-btn-primary"><i class="fa fa-eye"></i>View Payment</a>';
                    return $html;
                })->editColumn("invoice_no", function ($row) {
                    return '<a data-href="' .
                        action(
                            [\App\Http\Controllers\SellController::class, "show"],
                            [$row->id]
                        ) .
                        '" href="#" data-container=".view_modal" class="btn-modal">' .
                        $row->invoice_no .
                        "</a>";
                })
                ->editColumn('final_total','@format_currency($final_total)')
                ->editColumn('total_commission','@format_currency($total_commission)')
                ->editColumn('total_payment','@format_currency($total_payment)')
                ->editColumn('total_balance','@format_currency($total_balance)')
                ->removeColumn('id')
                ->rawColumns(['invoice_no','action'])
                ->make(true);
        }

        return view('sales_commission_agent.invoice')
            ->with(['user'=>$user]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('sales_commission_agent.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['surname', 'first_name', 'last_name', 'email', 'address', 'contact_no', 'cmmsn_percent']);
            $input['cmmsn_percent'] = $this->commonUtil->num_uf($input['cmmsn_percent']);
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input['allow_login'] = 0;
            $input['is_cmmsn_agnt'] = 1;

            $user = User::create($input);

            $output = ['success' => true,
                'msg' => __('lang_v1.commission_agent_added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        return view('sales_commission_agent.edit')
                    ->with(compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['surname', 'first_name', 'last_name', 'email', 'address', 'contact_no', 'cmmsn_percent']);
                $input['cmmsn_percent'] = $this->commonUtil->num_uf($input['cmmsn_percent']);
                $business_id = $request->session()->get('user.business_id');

                $user = User::where('id', $id)
                            ->where('business_id', $business_id)
                            ->where('is_cmmsn_agnt', 1)
                            ->first();
                $user->update($input);

                $output = ['success' => true,
                    'msg' => __('lang_v1.commission_agent_updated_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                User::where('id', $id)
                    ->where('business_id', $business_id)
                    ->where('is_cmmsn_agnt', 1)
                    ->delete();

                $output = ['success' => true,
                    'msg' => __('lang_v1.commission_agent_deleted_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function viewInvoicePayment(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $commissionPayments = CommissionPayment::where('business_id', $business_id)
            ->where('transaction_id', $request->id)
            ->where('user_id',$request->user_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $html = '';
        if ($commissionPayments->isNotEmpty()) {
            foreach ($commissionPayments as $commissionPayment) {
                $html .= '<tr>';
                $html .= '<td>' . $commissionPayment->paid_on . '</td>';
                $html .= '<td>php ' . $commissionPayment->amount. '</td>';
                $html .= '<td>' . $commissionPayment->method . '</td>';
                $html .= '<td>' . $commissionPayment->note . '</td>';
                $html .= '<td>
                            <a href="#" data-update="'.route('invoice.update-payment',['id'=>$commissionPayment->id]).'" data-href="'.route('invoice.edit-payment',['id'=>$commissionPayment->id]).'" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  edit-payment tw-dw-btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                            <a href="#" data-href="'.route('invoice.delete-payment',['id'=>$commissionPayment->id]).'" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_payment_button"><i class="glyphicon glyphicon-trash"></i> Delete</a>
                        </td>';
                $html .='</tr>';
            }
        }else{
            $html .='<tr><td colspan="3">No record found</td></tr>';
        }
        return response()->json(['html' => $html]);
    }

    public function editPayment($id)
    {
        $commissionPayment = CommissionPayment::findOrFail($id);
        if (empty($commissionPayment)){
            return response()->json([
                'success' => false,
                'message' => __('Payment not found'),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $commissionPayment,
        ]);
    }

    public function updatePayment($id, Request $request)
    {
        $commissionPayment = CommissionPayment::findOrFail($id);
        if (empty($commissionPayment)){
            return response()->json([
                'success' => false,
                'message' => __('Payment not found'),
            ]);
        }
        $transaction = Transaction::with(['sale_commission_agent'])
            ->withSum('commissionPayments','amount')
            ->whereHas('sale_commission_agent')
            ->where('business_id', $commissionPayment->business_id)
            ->where('id',$commissionPayment->transaction_id)
            ->first();
        if (empty($transaction)){
            return response()->json([
                'success' => false,
                'msg' => __('Invoice not found.'),
            ]);
        }
        $total_commission = $transaction->final_total * ($transaction->sale_commission_agent->cmmsn_percent / 100);
        $totalPayment = CommissionPayment::where('transaction_id',$transaction->id)->where('id','!=',$commissionPayment->id)->get()->sum('amount');
        $balance =  $total_commission - $totalPayment;
        if (number_format($balance, 2, '.', '') < number_format((float)$request->amount, 2, '.', '')){
            return response()->json([
                'success' => false,
                'msg' => __('Due balance is '.number_format($balance, 2, '.', '')),
            ]);
        }

        $commissionPayment->amount = number_format((float)$request->amount, 2, '.', '');
        $commissionPayment->paid_on = \Carbon::parse($request->paid_on)->format('Y-m-d H:i:s');
        $commissionPayment->method = $request->method;
        $commissionPayment->note = $request->note;
        if ($commissionPayment->save()){
            return response()->json([
                'success' => true,
                'msg' => __('Commission payment update successfully.'),
            ]);
        }else{
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ]);
        }
    }

    public function updateCommissionById(Request $request)
    {
        $user = User::findOrFail($request->userId);
        if (empty($user)){
            return response()->json([
                'success' => false,
                'message' => __('User not found'),
            ]);
        }

        $user->cmmsn_percent = (float) $request->value;
        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Sales commission updated successfully!'),
            'percentage' => $request->value,
        ]);
    }
    public function deletePayment($id)
    {
        $commissionPayment = CommissionPayment::findOrFail($id);
        if (empty($commissionPayment)){
            return response()->json([
                'success' => false,
                'message' => __('Payment not found'),
            ]);
        }
        $commissionPayment->delete();
        return response()->json([
            'success' => true,
            'message' => __('Payment deleted successfully'),
        ]);
    }
    public function invoiceAddPayment(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        if (number_format((float)$request->amount, 2, '.', '') <= 0){
            return  response()->json([
                'success' => false,
                'msg' => __('Please enter amount greater than 0.'),
            ]);
        }
        $transaction = Transaction::with(['sale_commission_agent'])
            ->withSum('commissionPayments','amount')
            ->whereHas('sale_commission_agent')
            ->where('business_id', $business_id)
            ->where('id',$request->transaction_id)
            ->first();
        if (empty($transaction)){
            return response()->json([
                'success' => false,
                'msg' => __('Invoice not found.'),
            ]);
        }
        $total_commission = $transaction->final_total * ($transaction->sale_commission_agent->cmmsn_percent / 100);
        $total_payment = $transaction->commission_payments_sum_amount ?? 0;
        $balance =  $total_commission - $total_payment;
        if (number_format($balance, 2, '.', '') < number_format((float)$request->amount, 2, '.', '')){
            return response()->json([
                'success' => false,
                'msg' => __('Due balance is '.number_format($balance, 2, '.', '')),
            ]);
        }
        $commissionPayment = new CommissionPayment();
        $commissionPayment->transaction_id = $request->transaction_id;
        $commissionPayment->business_id = $business_id;
        $commissionPayment->user_id = $request->user_id;
        $commissionPayment->note = $request->note;
        $commissionPayment->amount = number_format((float)$request->amount, 2, '.', '');
        $commissionPayment->paid_on = \Carbon::parse($request->paid_on)->format('Y-m-d H:i:s');
        $commissionPayment->method = $request->method;
        if ($commissionPayment->save()){
            return response()->json([
                'success' => true,
                'msg' => __('Commission payment added successfully.'),
            ]);
        }else{
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ]);
        }
    }
}
