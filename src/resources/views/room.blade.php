@extends('layout')
@section('content')
<div class="container">
  @if(Auth::check())
  <private-chat :room="{{$room}}" :user="{{Auth::user()}}"></private-chat>
    @endif
</div>
@endsection