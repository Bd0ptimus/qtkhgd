<div>
    <div class="form-group">
        @if(count($checkLists))
            <label for="checklist" class="d-block mb-2">Danh mục</label>
            <div class="progress mb-2">
                <div class="progress-bar" role="progressbar" style="width: {{$widthBar}}"
                     aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$percent}}
                </div>
            </div>
        @endif
        @foreach ($checkLists as $key => $item)
            <div class="form-check mb-1 check">
                <input type="checkbox" class="form-check-input" wire:model="checked.{{$item->id}}" value="{{$item->id}}"
                       id="exampleCheck{{$key}}">
                <label class="form-check-label font-weight-normal"
                       for="exampleCheck{{$key}}">{!! $item->title !!}</label>
            </div>
        @endforeach

        @if($roleAddCheckList)
            <a href="#" class="btn btn-link pl-0 text-decoration-underline" data-toggle="modal" data-target="#checkCreate">
                Tạo mới danh mục
            </a>
        @endif

        <!-- Modal -->
        <div class="modal fade" id="checkCreate" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit.prevent="saveCheckList">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Tạo mới danh mục</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="title">Tiêu đề</label>
                                <input type="text" class="form-control" wire:model="title" id="title">
                                @error('title') <span class="error text-danger">{{ 'Giá trị tiêu đề là bắt buộc' }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="description">Mô tả</label>
                                <textarea type="text" class="form-control" wire:model="description" id="description"
                                          rows="3"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
