<div>
    <div class="form-group">
        <label for="desc mb-2 d-block">Tài liệu</label>
        @foreach ($attachments as $key => $item)
            <div class="form-check">
                - <span class="mb-0 text-black">{{$item->name}}</span>
                @if($roleDelAttachment)
                    <a class="btn btn-link text-danger text-decoration-underline" wire:click.prevent="deleteFileById({{$item->id}})">Xoá tệp</a>
                @endif
                <a href="{{route('download-attachment', $item->id)}}" class="btn btn-link text-decoration-underline" target="_blank">Tải tệp</a>
            </div>
        @endforeach

        @if($roleAddAttachment)
            <p class="btn btn-link pl-0 text-decoration-underline mb-0" id="add-file">
                Chọn thêm tệp
            </p>
            <div class="row">
                <div class="col-md-12 pl-3 mb-2">
                    @foreach ($chooses as $key => $item)
                        <p class="mb-0 text-black">{{$item}}</p>
                    @endforeach
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <button class="btn btn-primary" wire:click.prevent="addFiles">Tải lên</button>
                    </div>
                    <div class="form-group">
                        <input type="file" class="form-control d-none" wire:model="files" multiple id="file-upload">
                        @error('files') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
