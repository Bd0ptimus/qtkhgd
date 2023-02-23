<div>
    @if(!$roleEdit)
        <label for="follower" class="d-block">Người giám sát</label>
        <p class="text-primary">{{collect($task->followers)->implode('name', ', ')}}</p>
    @else
        <form wire:submit.prevent="handleUpdateFollower">
            <label for="follower" class="d-block">Người giám sát</label>
            <select wire:model="follower" multiple="multiple"
                    class="form-control parent select2" id="select2-follow">
                @foreach ($users as $key => $item)
                    <option value="{{ $item['id'] }}"
                            @if(in_array($item['id'], $isFollower)) selected @endif>
                        {!! $item['name'] !!}
                    </option>
                @endforeach
            </select>
            @if($showAction)
                <div class="text-right mt-1">
                    <button type="button" class="btn btn-danger"
                            wire:click.prevent="cancelUpdateFollower">
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
                $('#select2-follow').on('change', function (e) {
                    let data = $(this).val();
                @this.set('showAction', true);
                @this.set('follower', data);
                });
            });

            window.livewire.on('followerSuccess', () => {
                $('#select2-follow').select2();
            });
        </script>
    @endpush
</div>
