@extends('base-template')

@section('content')

    <h1>Subtitle Tools</h1>

    <br/>

    <a href="{{ route('convert-to-srt') }}">Convert to Srt</a>
    <br/>
    <a href="{{ route('sub-idx-index') }}">Sub/Idx to Srt</a>

@endsection