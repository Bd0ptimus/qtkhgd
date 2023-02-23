<!-- Modal -->
<div class="modal fade" id="taskCreate" data-backdrop="static" data-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <form action="{{route('tasks.store')}}" method="POST" role="form" class="form-create">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel"><b>Tạo mới nhiệm vụ</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="title">Tiêu đề<sup class="text-danger">*</sup></label>
                                <input type="text" name="title" class="form-control" id="title" required>
                            </div>
                            <div class="form-group">
                                <label for="desc">Sự miêu tả<sup class="text-danger">*</sup></label>
                                <textarea id="description" name="description" class="description" class="form-control" rows="20"
                                          required>{!! old('description') !!}</textarea>
                            </div>

                            <div class="form-group">
                                @livewire('components.task.check-list-create')
                            </div>

                            <div class="form-group">
                                @livewire('components.task.attachment')
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="assignee" class="d-block">Người được chỉ định<sup class="text-danger">*</sup></label>
                                <select id="assignee_id" name="assignee_ids[]" multiple="multiple" class="form-control parent select2">
                                    @foreach ($users as $key => $item)
                                        <option value="{{ $item->id }}">{!! $item->name !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="assignee" class="d-block">Người giám sát</label>
                                <select id="follower_id" name="follower_ids[]" multiple="multiple" class="form-control parent select2">
                                    @foreach ($users as $key => $item)
                                        <option value="{{ $item->id }}">{!! $item->name !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="priority" class="d-block">Sự ưu tiên<sup class="text-danger">*</sup></label>
                                <select id="priority" name="priority" class="form-control parent select2">
                                    @foreach ($taskPriority as $key => $item)
                                        <option value="{{ $item }}">{!! $key !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="start-date" class="d-block">Ngày bắt đầu<sup class="text-danger">*</sup></label>
                                <input type="text" name="start_date" class="form-control" id="start-date" required>
                            </div>
                            <div class="form-group">
                                <label for="due-date" class="d-block">Hạn hoàn thành</label>
                                <input type="text" name="due_date" class="form-control" id="due-date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
