@extends('layouts.app')
@section('title', __('Sales Commission Agent Invoice'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Sales Commission Agent Invoice
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary'])
            @can('user.create')
                @slot('tool')
                    <div class="box-tools">
                        <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal pull-right"
                           data-href="{{action([\App\Http\Controllers\SalesCommissionAgentController::class, 'create'])}}" data-container=".commission_agent_modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> @lang('messages.add')
                        </a>
                    </div>
                @endslot
            @endcan
            @can('user.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="sales_commission_agent_table_invoice">
                        <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Total Invoice</th>
                            <th>Sales Commission Percentage(%)</th>
                            <th>Total Commission</th>
                            <th>Total Payment</th>
                            <th>Total Balance</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent
        <div class="modal fade" id="add-payment-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{route('invoice-add-payment')}}" method="POST" id="payment-form">
                        @csrf
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Add new Payment</h4>
                        </div>
                        <div class="modal-body" id="append-add-payment">

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">Save</button>
                            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="edit-payment-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="" method="POST" id="edit-payment-form">
                        @csrf
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Add new Payment</h4>
                        </div>
                        <div class="modal-body" id="edit-add-payment">

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">Save</button>
                            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="view-payment-modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">View Payment</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Paid On</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Note</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="payment-table-body">

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
@section('javascript')
    <script>
        var id = '{{$user->id}}';
        var userId = '{{$user->id}}';
        var maxPay = 0;
        $(document).ready( function(){
            initDatePicker();
            $(document).on('click','.view-payment',function(e){
                e.preventDefault();
                var id = $(this).data('id');
                $.ajax({
                    url:'{{route('invoice.view-payment')}}',
                    method:'POST',
                    data:{
                        '_token':'{{csrf_token()}}',
                        id:id,
                        user_id:userId,
                    },
                    success:function(response){
                        $('#payment-table-body').html(response.html);
                        $('#view-payment-modal').modal('show')
                    }
                })
            })
        });
        var sales_commission_agent_table = $('#sales_commission_agent_table_invoice').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader:false,
            ajax: '/sales-commission-agents/invoice/'+id,
            columns: [
                { data: 'invoice_no' },
                { data: 'final_total' },
                { data: 'sales_commission_percentage' },
                { data: 'total_commission' },
                { data: 'total_payment' },
                { data: 'total_balance' },
                { data: 'action' },
            ],
        });
        $(document).on('click','.add-payment',function(e){
            e.preventDefault();
            var id = $(this).data('id');
            maxPay = parseFloat($(this).data('max')).toFixed(2);
            $('#append-add-payment').html(`<div class="row">
                                    <input type="hidden" name="transaction_id" value="${id}">
                                    <input type="hidden" name="user_id" value="${userId}">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label for="amount_0">Amount:*</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fas fa-money-bill-alt"></i>
                                                </span>
                                                <input class="form-control payment-amount input_number" required="" id="amount" placeholder="Amount" name="amount" type="text" step="any" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label for="paid_on_0">Paid on:*</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input class="form-control paid_on" readonly="" required="" name="paid_on" type="text" value="12/04/2024 06:27" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label for="method_0">Payment Method:*</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i></span>
                                                <select class="form-control col-md-12 payment_types_dropdown" required="" id="method_0" style="width: 100%;" name="method">
                                                    <option value="cash" selected="selected">Cash</option>
                                                    <option value="card">Card</option>
                                                    <option value="cheque">Cheque</option>
                                                    <option value="bank_transfer">Bank Transfer</option>
                                                    <option value="other">Other</option>
                                                    <option value="custom_pay_1">Credit</option>
                                                    <option value="custom_pay_2">Custom Payment 2</option>
                                                    <option value="custom_pay_3">Custom Payment 3</option>
                                                    <option value="custom_pay_4">Custom Payment 4</option>
                                                    <option value="custom_pay_5">Custom Payment 5</option>
                                                    <option value="custom_pay_6">Custom Payment 6</option>
                                                    <option value="custom_pay_7">Custom Payment 7</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-12">
                                        <div class="form-group">
                                            <label for="paid_on_0">Note</label>
                                            <textarea class="form-control" name="note"></textarea>
                                        </div>
                                    </div>
                                </div>`)
            $('#add-payment-modal').modal('show')
            $('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
        });

        $('#add-payment-modal').on('shown.bs.modal', function () {
            // Ensure only one event binding occurs
            $('form#payment-form')
                .submit(function (e) {
                    e.preventDefault();
                })
                .validate({
                    rules: {
                        amount: {
                            required: true,
                            min: 0,
                        }
                    },
                    messages: {
                        amount: {
                            required: "The amount is required.",
                            min: "The amount must be greater than 0.",
                            max: "The amount must not exceed the maximum allowed."
                        }
                    },
                    submitHandler: function (form) {
                        var data = $(form).serialize();
                        $.ajax({
                            method: $(form).attr('method'),
                            url: $(form).attr('action'),
                            dataType: 'json',
                            data: data,
                            success: function (result) {
                                if (result.success) {
                                    $('#add-payment-modal').modal('hide');
                                    toastr.success(result.msg);
                                    $("form#payment-form").validate().resetForm();
                                    sales_commission_agent_table.ajax.reload();
                                    $('form#payment-form button').prop('disabled',false)
                                } else {
                                    toastr.error(result.msg);
                                    $('form#payment-form button').prop('disabled',false)
                                }
                            }
                        });
                    }
                });
        });
        $(document).on('click', '.delete_payment_button', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                sales_commission_agent_table.ajax.reload();
                                $('#view-payment-modal').modal('hide');
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        $(document).on('click','.edit-payment',function(e){
            e.preventDefault();
            $('#view-payment-modal').modal('hide');
            var href = $(this).data('href');
            var update = $(this).data('update')
            $.ajax({
                method: 'GET',
                url: href,
                success: function(result) {
                    if (result.success == true) {
                        var data = result.data;
                        $('#edit-payment-form').attr('action',update)
                        $('#edit-add-payment').html(`<div class="row">
                                    <input type="hidden" name="id" value="${data.id}">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label for="amount_0">Amount:*</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fas fa-money-bill-alt"></i>
                                                </span>
                                                <input class="form-control payment-amount input_number" required="" value="${parseFloat(data.amount).toFixed(2)}" id="amount" placeholder="Amount" name="amount" type="text" step="any" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label for="paid_on_0">Paid on:*</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input class="form-control paid_on" readonly="" required="" name="paid_on" type="text" value="${moment(data.paid_on).format('MM/DD/YYYY HH:mm')}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label for="method_0">Payment Method:*</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fas fa-money-bill-alt"></i></span>
                                                    <select class="form-control col-md-12 payment_types_dropdown" required="" id="method_0" style="width: 100%;" name="method">
                                                    <option value="cash" ${data.method === 'cash' ? 'selected' : ''}>Cash</option>
                                                    <option value="card" ${data.method === 'card' ? 'selected' : ''}>Card</option>
                                                    <option value="cheque" ${data.method === 'cheque' ? 'selected' : ''}>Cheque</option>
                                                    <option value="bank_transfer" ${data.method === 'bank_transfer' ? 'selected' : ''}>Bank Transfer</option>
                                                    <option value="other" ${data.method === 'other' ? 'selected' : ''}>Other</option>
                                                    <option value="custom_pay_1" ${data.method === 'custom_pay_1' ? 'selected' : ''}>Credit</option>
                                                    <option value="custom_pay_2" ${data.method === 'custom_pay_2' ? 'selected' : ''}>Custom Payment 2</option>
                                                    <option value="custom_pay_3" ${data.method === 'custom_pay_3' ? 'selected' : ''}>Custom Payment 3</option>
                                                    <option value="custom_pay_4" ${data.method === 'custom_pay_4' ? 'selected' : ''}>Custom Payment 4</option>
                                                    <option value="custom_pay_5" ${data.method === 'custom_pay_5' ? 'selected' : ''}>Custom Payment 5</option>
                                                    <option value="custom_pay_6" ${data.method === 'custom_pay_6' ? 'selected' : ''}>Custom Payment 6</option>
                                                    <option value="custom_pay_7" ${data.method === 'custom_pay_7' ? 'selected' : ''}>Custom Payment 7</option>
                                                    </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-12">
                                        <div class="form-group">
                                            <label for="paid_on_0">Note</label>
                                            <textarea class="form-control" name="note">${data.note}</textarea>
                                        </div>
                                    </div>
                                </div>`)
                        initDatePicker();
                        $('#edit-payment-modal').modal('show')
                    } else {
                        toastr.error(result.msg);
                    }
                    $('#edit-payment-form button').prop('disabled',false)
                },
            });
        });
        $('#edit-payment-form').on('submit',function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function (result) {
                    if (result.success) {
                        $('#edit-payment-modal').modal('hide');
                        toastr.success(result.msg);
                        sales_commission_agent_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                    $('#edit-payment-form button').prop('disabled',false)
                }
            });
        })
    </script>
    <script>
        function initDatePicker(){
            $('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
        }
    </script>
@endsection