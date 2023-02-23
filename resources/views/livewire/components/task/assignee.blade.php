<div>
    @if(!$roleEdit)
        <label for="assignee" class="d-block">Người được chỉ định: </label>
        <p class="text-primary">{{collect($task->assigned)->implode('name', ', ')}}</p>
    @else
        <form wire:submit.prevent="handleUpdateAssignee">
            <label for="assignee" class="d-block">Người được chỉ định</label>
            <select wire:model="assignee" multiple="multiple"
                    class="form-control parent select2" id="select2-assigned">
                @foreach ($users as $key => $item)
                    <option value="{{ $item['id'] }}"
                            @if(in_array($item['id'], $assigned)) selected @endif>
                        {!! $item['name'] !!}
                    </option>
                @endforeach
            </select>
            @if($showAction)
                <div class="text-right mt-1">
                    <button type="button" class="btn btn-danger"
                            wire:click.prevent="cancelUpdateAssignee">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </button>
                </div>
            @endif
        </form>
    @endif
    @push('script')
        <script type="text/javascript">
            $(document).ready(function () {
                $('#select2-assigned').on('change', function (e) {
                    let data = $(this).val();
                @this.set('showAction', true);
                @this.set('assignee', data);
                });
            });

            window.livewire.on('assigneeSuccess', () => {
                $('#select2-assigned').select2();
            });
        </script>
    @endpush
</div>
