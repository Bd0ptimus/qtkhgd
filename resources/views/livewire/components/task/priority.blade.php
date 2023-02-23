<div>
    @if(!$roleEdit)
        <label for="priority" class="d-block">Sự ưu tiên: <button class="btn btn-{{$priority[$task->priority]}}">{{$priority[$task->priority]}}</button></label>
    @else
        <label for="priority" class="d-block">Sự ưu tiên</label>
        <select wire:model="task_priority" wire:change="handleUpdatedTaskPriority" class="form-control parent">
            @foreach ($priority as $key => $item)
                <option value="{{ $key }}"
                        @if($key == $task->priority) selected @endif>{!! $item !!}</option>
            @endforeach
        </select>
    @endif
</div>
