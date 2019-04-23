@extends('layout.guest.template', [
    'title' => __('seo.title.cleanSrt'),
    'description' => __('seo.description.cleanSrt'),
    'keywords' => __('seo.keywords.cleanSrt'),
])

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Remove formatting from Srt subtitles</h1>
    <p>
        Cleans srt files by stripping html tags and other formatting
    </p>


    @component('components.tool-form')

        @slot('title') Select files to clean @endslot

        @slot('formats') Supported subtitle formats: srt @endslot

        @slot('buttonText') Clean @endslot

        @slot('extraAfter')
            <label class="block">
                <input type="hidden" name="stripParentheses" value="" />
                <input type="checkbox" name="stripParentheses" value="1" {{ old('stripParentheses', true) ? 'checked' : '' }}>
                Strip text between parentheses ( )
            </label>

            <label class="block my-3">
                <input type="hidden" name="stripCurly" value="" />
                <input type="checkbox" name="stripCurly" value="1" {{ old('stripCurly', true) ? 'checked' : '' }}>
                Strip text between curly brackets { }
            </label>

            <label class="block my-3">
                <input type="hidden" name="stripAngle" value="" />
                <input type="checkbox" name="stripAngle" value="1" {{ old('stripAngle', true) ? 'checked' : '' }}>
                Strip text between angle brackets &lt; &gt;
            </label>

            <label class="block mb-3">
                <input type="hidden" name="stripSquare" value="" />
                <input type="checkbox" name="stripSquare" value="1" {{ old('stripSquare', true) ? 'checked' : '' }}>
                Strip text between square brackets [ ]
            </label>

            <label class="block mb-3">
                <input type="hidden" name="stripCuesWithMusicNote" value="" />
                <input type="checkbox" name="stripCuesWithMusicNote" value="1" {{ old('stripCuesWithMusicNote') ? 'checked' : '' }}>
                Strip any cue containing a music note (♪)
            </label>

            <label class="block mb-3">
                <input type="hidden" name="stripSpeakerLabels" value="" />
                <input type="checkbox" name="stripSpeakerLabels" value="1" {{ old('stripSpeakerLabels') ? 'checked' : '' }}>
                Strip speaker labels
            </label>
        @endslot

    @endcomponent


    <h2>Cleaning srt files</h2>
    <p>
        Srt subtitles sometimes contain style formatting tags. Unfortunately, many video players don't support formatting and display them as plain text.
        Examples of formatting are italic {{ '<i></i>' }}, bold {{ '<b></b>' }} or colored text {{ '<font></font>' }}.
        This tool strips all html formatting that is contained in angle brackets.
        It can also remove song text and lyrics.
        <br/><br/>
        The cleaner also converts the file to UTF-8 text encoding, the cues will be sorted based on their start time, and duplicate or empty cues will be removed.
    </p>

    <h3>Cleaning hearing-impaired subtitles</h3>
    <p>
        The <i>strip text between parentheses</i> option can be used to turn subtitles for the deaf and hard-of-hearing (SDH subtitles) into regular subtitles.
        This option will remove any SDH text (which should be between parentheses), leaving only dialogue cues.
        If the SDH text is contained in different kinds of brackets, you can use one of the other options to strip them too.
        <br><br>
        The <strong>strip speaker labels</strong> option is also useful when working with SDH subtitles.
        It removes the name of the speaker from the beginning of lines.
        In a dialogue cue, any uppercase text before a colon (:) is considered a speaker label.
        For example, a cue containing the text "GEORGE: The summer of George!" will be changed to "-The summer of George!".
    </p>

    <h3>Other formatting tags</h3>
    <p>
        This tool also removes all effects that are leftover when converting a subtitle format to srt.
        Most notably, it removes formatting effects contained in curly brackets (eg: {\f4}) which come from substation alpha subtitles.
        The <a href="{{ route('convertToSrt') }}">srt converter</a> tool will properly remove these effects when converting to srt, but many other tools available online do not.
    </p>

@endsection
