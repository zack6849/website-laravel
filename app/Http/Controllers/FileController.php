<?php

namespace App\Http\Controllers;

use App\File;
use App\Http\Requests\UploadFileRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();
        return view('files.index', ['files' => $user->files()->orderBy('created_at', 'desc')->paginate(15)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('files.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UploadFileRequest $request)
    {
        $uploaded_file = $request->file("file");
        $name = $uploaded_file->getClientOriginalName();
        while (File::whereOriginalFilename($name)->exists()) {
            $name = implode(".", [Str::random(16),  $uploaded_file->getClientOriginalExtension()]);
        }
        $file = new File();
        $file->file_location = $uploaded_file->storeAs(config('upload.storage.path'), $name);
        $file->filename = str_replace(config('upload.storage.path') . DIRECTORY_SEPARATOR, '', $file->file_location);
        $file->original_filename = $name;
        $file->mime = $uploaded_file->getMimeType();
        $file->user_id = auth()->user()->id;
        $file->size = Storage::size($file->file_location);
        $file->save();
        return redirect(route('file.index'));
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $location
     * @return \Illuminate\Http\Response
     */
    public function show($filename)
    {
        $file = File::whereFilename($filename)->firstOrFail();
        $fs = Storage::getDriver();
        $stream = $fs->readStream($file->file_location);
        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $file->mime,
            'Content-disposition' => 'inline; filename="' . $file->original_filename . '"'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        /** @var User $user */
        $file = auth()->user()->files()->findOrFail($id);
        $success = true;
        if (Storage::exists($file->file_location)) {
            $success = Storage::delete($file->file_location);
        }
        $success = $success && $file->delete();
        if ($success) {
            return Redirect::back()->with('status', 'Your file was deleted!');
        }
        return Redirect::back()->with('status', 'File deletion failed :(');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function requestDestroy(Request $request, int $id)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }
        /** @var User $user */
        $file = auth()->user()->files()->findOrFail($id);
        $success = true;
        if (Storage::exists($file->file_location)) {
            $success = Storage::delete($file->file_location);
        }
        $success = $success && $file->delete();
        if ($success) {
            return Redirect::back()->with('status', 'Your file was deleted!');
        }
        return Redirect::back()->with('status', 'File deletion failed :(');
    }
}
