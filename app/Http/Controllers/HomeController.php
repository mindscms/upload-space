<?php

namespace App\Http\Controllers;

use App\Models\TransferFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function show($transfer_id, $file_id)
    {
        $document = TransferFile::where([
            'transfer_id' => $transfer_id,
            'id' => $file_id
        ])->first();

        if (! \request()->user()) {
            abort(403);
        }

        $storage = Storage::disk($document->disk);

        return \response($storage->get($document->path))
            ->header('Content-Type', $storage->mimeType($document->path));
    }














}
