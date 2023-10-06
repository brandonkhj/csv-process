<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $uploads = Upload::all();
        return view('home', [
            'uploads' => $uploads
        ]);
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $hash = md5_file($file->getRealPath());

        $existingFile = Upload::where('hash', $hash)->first();

        if ($existingFile) {
            return redirect()->back()->with('error', 'File already uploaded.');
        }

        try {
            $path = $file->store('csv', 'public');

            Upload::create([
                'path' => $path,
                'hash' => $hash,
                'status' => Upload::STATUS_PENDING
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'File upload failed.');
        }

        //dispatch job

        return redirect()->back()->with('success', 'File uploaded successfully.');
    }
}
