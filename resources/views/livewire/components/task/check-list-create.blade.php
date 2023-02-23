<div>
    <label for="checklist" class="d-block mb-2">Danh mục</label>
    @foreach ($checkLists as $key => $item)
        <div class="form-check">
            <label class="form-check-label">
                <input type="checkbox" checked class="form-check-input" name="check_list_ids[]" value="{!! $item['id'] !!}">
                {!! $item['title'] !!}
            </label>
        </div>
    @endforeach
    <a href="#" class="btn btn-link pl-0 text-decoration-underline" data-toggle="modal" data-target="#checkListCreate">
        Tạo mới danh mục
    </a>

    <!-- Modal -->
    <div class="modal fade" id="checkListCreate" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="exampleModalLongTitle">Tạo mới danh mục</h5>
                    <button type="button" class="close" wire:click="closePopup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Tiêu đề<sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" wire:model="title" id="title" autocomplete="off">
                        @error('title') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea type="text" class="form-control" wire:model="description" id="description"
                                  rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" wire:click="closePopup">Đóng</button>
                    <button type="button" class="btn btn-primary" wire:click.prevent="saveCheckList">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</div>
