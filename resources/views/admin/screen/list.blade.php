@extends('layouts.contentLayoutMaster')

@section('vendor-style')
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('admin/AdminLTE/plugins/iCheck/square/blue.css')}}">
@endsection

@section('main')

<div class="col-md-12">
  <div class="card">
    <div class="card-header">
      
        
        <div class="clearfix">
          
            {!! $menu_left??'' !!}
            {!! $menu_sort??'' !!}

            <!--- loc dai ly --->
            @if ( $title == 'Danh sách người dùng')
            {!! $menu_filter??'' !!}
            @endif
          
        </div>
        <div class="clearfix">
          <div class="pull-right">
            {!! $menu_right??'' !!}
          </div>
          <div class="pull-right">
            {!! $menu_search??'' !!}
          </div>
        </div>
    </div>
    <div class="card-content">
      <div class="card-body card-dashboard">
        <div class="form-group   {{ $errors->has('amount') ? ' has-error' : '' }}">  
            @if ($errors->has('amount'))
                <span class="help-block">
                    {{ $errors->first('amount') }}
                </span>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
      
          <!-- /.box-header -->
        <section id="pjax-container" class="table-list">
          <div class="table-responsive no-padding" >
            <table class="table table-hover">
                <thead>
                  <tr>
                    @foreach ($listTh as $key => $th)
                        <th>{!! $th !!}</th>
                    @endforeach
                  </tr>
                </thead>
                <tbody>
                        @foreach ($dataTr as $keyRow => $tr)
                            <tr>
                                @foreach ($tr as $key => $trtd)
                                    <td>{!! $trtd !!}</td>
                                @endforeach
                            </tr>
                        @endforeach
                </tbody>
            </table>
          </div>
          <div class="box-footer clearfix">
            {!! $result_items??'' !!}
            {!! $pagination??'' !!}
          </div>
          <script>
            $('.update-status').on('click', function(){
                $('#updateStatus #agencyName').text($(this).data('name'));
                $("#updateStatus #selection option[value="+`${$(this).data("status")}`+"]").attr('selected', 'selected');
                $('#updateStatus form').attr('action', "{!! url('/portal/auth/user/update_status') !!}" + `/${$(this).data("userid")}`);
                $('#updateStatus').modal('show');
          });
          </script>
        </section>
      </div>
    </div>
  </div>
</div>
@endsection

<div class="modal fade text-left" id="plusBalance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">{{ __('user.admin.plus_balance_title')}}  <span id="agencyName"></span></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" action="">
            @csrf
            {{ method_field('PATCH') }}
            <div class="modal-body">

                <!-- Customer Info -->
                <div class="row">
                    <div class="col-md-4">
                        <label>{{ __('user.admin.plus_balance_amount') }}</label>
                        <div class="form-group">
                            <input required type="number" autocomplete="off" class="form-control"
                                name="amount" id="amount" placeholder="{{ __('user.admin.plus_balance_amount') }}">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label>{{ __('user.admin.plus_balance_note') }}</label>
                        <div class="form-group">
                            <input required type="text" autocomplete="off" class="form-control"
                                name="note" id="note" placeholder="{{ __('user.admin.plus_balance_note') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="this.form.submit();this.disabled = true;" type="submit" class="btn btn-success">Submit</button>
            </div>
        </form>
    </div>
  </div>
</div>

<div class="modal fade" id="minusBalance" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">{{ __('user.admin.minus_balance_title')}}  <span id="agencyName"></span></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form method="POST" action="">
          @csrf
          {{ method_field('PATCH') }}
          <div class="modal-body">

              <!-- Customer Info -->
              <div class="row">
                  <div class="col-md-4">
                      <label>{{ __('user.admin.minus_balance_amount') }}</label>
                      <div class="form-group">
                          <input required type="number" autocomplete="off" class="form-control"
                              name="amount" id="amount" placeholder="{{ __('user.admin.minus_balance_amount') }}">
                      </div>
                  </div>
                  <div class="col-md-8">
                      <label>{{ __('user.admin.minus_balance_note') }}</label>
                      <div class="form-group">
                          <input required type="text" autocomplete="off" class="form-control"
                              name="note" id="note" placeholder="{{ __('user.admin.minus_balance_note') }}">
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button onclick="this.form.submit();this.disabled = true;" type="submit" class="btn btn-success">Submit</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="updateStatus" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Thay đổi trạng thái user: <span id="agencyName"></span></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
          <form method="POST" action="">
              @csrf
              {{ method_field('PATCH') }}
              <div class="modal-body">
                  <!-- Customer Info -->
                  <div class="row">
                      <div class="col-md-8">
                          <label>Chọn trạng thái</label>
                          <div class="form-group">
                              <select id="selection" class="form-control" name="status">
                                  <option value="1">Hoạt động</option>
                                  <option value="0">Tạm khoá</option>
                              </select>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn-success">Submit</button>
              </div>
          </form>
        </div>
    </div>
</div>


