<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title>

    @include('layouts.partials.css')

    @include('layouts.partials.extracss_auth')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src='https://www.google.com/recaptcha/api.js'></script>

</head>

<body class="pace-done" data-new-gr-c-s-check-loaded="14.1172.0" data-gr-ext-installed="" cz-shortcut-listen="true">
    @inject('request', 'Illuminate\Http\Request')
    @if (session('status') && session('status.success'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
            data-msg="{{ session('status.msg') }}">
    @endif
    <div class="box box-fluid">
        <div class="row eq-height-row">
            <div class="col-md-12 col-sm-12 col-xs-12 right-col tw-pt-0 tw-pb-10 tw-px-5">
                <div class="row">
                    <div class="box" style="background-color:purple;">
        <br>
        <br>
                    <h1 class="text-center"><i class="fas fa-thumbs-up	"<big><b> </i> Advantage Business Management Systems</a><h1>
                    <br>
                    <br>
                    <br>
                    <div>
                    <div class="tw-absolute tw-top-2 md:tw-top-5 tw-left-4 md:tw-left-8 tw-flex tw-items-center tw-gap-4"
                        style="text-align: left">
                        @include('layouts.partials.language_btn')

                        @if(Route::has('repair-status'))
                            <a class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base hover:tw-text-white"
                                href="{{ action([\Modules\Repair\Http\Controllers\CustomerRepairStatusController::class, 'index']) }}">
                                @lang('repair::lang.repair_status')
                            </a>
                        @endif
                    </div>

                    <div class="tw-absolute tw-top-5 md:tw-top-8 tw-right-5 md:tw-right-10 tw-flex tw-items-center tw-gap-4"
                        style="text-align: left">
                        @if (!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                            <!-- Register Url -->
                            @if (config('constants.allow_registration'))
                            {{-- <span
                                class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base">{{ __('business.not_yet_registered') }}
                            </span> --}}

                            <div class="tw-border-2 tw-border-white tw-rounded-full tw-h-10 md:tw-h-12 tw-w-24 tw-flex tw-items-center tw-justify-center">
                             <a href="{{ route('business.getRegister')}}@if(!empty(request()->lang)){{'?lang='.request()->lang}}@endif"
                                    class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base hover:tw-text-white">
                                    {{ __('business.register') }}</a>
                            </div>

                                <!-- pricing url -->
                                @if (Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) != 'pricing')
                                    &nbsp; <a class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base hover:tw-text-white"
                                        href="{{ action([\Modules\Superadmin\Http\Controllers\PricingController::class, 'index']) }}">@lang('superadmin::lang.pricing')</a>
                                @endif
                            @endif
                        @endif
                        @if ($request->segment(1) != 'login')
                            <a class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base hover:tw-text-white"
                                href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login'])}}@if(!empty(request()->lang)){{'?lang='.request()->lang}}@endif">{{ __('business.sign_in') }}</a>
                        @endif
                    </div>
                    <div class="col-md-10 col-xs-8" style="text-align: right;">

                    </div>
                </div>
                @yield('content')
                <div class="col-md-12 col-xs-12" style="padding-bottom: 30px;">
                    <br>
                    <br>
                    <br>
                    <br>
                    <div
                        class="lg:tw-w-16 md:tw-h-16 tw-w-12 tw-h-12 tw-flex tw-items-center tw-justify-center tw-mx-auto tw-overflow-hidden tw-bg-white tw-square-full tw-p-0.5 tw-mb-10">
                        <img src="{{ asset('https://jing.exelmed.ph/public/img/default.png')}}" alt="lock" class="tw-square-full tw-object-fill" />
                    </div>
                    <h1 class="text-center"><i class="fas fa-thumbs-up	"<small><b> </i> Exelmed Pharma & Vincare Pharma</a><h1>
        <br>
        <br>
        <br>
        <br>
        <div class="box box-success" >
            <div class="box-header">
        <h2 class="text-center"> For each phase of Business, it is Essential to have a System in place for Solutions  <small><b> = Cloud System is the intelligent solution for automating intricate processes and obtaining user-friendly dashboards and reports.</b></i><big><i></Big></h2>
        </div>        
<p class="text-center"="color:DodgerBlue;">By: Ariel Nazara</p>
                <h1 class="text-center"="color:Tomato;">Ariel Nazara</h1>
            <hr>
 <br>
 <br>
 <br>
 
<h4 class="text-center"><i class="fas fa-thumbs-up	"></i> Usefull Links Below!!!</a><h4>
<br>    
            <h4 class="text-center"><a href="https://www.bpi.com.ph//" class="btn bg-navy" role="button">BPI Bank</a>
<a href="https://www.metrobank.com.ph/home" class="btn btn-success" role="button">MetroBank</a>
<a href="https://www.bdo.com.ph/personal" class="btn btn-primary" role="button">BDO Bank</a>
<a href="https://meet.chikkahub.com" class="btn bg-purple" role="button">Video Conferencing</a>
<a href="https://facebook.com" class="btn btn-info" role="button">Facebook</a>
<a href="https://web.whatsapp.com/" class="btn btn-danger" role="button">WhatsApp</a><h4>
<br>
<br>
<br>
<br>
<br>
<hr>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

                </div>
            </div>
        </div>
    </div>


    @include('layouts.partials.javascripts')

    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>

    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2_register').select2();

            // $('input').iCheck({
            //     checkboxClass: 'icheckbox_square-blue',
            //     radioClass: 'iradio_square-blue',
            //     increaseArea: '20%' // optional
            // });
        });
    </script>
    <style>
        .wizard>.content {
            background-color: white !important;
        }
    </style>
</body>

</html>
