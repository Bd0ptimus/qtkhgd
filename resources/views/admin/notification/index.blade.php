@extends('layouts.contentLayoutMaster')
@section('main')

@foreach($notifications as $key => $notification)
<div class="media">
  <div class="media-left">
    <a href="#">
      #{{ $key + 1 }}
    </a>
  </div>
  <div class="media-body">
    <h4 class="media-heading">{{$notification->created_at}} @if(!$notification->read) [ NEW ] @endif{{ $notification->title}} @if(!$notification->read) <a href="{{route('notification.view', ['id' => $notification->id])}}"><i class="fa fa-eye"></i></a> @endif</h4>
    {{ $notification->content}}
    <br>
    @if($notification->link)
        <a href="{{ $notification->link}}">Xem</a>
    @endif
  </div>
</div>
@endforeach


@endsection