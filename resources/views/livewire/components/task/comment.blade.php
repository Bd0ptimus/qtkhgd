<div>
    <div class="row" id="task-detail-comment">
        <div class="col-9">
            <hr>
            <form wire:submit.prevent="saveComment" class="form-create">
                <div class="form-group mb-1">
                    <label for="desc">Bình luận</label>
                    <div id="card-comment-tinmyce-container">
                        <textarea type="text" wire:model="comment" rows="2" id="card-comment-tinmyce" class="form-control"></textarea>
                    </div>
                    @error('comment') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Gửi</button>
                </div>
            </form>

            <ul class="chat-users-list-wrapper media-list pl-0">
                @foreach($comments as $comment)
                    <li class="d-flex border-bottom mt-1 bg-comment p-2">
                        <div class="pr-1">
                            <span class="avatar m-0 avatar-md">
                                <img class="media-object rounded-circle"
                                     src="{{$comment->creator->avatar}}"
                                     height="42" width="42" alt="Generic placeholder image">
                            </span>
                        </div>
                        <div class="user-chat-info">
                            <div class="contact-info">
                                <h5 class="font-weight-bold mb-0">{{$comment->creator->name}}</h5>
                                <p class="truncate">{!! $comment->comment !!}</p>
                            </div>
                        </div>
                        <div class="contact-meta ml-auto">
                            <span class="float-right mb-25">{{$comment->created_at->diffForHumans()}}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            {!! $comments->links() !!}
        </div>
    </div>
</div>
