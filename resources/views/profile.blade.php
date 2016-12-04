@extends('layouts.app')

@section('content')
    <profile :user="{{ $user }}" :shelves="{{ $shelves }}"></profile>
    <profile-shelves :user="{{ $user }}" :shelves="{{ $shelves }}"></profile-shelves>
@endsection
