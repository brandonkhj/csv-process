<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_upload(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->post(route('upload'));

        $response->assertRedirect(route('home'));

        $response->assertSessionHas('error', 'File not found.');
    }

    public function test_new_csv_upload(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $csvFile = new UploadedFile(storage_path('app/test/test.csv'), 'test.csv');

        $response = $this->post(route('upload'), [
            'file' => $csvFile,
        ]);

        $response->assertRedirect(route('home'));

        $response->assertSessionHas('success', 'File uploaded successfully.');

        Storage::disk('public')->deleteDirectory('csv');
    }

    public function test_duplicate_upload(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $csvFile = new UploadedFile(storage_path('app/test/test.csv'), 'test.csv');

        $this->post(route('upload'), [
            'file' => $csvFile,
        ]);

        $response = $this->post(route('upload'), [
            'file' => $csvFile,
        ]);

        $response->assertRedirect(route('home'));

        $response->assertSessionHas('success', 'File updated.');

        Storage::disk('public')->deleteDirectory('csv');
    }
}
