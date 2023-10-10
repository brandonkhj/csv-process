<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Upload;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;

class PrintsImport implements ToModel, WithUpserts, WithBatchInserts, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    public $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function uniqueBy()
    {
        return 'unique_key';
    }

    public function model(array $row)
    {
        return new Product([
            'unique_key' => $row['unique_key'],
            'product_title' => $row['product_title'],
            'product_description' => $row['product_description'],
            'style' => $row['style'],
            'sanmar_mainframe_color' => $row['sanmar_mainframe_color'],
            'size' => $row['size'],
            'color_name' => $row['color_name'],
            'piece_price' => $row['piece_price'],
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        $upload = $this->upload;

        return [
            ImportFailed::class => function() use ($upload) {
                $upload->update([
                    'status' => Upload::STATUS_FAILED
                ]);
            },
            AfterImport::class => function() use ($upload) {
                $upload->update([
                    'status' => Upload::STATUS_COMPLETED
                ]);
            }
        ];
    }
}
