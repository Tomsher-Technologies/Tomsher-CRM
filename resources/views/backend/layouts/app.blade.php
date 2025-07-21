<!doctype html>
@if (\App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">
    <meta name="admin-url" content="{{ getBaseURL() . env('ADMIN_PREFIX') }}">
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicon -->
    <link rel="icon" type="image/svg" href="{{ asset('assets/img/favicon.ico') }}">
    <title>{{ env('APP_NAME') . ' | ' . $title }}</title>

    <!-- google font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

    <!-- aiz core css -->
    <link rel="stylesheet" href="{{ asset('assets/css/vendors.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-style.css') }}">
    @if (\App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/css/aiz-core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/flatpickr.min.css') }}">
    @yield('header')
    @yield('style')
    <style>
        /* body {
            font-size: 14px !important;
        } */
    </style>
    <script>
        var AIZ = AIZ || {};
        AIZ.local = {
            nothing_selected: '{!!  trans('messages.nothing_selected') !!}',
            nothing_found: '{!!  trans('messages.nothing_found') !!}',
            choose_file: '{{  trans('messages.choose_file') }}',
            file_selected: '{{  trans('messages.file_selected') }}',
            files_selected: '{{  trans('messages.files_selected') }}',
            add_more_files: '{{  trans('messages.add_more_files') }}',
            adding_more_files: '{{  trans('messages.adding_more_files') }}',
            drop_files_here_paste_or: '{{  trans('messages.drop_files_here_paste_or') }}',
            browse: '{{  trans('messages.browse') }}',
            upload_complete: '{{  trans('messages.upload_complete') }}',
            upload_paused: '{{  trans('messages.upload_paused') }}',
            resume_upload: '{{  trans('messages.resume_upload') }}',
            pause_upload: '{{  trans('messages.pause_upload') }}',
            retry_upload: '{{  trans('messages.retry_upload') }}',
            cancel_upload: '{{  trans('messages.cancel_upload') }}',
            uploading: '{{  trans('messages.uploading') }}',
            processing: '{{  trans('messages.processing') }}',
            complete: '{{  trans('messages.complete') }}',
            file: '{{  trans('messages.file') }}',
            files: '{{  trans('messages.files') }}',
        }
    </script>

</head>

<body class="">

    <div class="aiz-main-wrapper">
        @include('backend.inc.admin_sidenav')
        <div class="aiz-content-wrapper">
            @include('backend.inc.admin_nav')
            <div class="aiz-main-content">
                <div class="px-15px px-lg-5px">
                    @yield('content')
                </div>
                <div class="bg-white text-center py-3 px-15px px-lg-25px mt-auto">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ env('APP_NAME') }}</p>
                </div>
            </div><!-- .aiz-main-content -->
        </div><!-- .aiz-content-wrapper -->
    </div><!-- .aiz-main-wrapper -->

    @yield('modal')


    <script src="{{ asset('assets/js/vendors.js') }}"></script>
    <script src="{{ asset('assets/js/aiz-core.js') }}"></script>
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>

    @yield('script')

    <script type="text/javascript">
        @foreach (session('flash_notification', collect())->toArray() as $message)
            AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
        @endforeach

        function menuSearch() {
            var filter, item;
            filter = $("#menu-search").val().toUpperCase();
            items = $("#main-menu").find("a");
            items = items.filter(function(i, item) {
                if ($(item).find(".aiz-side-nav-text")[0].innerText.toUpperCase().indexOf(filter) > -1 && $(item)
                    .attr('href') !== '#') {
                    return item;
                }
            });

            if (filter !== '') {
                $("#main-menu").addClass('d-none');
                $("#search-menu").html('')
                if (items.length > 0) {
                    for (i = 0; i < items.length; i++) {
                        const text = $(items[i]).find(".aiz-side-nav-text")[0].innerText;
                        const link = $(items[i]).attr('href');
                        $("#search-menu").append(
                            `<li class="aiz-side-nav-item"><a href="${link}" class="aiz-side-nav-link"><i class="las la-ellipsis-h aiz-side-nav-icon"></i><span>${text}</span></a></li`
                            );
                    }
                } else {
                    $("#search-menu").html(
                        `<li class="aiz-side-nav-item"><span	class="text-center text-white d-block">Nothing Found</span></li>`
                        );
                }
            } else {
                $("#main-menu").removeClass('d-none');
                $("#search-menu").html('')
            }
        }
    </script>

</body>

</html>
