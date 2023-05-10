@extends('base')

@section('content')
    <form action="{{ route('offer.create') }}" method="POST">
        @csrf
        <label>Title</label>
        <input name="title" type="text" placeholder="title">
        <br>
        <label>Price</label>
        <input name="price" type="number" placeholder="price">
        <br>
        <label>Description</label>
        <input name="description" type="text" placeholder="description">
        <br>
        <input type="hidden" value="{{ true }}" name="isActive">
        <br>
        <input type="hidden" value="{{ (new DateTime())->format('Y-m-d H:i:s') }}" name="publishAt">
        <input type="submit">
    </form>
@endsection
