<div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                @if(!$showEdit)
                    <h4 wire:click.prevent="showEditTitle">{!! $title !!}</h4>
                @else
                    <form wire:submit.prevent="updateTaskTitle">
                        <input class="form-control" wire:model="title" type="text" placeholder="Tiêu đề">
                        @error('title') <span class="error text-danger">{{ $message }}</span> @enderror
                        <div class="text-right mt-1">
                            <button type="button" class="btn btn-danger" wire:click.prevent="cancelUpdateTaskTitle">
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
