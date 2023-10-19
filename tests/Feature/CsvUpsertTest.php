<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvUpsertTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_upsert(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $csvFile = new UploadedFile(storage_path('app/test/test.csv'), 'test.csv');

        $this->post(route('upload'), [
            'file' => $csvFile,
        ]);

        $this->assertDatabaseCount('products', 17);

        Storage::disk('public')->deleteDirectory('csv');
    }
}
