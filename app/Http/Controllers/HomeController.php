<?php

namespace App\Http\Controllers;

use App\Jobs\UpsertProductJob;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $uploads = Upload::all();

        return view('home', [
            'uploads' => $uploads,
        ]);
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');

        if (! $file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $hash = md5_file($file->getRealPath());

        $existingFile = Upload::where('hash', $hash)->first();

        if ($existingFile) {
            Storage::disk('public')->delete($existingFile->path);

            $existingFile->update([
                'status' => Upload::STATUS_PENDING,
                'hash' => $hash,
                'path' => $file->store('csv', 'public'),
                'uploaded_at' => now(),
            ]);

            dispatch(new UpsertProductJob($existingFile));

            return redirect()->back()->with('success', 'File updated.');
        }

        try {
            $upload = Upload::create([
                'path' => $file->store('csv', 'public'),
                'hash' => $hash,
                'status' => Upload::STATUS_PENDING,
                'uploaded_at' => now(),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'File upload failed.');
        }

        dispatch(new UpsertProductJob($upload));

        return redirect()->back()->with('success', 'File uploaded successfully.');
    }
}
