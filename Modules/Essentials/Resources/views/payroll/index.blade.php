@extends('layouts.app')
@section('title', __('essentials::lang.payroll'))

@section('content')
@include('essentials::layouts.nav_hrm')
<section class="content-header">
    <h1>@lang('essentials::lang.payroll')
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#payroll_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fas fa-coins" aria-hidden="true"></i>
                            @lang('essentials::lang.all_payrolls')
                        </a>
                    </li>
                    @can('essentials.view_all_payroll')
                        <li>
                            <a href="#payroll_group_tab" data-toggle="tab" aria-expanded="true">
                                <i class="fas fa-layer-group" aria-hidden="true"></i>
                                @lang('essentials::lang.all_payroll_groups')
                            </a>
                        </li>
                    @endcan
                    @if(auth()->user()->can('essentials.view_allowance_and_deduction') || auth()->user()->can('essentials.add_allowance_and_deduction'))
                        <li>
                            <a href="#pay_component_tab" data-toggle="tab" aria-expanded="true">
                                <i class="fab fa-gg-circle" aria-hidden="true"></i>
                                @lang( 'essentials::lang.pay_components' )
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="payroll_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.filters', ['title' => __('report.filters'), 'class' => 'box-solid', 'closed' => true])
                                    @can('essentials.view_all_payroll')
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('user_id_filter', __('essentials::lang.employee') . ':') !!}
                                                {!! Form::select('user_id_filter', $employees, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('location_id_filter',  __('purchase.business_location') . ':') !!}

                                                {!! Form::select('location_id_filter', $locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('department_id', __('essentials::lang.department') . ':') !!}
                                                {!! Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('designation_id', __('essentials::lang.designation') . ':') !!}
                                                {!! Form::select('designation_id', $designations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                            </div>
                                        </div>
                                    @endcan
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('month_year_filter', __( 'essentials::lang.month_year' ) . ':') !!}
                                            <div class="input-group">
                                                {!! Form::text('month_year_filter', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.month_year' ) ]); !!}
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('from_date_filter', "From Date" .':') !!}
                                            <div class="input-group">
                                                {!! Form::text('from_date_filter', null, ['class' => 'form-control', 'placeholder' => "From Date" ]); !!}
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('to_date_filter', "To Date" . ':') !!}
                                            <div class="input-group">
                                                {!! Form::text('to_date_filter', null, ['class' => 'form-control', 'placeholder' => "To Date" ]); !!}
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                        <div class="row">
                            @can('essentials.create_payroll')
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#payroll_modal">
                                        <i class="fa fa-plus"></i>
                                        @lang( 'messages.add' )
                                    </button>
                                </div>
                                <br><br><br>
                            @endcan
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="payrolls_table" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>@lang( 'essentials::lang.employee' )</th>
                                                <th>@lang( 'essentials::lang.department' )</th>
                                                <th>@lang( 'essentials::lang.designation' )</th>
                                                <th>@lang( 'essentials::lang.month_year' )</th>
                                                <th>From Date</th>
                                                <th>To Date</th>
                                                <th>@lang( 'purchase.ref_no' )</th>
                                                <th>@lang( 'sale.total_amount' )</th>
                                                <th>@lang( 'sale.payment_status' )</th>
                                                <th>@lang( 'messages.action' )</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    @can('essentials.view_all_payroll')
                        <div class="tab-pane" id="payroll_group_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="payroll_group_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('essentials::lang.name')</th>
                                                    <th>@lang('sale.status')</th>
                                                    <th>@lang( 'sale.payment_status' )</th>
                                                    <th>@lang('essentials::lang.total_gross_amount')</th>
                                                    <th>@lang('lang_v1.added_by')</th>
                                                    <th>@lang('business.location')</th>
                                                    <th>@lang('lang_v1.created_at')</th>
                                                    <th>@lang( 'messages.action' )</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                    @if(auth()->user()->can('essentials.view_allowance_and_deduction') || auth()->user()->can('essentials.add_allowance_and_deduction'))
                        <div class="tab-pane" id="pay_component_tab">
                            <div class="row">
                                @can('essentials.add_allowance_and_deduction')
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary btn-modal pull-right" data-href="{{action([\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController::class, 'create'])}}" data-container="#add_allowance_deduction_modal">
                                                <i class="fa fa-plus"></i> @lang( 'messages.add' )
                                        </button>
                                    </div><br><br><br>
                                @endcan
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="ad_pc_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang( 'lang_v1.description' )</th>
                                                    <th>@lang( 'lang_v1.type' )</th>
                                                    <th>@lang( 'sale.amount' )</th>
                                                    <th>@lang( 'essentials::lang.applicable_date' )</th>
                                                    <th>@lang( 'essentials::lang.employee' )</th>
                                                    <th>@lang( 'messages.action' )</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="user_leave_summary"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @can('essentials.create_payroll')
        @includeIf('essentials::payroll.payroll_modal')
    @endcan
    <div class="modal fade" id="add_allowance_deduction_modal" tabindex="-1" role="dialog"
 aria-labelledby="gridSystemModalLabel"></div>
</section>
<!-- /.content -->
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            const payrollDatepickerConfig = {
                autoApply: true,
                autoUpdateInput: false,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
            };

            payrolls_table = $('#payrolls_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'index'])}}",
                    data: function (d) {
                        if ($('#user_id_filter').length) {
                            d.user_id = $('#user_id_filter').val();
                        }
                        if ($('#location_id_filter').length) {
                            d.location_id = $('#location_id_filter').val();
                        }
                        d.month_year = $('#month_year_filter').val();
                        d.from_date = $('#from_date_filter').val();
                        d.to_date = $('#to_date_filter').val();
                        if ($('#department_id').length) {
                            d.department_id = $('#department_id').val();
                        }
                        if ($('#designation_id').length) {
                            d.designation_id = $('#designation_id').val();
                        }
                    },
                },
                columnDefs: [
                    {
                        targets: 7,
                        orderable: false,
                        searchable: false,
                    },
                ],
                aaSorting: [[4, 'desc']],
                columns: [
                    { data: 'user', name: 'user' },
                    { data: 'department', name: 'dept.name' },
                    { data: 'designation', name: 'dsgn.name' },
                    { data: 'transaction_date', name: 'transaction_date'},
                    { data: 'from_date', name: 'from_date'},
                    { data: 'to_date', name: 'to_date'},
                    { data: 'ref_no', name: 'ref_no'},
                    { data: 'final_total', name: 'final_total'},
                    { data: 'payment_status', name: 'payment_status'},
                    { data: 'action', name: 'action' },
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#payrolls_table'));
                },
            });

            $(document).on('change', '#user_id_filter, #month_year_filter, #department_id, #designation_id, #location_id_filter', function() {
                payrolls_table.ajax.reload();
            });

            if ($('#add_payroll_step1').length) {
                $('#add_payroll_step1').validate();
                $('#employee_id').select2({
                    dropdownParent: $('#payroll_modal')
                });
            }

            $('div.view_modal').on('shown.bs.modal', function(e) {
                __currency_convert_recursively($('.view_modal'));
            });

            $('#month_year, #month_year_filter').datepicker({
                autoclose: true,
                format: 'mm/yyyy',
                minViewMode: "months"
            });

            $('#from_date_filter, #to_date_filter').daterangepicker(
                payrollDatepickerConfig, (startDate, endDate) => {
                    $('#from_date_filter').val(startDate.format('MM/DD/YYYY'));
                    $('#to_date_filter').val(endDate.format('MM/DD/YYYY'));
                    payrolls_table.ajax.reload();
                }
            );

            //pay components
            @if(auth()->user()->can('essentials.view_allowance_and_deduction') || auth()->user()->can('essentials.add_allowance_and_deduction'))
                $('#add_allowance_deduction_modal').on('shown.bs.modal', function(e) {
                    var $p = $(this);
                    $('#add_allowance_deduction_modal .select2').select2({dropdownParent:$p});
                    $('#add_allowance_deduction_modal #applicable_date').datepicker();
                    
                });

                $(document).on('submit', 'form#add_allowance_form', function(e) {
                    e.preventDefault();
                    $(this).find('button[type="submit"]').attr('disabled', true);
                    var data = $(this).serialize();

                    $.ajax({
                        method: $(this).attr('method'),
                        url: $(this).attr('action'),
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                $('div#add_allowance_deduction_modal').modal('hide');
                                toastr.success(result.msg);
                                ad_pc_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                });
                
                ad_pc_table = $('#ad_pc_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{action([\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController::class, 'index'])}}",
                    columns: [
                        { data: 'description', name: 'description' },
                        { data: 'type', name: 'type' },
                        { data: 'amount', name: 'amount' },
                        { data: 'applicable_date', name: 'applicable_date' },
                        { data: 'employees', searchable: false, orderable: false },
                        { data: 'action', name: 'action' }
                    ],
                    fnDrawCallback: function(oSettings) {
                        __currency_convert_recursively($('#ad_pc_table'));
                    },
                });

                $(document).on('click', '.delete-allowance', function(e) {
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
                                        ad_pc_table.ajax.reload();
                                    } else {
                                        toastr.error(result.msg);
                                    }
                                },
                            });
                        }
                    });
                });
            @endif
            //payroll groups
            @can('essentials.view_all_payroll')
                payroll_group_table = $('#payroll_group_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'payrollGroupDatatable'])}}",
                        aaSorting: [[6, 'desc']],
                        columns: [
                            { data: 'name', name: 'essentials_payroll_groups.name' },
                            { data: 'status', name: 'essentials_payroll_groups.status' },
                            { data: 'payment_status', name: 'essentials_payroll_groups.payment_status' },
                            { data: 'gross_total', name: 'essentials_payroll_groups.gross_total' },
                            { data: 'added_by', name: 'added_by' },
                            { data: 'location_name', name: 'BL.name' },
                            { data: 'created_at', name: 'essentials_payroll_groups.created_at', searchable: false},
                            { data: 'action', name: 'action', searchable: false, orderable: false}
                        ]
                    });
            @endcan

            @can('essentials.delete_payroll')
                $(document).on('click', '.delete-payroll', function(e) {
                    e.preventDefault();
                    swal({
                        title: LANG.sure,
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    }).then(willDelete => {
                        if (willDelete) {
                            var href = $(this).attr('href');
                            var data = $(this).serialize();

                            $.ajax({
                                method: 'DELETE',
                                url: href,
                                dataType: 'json',
                                data: data,
                                success: function(result) {
                                    if (result.success == true) {
                                        toastr.success(result.msg);
                                        payroll_group_table.ajax.reload();
                                    } else {
                                        toastr.error(result.msg);
                                    }
                                },
                            });
                        }
                    });
                });
            @endcan

            $(document).on('change', '#primary_work_location, #payment_period', function () {
                $.ajax({
                    method: 'GET',
                    url: "{{action([\Modules\Essentials\Http\Controllers\PayrollController::class, 'getEmployeesBasedOnLocation'])}}",
                    dataType: 'json',
                    data: {
                        location_id: $('#primary_work_location').val(),
                        pay_period: $('#payment_period').val()
                    },
                    success: function(result) {
                        if (result.success == true) {
                            $('select#employee_ids').html('');
                            $('select#employee_ids').html(result.employees_html);
                        }
                    }
                });
            });

            @can('essentials.create_payroll')
                function applyPayrollDate(startDate, endDate) {
                    $('#from_date').val(startDate.format('MM/DD/YYYY'));
                    $('#to_date').val(endDate.format('MM/DD/YYYY'));
                    $("#month_year").val(startDate.format('MM/YYYY'));
                }
                function applyPayrollWeekDate(startDate, endDate) {
                    let fromDate = startDate.format('MM/DD/YYYY');
                    let toDate = endDate.format('MM/DD/YYYY');
                    $('#from_date').val(fromDate);
                    $('#to_date').val(toDate);
                    $("#month_year").val(endDate.format('MM/YYYY'));
                    $("#month_week").val(startDate.format(`${fromDate}-${toDate}`));
                }
                function applyPayrollHalfMonth(startDate, endDate) {
                    let fromDate = startDate.format('MM/DD/YYYY');
                    let toDate = endDate.format('MM/DD/YYYY');
                    $('#from_date').val(fromDate);
                    $('#to_date').val(toDate);
                    $('#half_of_month').val(startDate.format(`${fromDate}-${toDate}`));
                    if ($("#month_year").val()=='') {
                        $("#month_year").val(startDate.format('MM/YYYY'));
                    }
                }
                function renderDateRangePicker() {
                    try {
                        $('#month_week').data('daterangepicker').remove();
                    } catch {}
                    const pickerRanges = {
                        'Last Week': [moment().subtract(1, 'weeks').startOf('week'), moment().subtract(1, 'weeks').endOf('week')],
                        'This Week': [moment().startOf('week'), moment().endOf('week')],
                        'Next Week': [moment().add(1, 'weeks').startOf('month'), moment().add(1, 'weeks').endOf('week')],
                    };
                    const pickerConfig = {
                        autoApply: true,
                        autoUpdateInput: false,
                        showCustomRangeLabel:false,
                        linkedCalendars: false,
                        ranges: pickerRanges
                    };

                    if($("#month_year").val()!='') {
                        const [month, year] = $("#month_year").val().split('/');
                        const date = new Date(`${year}-${month}-01`);
                        pickerConfig.ranges = {
                            ...pickerRanges,
                            "1st Week": [moment(date).startOf('week'), moment(date).endOf('week')],
                            "2nd Week": [moment(date).add(1, 'weeks').startOf('week'), moment(date).add(1, 'weeks').endOf('week')],
                            "3rd Week": [moment(date).add(2, 'weeks').startOf('week'), moment(date).add(2, 'weeks').endOf('week')],
                            "4th Week": [moment(date).add(4, 'weeks').startOf('week'), moment(date).add(4, 'weeks').endOf('week')],
                        }
                    }

                    $('#month_week').daterangepicker(pickerConfig, applyPayrollWeekDate);
                    $(".week_of_month").show();
                }
                function renderHalfMonthDateRangePicker() {
                    try {
                        $('#half_of_month').data('daterangepicker').remove();
                        console.log('removed');
                    } catch {}
                    const pickerConfig = {
                        autoApply: true,
                        autoUpdateInput: false,
                        showCustomRangeLabel:false,
                        alwaysShowCalendars: false,
                        linkedCalendars: false,
                        ranges: {
                            'First Half Month': [moment().startOf('month'), moment().startOf('month').add(14, 'days')],
                            'Last Half Month': [moment().startOf('month').add(15, 'days'), moment().endOf('month')]
                        },
                        //startDate: moment().startOf('month'),
                        //endDate: moment().startOf('month').add(14, 'days'),
                    };

                    if($("#month_year").val()!='') {
                        const [month, year] = $("#month_year").val().split('/');
                        const firstDate = new Date(`${year}-${month}-01`);

                        pickerConfig.ranges = {
                            'First Half Month': [moment(firstDate).startOf('month'), moment(firstDate).startOf('month').add(14, 'days')],
                            'Last Half Month': [moment(firstDate).startOf('month').add(15, 'days'), moment(firstDate).endOf('month')]
                        };
                        //pickerConfig.startDate = moment(firstDate).startOf('month');
                        //pickerConfig.endDate = moment(firstDate).startOf('month').add(14, 'days');
                        pickerConfig.minDate = moment(firstDate).startOf('month')
                        pickerConfig.maxDate = moment(firstDate).endOf('month')
                    }
                    $('#half_of_month').daterangepicker(pickerConfig, applyPayrollHalfMonth);
                }
                $(document).on('shown.bs.modal', '#payroll_modal', function() {
                    $('.payslip_form_input').hide();
                    if($('#payment_period').val()!=='') {
                        $("#month_year").parents(".form-group").show();
                        if($('#payment_period').val()==='week') {
                            renderDateRangePicker();
                        }
                        if($('#payment_period').val()=='month') {
                            renderHalfMonthDateRangePicker();
                            $('.half_of_month_input').show();
                        }
                    }
                    $('#from_date, #to_date').daterangepicker(payrollDatepickerConfig, applyPayrollDate);
                });
                $(document).on('change', '#month_year', function() {
                    if ($(this).val()!='') {
                        $('#from_date, #to_date, #month_week, #half_of_month').val('');
                        if ($('#payment_period').val()=='day') {
                            const [month, year] = $(this).val().split('/');
                            const firstDate = moment(new Date(`${year}-${month}-01`));
                            $('#from_date').val(firstDate.startOf('month').format('MM/DD/YYYY'));
                            $('#to_date').val(firstDate.endOf('month').format('MM/DD/YYYY'));
                        }
                        if($('#payment_period').val()=='week') {
                            renderDateRangePicker();
                        }
                        if($('#payment_period').val()=='month') {
                            console.log($(this).val());
                            renderHalfMonthDateRangePicker();
                            $('.half_of_month_input').show();
                        }
                    }
                });
                $(document).on('change', '#payment_period', function() {
                    $('.payslip_form_input').hide();
                    if($(this).val()!='') {
                        $('#from_date, #to_date, #month_week, #half_of_month').val('');
                        $("#month_year").parents(".form-group").show();
                        if ($(this).val()=='day') {
                            $("#from_date").parents(".form-group").show();
                            $("#to_date").parents(".form-group").show();
                        }
                        if($(this).val()=='week') {
                            renderDateRangePicker();
                        }
                        if($(this).val()=='month') {
                            renderHalfMonthDateRangePicker();
                            $('.half_of_month_input').show();
                        }
                    }
                });
            @endcan
        });
    </script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection
