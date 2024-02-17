@extends('ministry::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('ministry.name') !!}</p>
@endsection