@push('styles')
<style type="text/css">
  .box-body td,.box-body th{
  max-width:150px;word-break:break-all;
}
</style>
@endpush

@push('scripts')
  <!-- iCheck -->
  <script src="{{ asset('admin/AdminLTE/plugins/iCheck/icheck.min.js')}}"></script>

  {{-- //Pjax --}}
  <script src="{{ asset('admin/plugin/jquery.pjax.js')}}"></script>

  <script type="text/javascript">

    $('.grid-refresh').click(function(){
      $.pjax.reload({container:'#pjax-container'});
    });

    $(document).on('submit', '#button_search', function(event) {
      $.pjax.submit(event, '#pjax-container')
    })

    $(document).on('pjax:send', function() {
      $('#loading').show()
    })
    $(document).on('pjax:complete', function() {
      $('#loading').hide()
    })

    // tag a
    $(function(){
     $(document).pjax('a.page-link', '#pjax-container')
    })


    $(document).ready(function(){
    // does current browser support PJAX
      if ($.support.pjax) {
        $.pjax.defaults.timeout = 2000; // time in milliseconds
      }

      $('.plus-balance').on('click', function(e){
          $('#plusBalance #agencyName').text($(this).data('name'));
          $('#plusBalance form').attr('action', "{!! url('/portal/user/plus_balance') !!}" + `/${$(this).data("userid")}`);
          $('#plusBalance').modal('show');
      });

      $('.minus-balance').on('click', function(){
          $('#minusBalance #agencyName').text($(this).data('name'));
          $('#minusBalance form').attr('action', "{!! url('/portal/user/minus_balance') !!}" + `/${$(this).data("userid")}`);
          $('#minusBalance').modal('show');
      });

      $('.update-status').on('click', function(){
          $('#updateStatus #agencyName').text($(this).data('name'));
          $("#updateStatus #selection option[value="+`${$(this).data("status")}`+"]").attr('selected', 'selected');
          $('#updateStatus form').attr('action', "{!! url('/portal/auth/user/update_status') !!}" + `/${$(this).data("userid")}`);
          $('#updateStatus').modal('show');
      });

      $(document).click(function (e) {
          if ($(e.target).is('#plusBalance')) {
              $('#plusBalance').modal('hide');
          }
          if ($(e.target).is('#minusBalance')) {
              $('#minusBalance').modal('hide');
          }
      });
      $('#button_filter').on('click', function(){
        var url = "{!! route('admin_user.index') !!}";
          url = addUrlParam(url, 'role', $('select[name="roles_filter"]').val());
          $.pjax({url: url, container: '#pjax-container'});
    });
    function addUrlParam(url, param, value) {
        if(url.includes('?')) {
            url += `&${param}=${value}`;
        }else{
            url += `?${param}=${value}`;
        }
        return url;
    }

    });

    {!! $script_sort??'' !!}

    $(document).on('ready pjax:end', function(event) {
      $('.table-list input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' /* optional */
      });
    })
    
  </script>
    {{-- //End pjax --}}


<script type="text/javascript">
{{-- sweetalert2 --}}
var selectedRows = function () {
    var selected = [];
    $('.grid-row-checkbox:checked').each(function(){
        selected.push($(this).data('id'));
    });

    return selected;
}

$('.grid-trash').on('click', function() {
  var ids = selectedRows().join();
  deleteItem(ids);
});

  function deleteItem(ids){
    if (checkIfAccountIsDemo()) return false;
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: 'btn btn-success',
      cancelButton: 'btn btn-danger'
    },
    buttonsStyling: true,
  })

  swalWithBootstrapButtons.fire({
    title: 'Bạn có chắc chắn muốn xoá hết các đối tượng đã chọn?',
    text: "",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Tôi chắc chắn!',
    confirmButtonColor: "#DD6B55",
    cancelButtonText: 'Không, Bỏ qua!',
    reverseButtons: true,

    preConfirm: function() {
        return new Promise(function(resolve) {
            $.ajax({
                method: 'post',
                url: '{{ $url_delete_item }}',
                data: {
                  ids:ids,
                    _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                    //console.log(data);
                    if(data.error == 1){
                      swalWithBootstrapButtons.fire(
                        'Cancelled',
                        data.msg,
                        'error'
                      )
                      $.pjax.reload('#pjax-container');
                      return;
                    }else{
                      $.pjax.reload('#pjax-container');
                      resolve(data);
                    }

                }
            });
        });
    }

  }).then((result) => {
    if (result.value) {
      swalWithBootstrapButtons.fire(
        'Hoàn thành!',
        'Đã xoá dữ liệu được chọn.',
        'success'
      )
    } else if (
      // Read more about handling dismissals
      result.dismiss === Swal.DismissReason.cancel
    ) {
      // swalWithBootstrapButtons.fire(
      //   'Cancelled',
      //   'Your imaginary file is safe :)',
      //   'error'
      // )
    }
  })
}
{{--/ sweetalert2 --}}


</script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
@endpush
