<?php

namespace Tests\Unit\Jobs\FileJobs;

use App\Jobs\FileJobs\ConvertToUtf8Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SjorsO\TextFile\Facades\TextEncoding;
use Tests\CreatesFileGroups;
use Tests\TestCase;

class ConvertToUtf8JobTest extends TestCase
{
    use RefreshDatabase, CreatesFileGroups;

    /** @test */
    function it_converts_a_file_to_utf8()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToUtf8Job($fileGroup, $this->testFilesStoragePath.'text/ass/three-cues.ass')
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues.ass', $fileJob->original_name);

        $this->assertSame('three-cues.ass', $fileJob->originalNameWithNewExtension);

        $newFilePath = $fileJob->outputStoredFile->filePath;

        $encoding = TextEncoding::detectFromFile($newFilePath);

        $this->assertSame('UTF-8', $encoding);
    }

    /** @test */
    function it_rejects_non_text_files()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToUtf8Job($fileGroup, $this->testFilesStoragePath.'text/fake/dat.ass')
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('messages.not_a_text_file', $fileJob->error_message);

        $this->assertNull($fileJob->output_stored_file_id);
    }
}