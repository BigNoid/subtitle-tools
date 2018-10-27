<?php

namespace App\Jobs\Diagnostic;

use App\Subtitles\TextFile;
use App\Subtitles\Tools\Options\ToPlainTextOptions;
use App\Subtitles\Tools\ToPlainText;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Support\Facades\TextFileFormat;
use App\Support\TextFile\Facades\TextEncoding;
use App\Support\TextFile\Facades\TextFileReader;
use App\Models\StoredFile;
use App\Models\StoredFileMeta;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use LanguageDetection\Language;

class CollectStoredFileMetaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 30;

    protected $storedFile;

    public function __construct(StoredFile $storedFile)
    {
        $this->storedFile = $storedFile;
    }

    public function handle()
    {
        $filePath = $this->storedFile->filePath;

        if (StoredFileMeta::query()->where('stored_file_id', $this->storedFile->id)->count() > 0) {
            Log::error("Tried running a CollectStoredFileMetaJob for a stored file that already has meta info ({$this->storedFile->id})");
            return;
        }

        if (!file_exists($filePath)) {
            Log::error("CollectStoredFileMetaJob: file does not exist ({$this->storedFile->id})");
            return;
        }

        $meta = new StoredFileMeta();

        $meta->stored_file_id = $this->storedFile->id;

        $meta->size = filesize($filePath);

        $meta->mime = file_mime($filePath);

        if (is_text_file($filePath)) {
            $meta->is_text_file = true;

            $meta->encoding = TextEncoding::detectFromFile($filePath);

            $subtitleFormat = TextFileFormat::getMatchingFormat($filePath);

            $meta->identified_as = get_class($subtitleFormat);

            $fileLines = read_lines($filePath);

            $meta->line_count = count($fileLines);

            $fileContent = read_content($filePath);

            $lineEndings = [
                "\r\n" => 'CRLF',
                "\n" => 'LF',
                "\r" => 'CR',
            ];

            $highestCount = 0;
            $detectedEol = 'unknown';

            foreach (array_keys($lineEndings) as $eol) {
                $count = substr_count($fileContent, $eol);

                if ($count > $highestCount) {
                    $highestCount = $count;
                    $detectedEol = $eol;
                }
            }

            $meta->line_endings = $detectedEol === 'unknown' ? 'unknown' : $lineEndings[$detectedEol];


            try {
                if ($subtitleFormat instanceof TransformsToGenericSubtitle) {
                    $tool = new ToPlainText();

                    $options = new ToPlainTextOptions();

                    $options->newLineBetweenCues = false;

                    $detectableContent = $tool->options($options)->convert($subtitleFormat)->getContent();
                } else {
                    $detectableContent = $fileContent;
                }

                $language = (string) (new Language)->detect($detectableContent);
            } catch (Exception $e) {
                Log::error('Language detection exception for stored file id: '.$meta->stored_file_id);
                Log::error($e->getMessage());

                $language = 'failed';
            }

            $meta->language = $language;
        }

        $meta->save();
    }

    public function failed(Exception $exception)
    {
        Log::error("Failed collecting stored file meta for {$this->storedFile->id}");
        Log::error($exception->getMessage());
    }
}
