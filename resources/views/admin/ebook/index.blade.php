@extends('layouts.contentLayoutMaster')
@php
$grades = GRADES;
$schoolLevels = SCHOOL_TYPES;
$breadcrumbs = [['name' => trans('admin.home'), 'link' => route('admin.home')], ['name' => $title_description ?? 'Sách điện tử']];
@endphp
@section('title', $title_description ?? 'Sách điện tử')
@section('vendor-style')
{{-- vendor css files --}}
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
<link rel="stylesheet" href="{{ asset('admin/AdminLTE/bower_components/select2/dist/css/select2.min.css') }}">

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
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="formGroupExampleInput2">Cấp học</label>
                                <select class="form-control parent select2 filter-level" style="width: 100%;" name="level">
                                    <option value="">Tất cả</option>
                                    @foreach ($schoolLevels as $key => $name)
                                    <option value="{{ $key }}" @if (isset($level) && $key==$level) selected @endif>
                                        {{ $name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="formGroupExampleInput2">Bộ sách</label>
                                <select class="form-control parent select2 filter-assemblage" style="width: 100%;" name="assemblade">
                                    <option value="">Tất cả</option>
                                    @foreach (BOOK_ASSEMBLAGES as $value)
                                    <option value="{{ $value }}" @if (isset($assemblage) && $value==$assemblage) selected @endif>
                                        {{ $value }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="formGroupExampleInput2">Khối học</label>
                                <select class="form-control parent select2 filter-grade" style="width: 100%;" name="grade">
                                    <option value="">Tất cả</option>
                                    @foreach ($grades as $key => $grade)
                                    <option value="{{ $key }}" @if (isset($keyGrade) && $key==$keyGrade) selected @endif>
                                        {{ $grade }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="formGroupExampleInput2">Môn học</label>
                                <select class="form-control parent select2 filter-subject" style="width: 100%;" name="subject">
                                    <option value="">Tất cả</option>
                                    @foreach ($subjects as $key => $subject)
                                    <option value="{{ $subject->id }}" @if (isset($subjectId) && $subject->id == $subjectId) selected @endif>
                                        {{ $subject->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="formGroupExampleInput2">Loại sách</label>
                                <select class="form-control parent select2 filter-ebook-category" style="width: 100%;" name="ebookCategory">
                                    <option value="">Tất cả</option>
                                    @foreach ($ebookCategories as $ebookCategory)
                                    <option value="{{ $ebookCategory->id }}" @if ($ebookCategory->id == $selectedEbookCategory) selected @endif>
                                        {{ $ebookCategory->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                    </div>
                    <div class='row align-items-center'>
                        <div class="col-sm-4 d-flex">
                            <div class="d-flex">
                                <button type="button" class="btn btn-primary mr-1 reset-filter">
                                    <i class="feather icon-refresh-cw"></i>
                                </button>
                                <input type="text" name="search" id="search" placeholder="{{ trans('admin.search') }}" value="{{ old('search',$search ?? '')}}" class="form-control title" />
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
                                <a type="button" @if (!$permission) hidden @endif class="main-action btn btn-flat btn-success" href="{{ route('ebooks.create') }}">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    Thêm sách điện tử
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="exercise-question">
                                <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên sách</th>
                                        <th scope="col">Khối học</th>
                                        <th scope="col">Môn học</th>
                                        <th scope="col">Thuộc bộ sách</th>
                                        <th scope="col">Mô tả sách</th>
                                        <th scope="col">Loại sách</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ebooks as $key => $ebook)
                                    <tr>
                                        <th scope="row">{{ $key + 1 }}</th>
                                        <td>{{ $ebook->name }}</td>
                                        <td>{{ mapGradeName($ebook->grade) }}</td>
                                        <td>{{ $ebook->subject->name }}</td>
                                        <td>{{ $ebook->assemblage }}</td>
                                        <td class="exercise-question-content">
                                            <p>{!! $ebook->description !!}</p>
                                        </td>
                                        <td>
                                            @foreach ($ebook->ebookCategories as $category)
                                            <span class="badge badge-primary">
                                                {{ $category->name }}
                                            </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a type="button" class="main-action btn btn-flat btn-success btn-datatable" @if (!$permission) hidden @endif href="{{ route('ebooks.edit', ['id' => $ebook->id]) }}">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>Sửa
                                            </a>
                                            <a type="button" class="main-action btn btn-flat btn-danger delete-item btn-datatable" @if (!$permission) hidden @endif data-url="{{ route('ebooks.delete', ['id' => $ebook->id]) }}" href="#">
                                                <span title="Xoá"><i class="fa fa-trash" aria-hidden="true"></i>Xoá</span>
                                            </a>

                                            @if (count($ebook->attachments) > 0)
                                            <a type="button" target="_blank" class="btn btn-flat btn-success btn-datatable" href="{{ route('ebooks.download', ['id' => $ebook->id]) }}">
                                                <span title="Xem"><i class="fa fa-eye" aria-hidden="true"></i>Xem và tải về</span>
                                            </a>
                                            @endif

                                            <!-- <a type="button" class="btn btn-flat btn-default download-item mt-2" target="_blank"
                                                           href="{{ route('ebooks.download', ['id' => $ebook->id]) }}">
                                                            <span title="Download"><i class="fa fa-download" aria-hidden="true"></i> Download</span>
                                                        </a> -->
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>


                            <div class="row">
                                <div class="col-7">
                                    <div class="show_entries" id="info" role="status" aria-live="polite"> Showing {{ $ebooks->firstItem()=='' ? '0' : $ebooks->firstItem()}} to {{ $ebooks->lastItem()=='' ? '0' : $ebooks->lastItem()}} of {{$ebooks->total()}} entries</div>
                                </div>
                                <div class="col-5">
                                    <div class="btn-group float-end">
                                      {{ $ebooks->appends(request()->query())->links()}}
                                    </div>

                                </div>
                            </div>

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
<script src="{{ asset('admin/AdminLTE/bower_components/select2/dist/js/select2.full.min.js') }}"></script>




<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            allowClear: true
        });

        var limit = 25;

        var url = new URL(window.location.href);
        if (url.searchParams.get('limit'))
            limit = url.searchParams.get('limit')


        $.fn.dataTable.ext.errMode = 'throw';
        $('#exercise-question').DataTable({
            pageLength: limit,
            search: true,
            info: false,
            bPaginate: false,
        });

        var table = $('#homework-sheet').DataTable();
        $('#search').on('keyup', function() {
            table.search(this.value)
                .draw();
        });

        $('#exercise-question').on('click', '.delete-item', function(e) {
            e.preventDefault();
            let confirmDelete = confirm('Bạn có chắc chắn muốn xoá ebook này?');
            if (confirmDelete) {
                let element = $(this);
                $.ajax({
                    url: element.data('url'),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(res) {
                        element.parents('tr').remove();
                        alert('Đã xoá đề kiểm tra');
                    }
                });
            }
        });

        //update parameter when change filter
        function updateParameter(urlCurrent, key, value) {
            let url = new URL(urlCurrent);
            let search_params = url.searchParams;

            // new value of "key" is set to "value"
            search_params.set(key, value);
            //fetch data
            search_params.set('page', '1');

            // change the search property of the main url
            url.search = search_params.toString();

            return url.toString();
        }

        $('.reset-filter').click(function() {
            window.location.href = window.location.href.substring(0, window.location.href.indexOf('?'))
        });

        $('.filter-level').change(function() {
            let optionSelected = $(this).find("option:selected");
            window.location.href = updateParameter(window.location.href, 'level', optionSelected.val())
        });

        $('.filter-grade').change(function() {
            let optionSelected = $(this).find("option:selected");
            window.location.href = updateParameter(window.location.href, 'grade', optionSelected.val())
        });

        $('.filter-subject').change(function() {
            window.location.href = updateParameter(window.location.href, 'subjectId', $(this).val())
        });

        $('.filter-assemblage').change(function() {
            window.location.href = updateParameter(window.location.href, 'assemblage', $(this).val())
        });

        $('.filter-ebook-category').change(function() {
            window.location.href = updateParameter(window.location.href, 'ebookCategory', $(this).val())
        });

        $('.filter-collaborator-category').change(function() {
            window.location.href = updateParameter(window.location.href, 'selectedCollaborator', $(this).val())
        });

        $('.input-search').click(function() {
            window.location.href = updateParameter(window.location.href, 'search', $('#search').val())
        });

        $('.dataTables_length select').change(function() {
            window.location.href = updateParameter(window.location.href, 'limit', $(this).val())
        })

    });
</script>
@endsection