<?php

namespace Tests;

use App\Models\StoredFile;
use App\Support\Facades\TempFile;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;
use Spatie\Snapshots\MatchesSnapshots;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MatchesSnapshots;

    public $testFilesStoragePath;

    private $testingStorageDirectories = [
        'sub-idx',
        'temporary-files',
        'temporary-dirs',
        'stored-files',
        'diagnostic',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->testFilesStoragePath = base_path('tests/Storage/');

        $this->ensureProperStorageDiskConfig();

        foreach($this->testingStorageDirectories as $dirName) {
            Storage::makeDirectory($dirName);
        }
    }

    public function tearDown()
    {
        $this->ensureProperStorageDiskConfig();

        foreach($this->testingStorageDirectories as $dirName) {
            Storage::deleteDirectory($dirName);
        }

        parent::tearDown();
    }

    private function ensureProperStorageDiskConfig()
    {
        $storagePath = storage_disk_file_path('/');

        if(!ends_with($storagePath, "/testing/")) {
            throw new \Exception("It looks like the storage driver is not set up properly");
        }
    }

    public function dumpSession()
    {
        dd(app('session.store'));
    }

    protected function getSnapshotDirectory(): string
    {
        return $this->testFilesStoragePath.'__snapshots__';
    }

    public function assertMatchesFileSnapshot($file)
    {
        if($file instanceof StoredFile) {
            $temporaryFilePath = TempFile::makeFilePath().'.txt';

            copy($file->file_path, $temporaryFilePath);

            $file = $temporaryFilePath;
        }

        $this->doFileSnapshotAssertion($file);
    }
}
