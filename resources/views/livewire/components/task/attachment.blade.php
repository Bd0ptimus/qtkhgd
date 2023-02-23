<div>
    <label for="desc">Tài liệu</label>
    <div class="input-group">
        <div class="add-input">
            <div class="row mb-2 pt-1">
                <div class="col-md-12">
                    @foreach ($uploaded as $key => $item)
                        <div class="form-check">
                            <label class="form-check-label font-weight-normal">
                                <input type="checkbox" checked class="form-check-input" name="attachments[]"
                                       value="{!! $item['path'] !!}">
                                {!! $item['name'] !!}
                            </label>
                        </div>
                    @endforeach
                    <p class="btn btn-link pl-0 text-decoration-underline mb-0" id="add-file">
                        Tải thêm tệp
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <input type="file" class="form-control d-none" wire:model="files" multiple id="file-upload">
                        @error('file.*') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
