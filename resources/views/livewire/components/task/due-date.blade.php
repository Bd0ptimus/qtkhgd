<div>
    @if(!$roleEdit)
        <label for="start-date" class="d-block">Ngày hoàn thành: <a href="#">{{$task->due_date}}</a></label>
    @else
        <label for="due-date" class="d-block">Hạn hoàn thành</label>
        <input type="text" name="due_date" wire:model="due_date"
               class="form-control due-date" autocomplete="off"
               value="{{old('due_date', $task->due_date)}}" id="due-date-edit"/>
    @endif
    @push('script')
        <script type="text/javascript">
            $(document).ready(function () {
                $('#due-date-edit').datepicker().on('changeDate', function (ev) {
                    let end_date = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0, 0);
                    $('#start-date-edit').datepicker('setEndDate', end_date);
                    @this.set('due_date', ev.target.value);
                });
                let startDate = $('#start-date-edit').val()
                if(startDate) {
                    const dateArray = startDate.split("/");
                    let dateFormat = dateArray[2] + '/' + dateArray[1] + '/' + dateArray[0];
                    $('#due-date-edit').datepicker('setStartDate',new Date(dateFormat));
                }
            });
        </script>
    @endpush
</div>
