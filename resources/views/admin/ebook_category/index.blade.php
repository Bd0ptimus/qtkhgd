@extends('layouts.contentLayoutMaster')
@php
    $grades = GRADES;
    $schoolLevels = SCHOOL_TYPES;
    $breadcrumbs = [
        ['name' => trans('admin.home'), 'link' => route('admin.home')],
        ['name' => $title_description ?? 'Sách điện tử'],
    ];
@endphp
@section('title', $title_description ?? 'Sách điện tử')
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css')}}">
    <style>
        .exercise-question-content {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .dataTables_filter {
            display: none;
        }
    </style>
@endsection

@section('main')

    <!-- Scroll - horizontal and vertical table -->
    <section id="horizontal-vertical">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body card-dashboard form-group form-filter">
                        <div class='row align-items-center'>
                            @if(Admin::user()->isRole('administrator'))
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="formGroupExampleInput2">Cộng tác viên</label>
                                    <select class="form-control parent select2 filter-collaborator-category" style="width: 100%;" name="collaborator">
                                        <option value="">Tất cả</option>
                                        @foreach ($collaborators as $collaborator)
                                        <option value="{{ $collaborator->id }}" @if ($collaborator->id == $selectedCollaborator) selected @endif>
                                            {{ $collaborator->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-4 d-flex">
                                <div class="d-flex">
                                    <input type="text" name="search"
                                           id="search"
                                           placeholder="{{ trans('admin.search') }}"
                                           value="{{ old('search', $search ?? '') }}"
                                           class="form-control title"/>
                                    <button type="submit" style="width: 50%;" class="btn btn-primary ml-1 input-search">{{ trans('admin.search') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="d-flex">
                                <div class="btn-group text-nowrap">
                                    <a type="button" @if(!$permission) hidden @endif
                                        class="btn btn-flat btn-success"
                                        href="{{ route('ebook-categories.create') }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        Thêm loại sách
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="exercise-question">
                                    <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên sách</th>
                                        <th scope="col">Tên không dấu</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($ebookCategories as $ebookCategory)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $ebookCategory->name}}</td>
                                            <td>{{ $ebookCategory->slug }}</td>
                                            <td>
                                                <a type="button" class="btn btn-flat btn-success btn-datatable"
                                                    @if(!$permission) hidden @endif
                                                    href="{{ route('ebook-categories.edit', ['id' => $ebookCategory->id]) }}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                                </a>
                                                <a type="button" class="btn btn-flat btn-danger delete-item btn-datatable"
                                                   data-url="{{route('ebook-categories.delete', ['id' => $ebookCategory->id])}}"
                                                   @if(!$permission) hidden @endif
                                                   href="#">
                                                    <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
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
    <script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2({
                allowClear: true
            });

            $.fn.dataTable.ext.errMode = 'throw';
            $('#exercise-question').DataTable({
                pageLength: 25,
                search: false
            });

            var table = $('#homework-sheet').DataTable();
            $('#search').on( 'keyup', function () {
                table.search(this.value)
                    .draw();
            } );

            $('#exercise-question').on('click','.delete-item',function (e) {
                e.preventDefault();
                let confirmDelete = confirm('Bạn có chắc chắn muốn xoá loại sách này?');
                if(confirmDelete) {
                    let element = $(this);
                    $.ajax({
                        url: element.data('url'),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(res) {
                            element.parents('tr').remove();
                            alert('Đã xoá thành công!');
                        },
                        error: function(res) {
                            alert(res.responseJSON.msg);
                        },
                    });
                }
            });

            //update parameter when change filter
            function updateParameter(urlCurrent, key, value) {
                let url = new URL(urlCurrent);
                let search_params = url.searchParams;

                // new value of "key" is set to "value"
                search_params.set(key, value);

                // change the search property of the main url
                url.search = search_params.toString();

                return url.toString();
            }

            $('.input-search').click(function () {
                window.location.href = updateParameter(window.location.href, 'search', $('#search').val());
            });
            
            $('.filter-collaborator-category').change(function() {
                window.location.href = updateParameter(window.location.href, 'selectedCollaborator', $(this).val())
            });
        });
    </script>
@endsection
