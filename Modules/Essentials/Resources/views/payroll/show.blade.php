<div class="modal-dialog modal-lg" role="document">
  	<div class="modal-content">
  		<div class="modal-header no-print">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      		<span aria-hidden="true">&times;</span>
	      	</button>
	      	<h4 class="modal-title no-print">
	      		{!! __('essentials::lang.payroll_of_employee', ['employee' => $payroll->transaction_for->user_full_name, 'date' => $month_name . ' ' . $year]) !!}
	      	</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="table-responsive">
		      	<table class="table table-bordered" id="payroll-view">
		      		<tr>
		      			<td colspan="3" style="padding: 20px; text-align: center;">
                            <!-- Business Logo and Name in One Row -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                                <!-- Business Logo Section (left aligned) -->
                                @if(!empty(Session::get('business.logo')))
                                    <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo" style="max-width: 150px; max-height: 50px;">
                                @endif
                        
                                <!-- Business Name and Address Section (right aligned) -->
                                <div style="text-align: right;">
                                    <div style="font-size: 24px; font-weight: bold;">
                                        {{ Session::get('business.name') ?? '' }}
                                    </div>
                                    <div style="font-size: 14px; color: #777;">
                                        {!! Session::get('business.business_address') ?? '' !!}
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Payslip for the Month Section -->
                            <div style="text-align: center; padding-top: 10px;">
                                <p style="font-size: 16px; color: #555;">
                                    @lang('essentials::lang.payslip_for_the_month', ['month' => $month_name, 'year' => $year])
                                </p>
                                <p style="font-size: 16px; color: #555;">
                                    (From {{ $start_of_month->format('m/d/Y') }} To {{ $end_of_month->format('m/d/Y') }})
                                </p>
                            </div>
                        </td>

		      		</tr>
		      		<tr>
		      			<td colspan="3">
		      				<div class="pull-left" style="width: 50% !important;">
		      					<strong>@lang('essentials::lang.employee'):</strong>
		      					{{$payroll->transaction_for->user_full_name}}<br>

		      					<strong>@lang('essentials::lang.department'):</strong>
		      					{{$department->name ?? ''}}
		      					<br>

		      					<strong>@lang('essentials::lang.designation'):</strong>
		      					{{$designation->name ?? ''}}

		      					<br>
		      					<strong>@lang('lang_v1.primary_work_location'):</strong>
		      					@if(!empty($location))
		      						{{$location->name}}
		      					@else
		      						{{__('report.all_locations')}}
		      					@endif
		      					<br>

		      					@if(!empty($payroll->transaction_for->id_proof_name) && !empty($payroll->transaction_for->id_proof_number))
		      						<strong>
		      							{{ucfirst($payroll->transaction_for->id_proof_name)}}:
		      						</strong>
		      						{{$payroll->transaction_for->id_proof_number}}
		      						<br>
		      					@endif

		      					<strong>@lang('lang_v1.tax_payer_id'):</strong>
		      					{{$bank_details['tax_payer_id'] ?? ''}}
		      					<br>
		      				</div>
		      				<div class="pull-right" style="width: 50% !important;">
		      					<strong>@lang('lang_v1.bank_name'):</strong>
		      					{{$bank_details['bank_name'] ?? ''}}
		      					<br>

		      					<strong>@lang('lang_v1.branch'):</strong>
		      					{{$bank_details['branch'] ?? ''}}
		      					<br>

		      					<strong>@lang('lang_v1.bank_code'):</strong>
		      					{{$bank_details['bank_code'] ?? ''}}
		      					<br>
		      					
		      					<strong>@lang('lang_v1.account_holder_name'):</strong>
		      					{{$bank_details['account_holder_name'] ?? ''}}
		      					<br>

		      					<strong>@lang('lang_v1.bank_account_no'):</strong>
		      					{{$bank_details['account_number'] ?? ''}}
		      					<br>
		      				</div>
		      			</td>
		      		</tr>
		      		<tr>
		      			<td>
		      				<strong>@lang('essentials::lang.total_work_duration'):</strong>
							{{__('essentials::lang.work_duration_hour', ['duration' => (int)$total_work_duration])}}
		      			</td>
		      			<td>
		      				<strong>@lang('essentials::lang.days_present'):</strong>
		      				{{$total_days_present}}
		      			</td>
		      			<td>
							<?php
								$total_working_days = $start_of_month->diffInDays($end_of_month->clone()->addDay());
								$total_absent_days = $total_working_days - $total_days_present;
							?>
		      				<strong>@lang('essentials::lang.days_absent'):</strong> {{$total_absent_days}}
							<span class="float-right"><strong>(@lang('essentials::lang.leaves'):</strong> {{$total_leaves}})</span>
		      			</td>
		      		</tr>
		      		<tr>
		      			<td colspan="3"></td>
		      		</tr>
		      		<tr>
						<td colspan="2" style="width: 50% !important;">
							<div style="width: 50% !important; float: left;">
								<strong>@lang('essentials::lang.allowances')</strong>
							</div>
							<div style="width: 30% !important;float: right;">
								<strong>@lang('sale.amount')</strong>
							</div>
							<div style="width: 20% !important;float: right;">
								<strong>@lang('essentials::lang.rate')</strong>
							</div>
						</td>
						<td style="width: 50% !important;">
							<div style="width: 50% !important; float: left;">
								<strong>@lang('essentials::lang.deductions')</strong>
							</div>
							<div style="width: 30% !important;float: right;">
								<strong>@lang('sale.amount')</strong>
							</div>
							<div style="width: 20% !important;float: right;">
								<strong>@lang('essentials::lang.rate')</strong>
							</div>
						</td>
					</tr>
		      		<tr>
						<td colspan="2" style="width: 50% !important;">
							@php
								$basic_salary = $payroll->essentials_amount_per_unit_duration;
								$total_earnings = $payroll->essentials_duration_day * $payroll->essentials_amount_per_day;
								if ($payroll->essentials_duration_unit == 'Month') {
									$total_earnings = $payroll->essentials_amount_per_unit_duration;
									$basic_salary = $basic_salary * 2;
								}
		                    @endphp
		                    <div style="width: 50% !important; float: left;">
								@lang('essentials::lang.salary')
								<br>
								<small>({{@num_format($basic_salary)}} / {{$payroll->essentials_duration_unit}})</small>
							</div>
							<div style="width: 30% !important;float: right;">
								<span class="display_currency" data-currency_symbol="true">
									@if ($payroll->essentials_duration_unit != 'Month')
										{{$payroll->essentials_duration_day * $payroll->essentials_amount_per_day}}
									@else 
										{{ $total_earnings }}
									@endif
								</span>
								@if ($payroll->essentials_duration_unit != 'Month')
									<br>
									<small>({{@num_format($payroll->essentials_duration_day)}} Day * {{@num_format($payroll->essentials_amount_per_day)}})</small>
								@endif
							</div>
							<div style="width: 20% !important;float: right;">
							</div><br><br>
		                    @forelse($allowances['allowance_names'] as $key => $value)
								<div style="width: 50% !important; float: left;">
									{{$value}}
								</div>
								<div style="width: 30% !important;float: right;">
									<span class="display_currency" data-currency_symbol="true">
										{{$allowances['allowance_amounts'][$key]}}
									</span>
								</div>
								<div style="width: 20% !important;float: right;">
									@if(!empty($allowances['allowance_types'][$key]) 
		                    		&& $allowances['allowance_types'][$key] == 'percent')
		                    			{{@num_format($allowances['allowance_percents'][$key])}}%
		                    		@endif
								</div>
								@php
		                            $total_earnings += !empty($allowances['allowance_amounts'][$key]) ? $allowances['allowance_amounts'][$key] : 0;
		                        @endphp
							@empty
		                       
		                    @endforelse
						</td>
						<td colspan="2" style="width: 50% !important;">
							@php
		                        $total_deduction = 0;
		                    @endphp
		                    @forelse($deductions['deduction_names'] as $key => $value)
								<div style="width: 50% !important; float: left;">
									{{$value}}
								</div>
								<div style="width: 30% !important;float: right;">
									<span class="display_currency" data-currency_symbol="true">
										{{$deductions['deduction_amounts'][$key]}}
									</span>
								</div>
								<div style="width: 20% !important;float: right;">
									@if(!empty($deductions['deduction_types'][$key]) 
			                    		&& $deductions['deduction_types'][$key] == 'percent')
		                    			{{@num_format($deductions['deduction_percents'][$key])}}%
		                    		@endif
								</div>
								@php
		                            $total_deduction += !empty($deductions['deduction_amounts'][$key]) ? $deductions['deduction_amounts'][$key] : 0;
		                        @endphp
							@empty
		                       <div style="width: 100% !important; text-align: center;">
		                       		@lang('lang_v1.none')
		                       </div>
		                    @endforelse
						</td>
					</tr>
					<tr>
						<td colspan="2" style="width: 50% !important;">
							<div style="width: 50% !important; float: left;">
								<strong>
									@lang('essentials::lang.total_earnings'):
								</strong>
							</div>
							<div style="width: 30% !important;float: right;">
								<strong>
									<span class="display_currency" data-currency_symbol="true">
										{{$total_earnings}}
									</span>
								</strong>
							</div>
							<div style="width: 20% !important;float: right;">
								
							</div>
						</td>
						<td style="width: 50% !important;">
							<div style="width: 50% !important; float: left;">
								<strong>
									@lang('essentials::lang.total_deductions'):
								</strong>
							</div>
							<div style="width: 30% !important;float: right;">
								<strong>
									<span class="display_currency" data-currency_symbol="true">
										{{$total_deduction}}
									</span>
								</strong>
							</div>
							<div style="width: 20% !important;float: right;">
								
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: right;">
							<div style="width: 43% !important;float: right;padding-right: 49px">
								<span class="display_currency" data-currency_symbol="true">
									{{$total_earnings - $total_deduction}}
								</span>
							</div>
							<div style="width: 57% !important;">
								<strong>
									@lang('essentials::lang.net_pay')
								</strong>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<strong>@lang('essentials::lang.in_words'):</strong> {{ucfirst($final_total_in_words)}}
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<strong>{{ __('sale.payment_info') }}:</strong>
							<table class="table bg-gray table-slim">
							<tr class="bg-green">
								<th>#</th>
								<th>{{ __('messages.date') }}</th>
								<th>{{ __('purchase.ref_no') }}</th>
								<th>{{ __('sale.amount') }}</th>
								<th>{{ __('sale.payment_mode') }}</th>
								<th>{{ __('sale.payment_note') }}</th>
							</tr>
							@php
								$total_paid = 0;
							@endphp
							@forelse($payroll->payment_lines as $payment_line)
								@php
									if($payment_line->is_return == 1){
									  $total_paid -= $payment_line->amount;
									} else {
									  $total_paid += $payment_line->amount;
									}
								@endphp
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td>{{ @format_date($payment_line->paid_on) }}</td>
									<td>{{ $payment_line->payment_ref_no }}</td>
									<td><span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
									<td>
									  	{{ $payment_types[$payment_line->method]}}
									</td>
									<td>@if($payment_line->note) 
									  {{ ucfirst($payment_line->note) }}
									  @else
									  --
									  @endif
									</td>
								</tr>
							@empty
								<tr><td colspan="6" class="text-center">@lang('purchase.no_records_found')</td></tr>
							@endforelse
						</table>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<strong>@lang('brand.note'):</strong><br>
							{{$payroll->staff_note ?? ''}}
						</td>
					</tr>
		      	</table>
	      	</div>
	    </div>
	    <div class="modal-footer no-print">
	      	<button type="button" class="btn btn-primary" aria-label="Print" onclick="$(this).closest('div.modal-content').find('.modal-body').printThis();">
	      		<i class="fa fa-print"></i> @lang( 'messages.print' )
      		</button>
	      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
  	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<style type="text/css">
	#payroll-view>thead>tr>th, #payroll-view>tbody>tr>th,
	#payroll-view>tfoot>tr>th, #payroll-view>thead>tr>td,
	#payroll-view>tbody>tr>td, #payroll-view>tfoot>tr>td {
		border: 1px solid #1d1a1a;
	}
</style>