@extends('layouts.contentLayoutMaster')
@php
    $grades = GRADES;
    $schoolLevels = SCHOOL_TYPES;
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => 'Lên bài giảng', 'link' => route('lesson_sample.up.lesson')],
        ['name' => $title_description ?? 'Mô hình mô phỏng'],
    ];
@endphp
@section('title', $title_description ?? 'Mô hình mô phỏng')
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    {{--    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">--}}
    <style>
        .iframe-simulation {
            width: 100%;
            height: 70vh;
        }
    </style>
@endsection

@section('main')

    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard d-flex justify-content-center">
                            <iframe class="iframe-simulation" src="https://phet.colorado.edu/sims/html/geometric-optics/latest/geometric-optics_vi.html" title="Mô hình mô phỏng"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Scroll - horizontal and vertical table -->
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
    {{--    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>--}}
@endsection
