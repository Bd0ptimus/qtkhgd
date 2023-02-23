<div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                @if(!$showEdit)
                    <h4>Mô tả</h4>
                    <div wire:click.prevent="showEditDescription">{!! $description !!}</div>
                @else
                    <form wire:submit.prevent="updateTaskDescription">
                        <div id="card-comment-tinmyce-container">
                            <textarea type="text" wire:model="description" id="task-description"
                                      class="form-control" rows="10" required>{!! $description !!}</textarea>
                            @error('description') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="text-right mt-1">
                            <button type="button" class="btn btn-danger"
                                    wire:click.prevent="cancelUpdateTaskDescription">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@push('script')
{{--    <script>--}}
{{--        const editor = CKEDITOR.replace('task-description', {--}}
{{--            height: 300--}}
{{--        });--}}
{{--        editor.on('change', function(event){--}}
{{--            //console.log(event.editor.getData());--}}
{{--        @this.set('description', event.editor.getData());--}}
{{--        });--}}
{{--        //editor.setData('@this.description');--}}

{{--        window.addEventListener('contentTaskDetail', event => {--}}
{{--            editor.setData(event.detail.description)--}}
{{--            console.log('content task: ' + event.detail.description);--}}
{{--        })--}}
{{--    </script>--}}
@endpush