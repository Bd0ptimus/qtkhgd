<div>
    @if(!$roleEdit)
        <label for="priority" class="d-block">Trạng thái: <button class="btn btn-{{$task->currentStatus->color}}">{{$task->currentStatus->title}}</button></label>
    @else
        <label for="priority" class="d-block">Trạng thái</label>
        <select id="priority" wire:model="task_status" wire:change="handleUpdatedTaskStatus" class="form-control parent">
            @foreach ($status as $key => $item)
                <option value="{{ $item->id }}" @if($item->id == $task->status) selected @endif>{!! $item->title !!}</option>
            @endforeach
        </select>
    @endif
</div>
