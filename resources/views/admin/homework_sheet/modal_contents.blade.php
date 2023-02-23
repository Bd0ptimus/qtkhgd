@foreach($homeworkSheets as $key => $homeworkSheet)
    <div class="modal fade" id="modalContent{{$homeworkSheet->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xem bài giảng mẫu</h5>
                    <button type="button" class="close" data-dismiss="modal" data-target="#modalContent{{$homeworkSheet->id}}" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            
                <div class="modal-body">
                    <div class="table-responsive">
                        <textarea class='form-control description' name='content'>{{ $homeworkSheet->content}}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach