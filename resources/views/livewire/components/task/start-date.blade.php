<div>
    @if(!$roleEdit)
        <label for="start-date" class="d-block">Ngày bắt đầu: <a href="#">{{$task->start_date}}</a></label>
    @else
        <label for="start-date" class="d-block">Ngày bắt đầu</label>
        <input type="text" name="start_date" wire:model="start_date"
               class="form-control" autocomplete="off"
               value="{{old('start_date', $task->start_date)}}" id="start-date-edit" required>
           
    @endif
    @push('script')
        <script type="text/javascript">
            $(document).ready(function () {
                $('#start-date-edit').datepicker().on('changeDate', function (ev) {
                    let start_date = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0, 0);
                    $('#due-date-edit').datepicker('setStartDate', start_date);
                @this.set('start_date', ev.target.value);
                });
            });
        </script>
    @endpush
</div>
