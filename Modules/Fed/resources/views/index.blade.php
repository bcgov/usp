@extends('fed::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('fed.name') !!}</p>
@endsection
