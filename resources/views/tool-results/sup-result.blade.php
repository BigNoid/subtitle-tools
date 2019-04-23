@extends('layout.guest.template', [
    'title' => 'Download sup to srt file | Subtitle Tools',
    'description' => 'Download your srt file when it is done processing',
])

@include('helpers.robots-no-index')

@section('content')

    <h1>Sup to Srt Download</h1>
    <p>
        Your sup file is being converted.
        Once it is done the page will update automatically.
    </p>


    <div class="inline-block mt-8 p-3 bg-yellow-lighter">
        <strong class="block mb-1">This tool is still in beta!</strong>
        It might not work correctly yet
    </div>


    <p class="mt-4">
        Extracting srt files from <strong>{{ $originalName }}</strong>
        using <strong>{{ __('languages.tesseract.'.$ocrLanguage) }}</strong> as the OCR language
    </p>


    <div class="flex my-8 flex-col md:flex-row">
        <sup-job url-key="{{ $urlKey }}"></sup-job>

        <div class="mx-auto md:mt-0 mt-8">
            @include('helpers.ads.result-page-large-rectangle')
        </div>
    </div>


    <a class="tool-btn inline-block" href="{{ route('sup') }}">Back to tool</a>

@endsection
