<?php

namespace App\Http\Controllers;

use App\File;
use App\Http\Requests\UploadFileRequest;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;
use Yajra\DataTables\Facades\DataTables;

class FileController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('files.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('files.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UploadFileRequest $request
     * @return Response
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
     * Store a newly created resource in storage.
     *
     * @param UploadFileRequest $request
     * @return array
     */
    public function api_store(UploadFileRequest $request)
    {
        $user = auth()->user();
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
        $file->user_id = $user->id;
        $file->size = Storage::size($file->file_location);
        $file->save();
        return ['file' => $file, 'view_url' => route('file.show', ['file' => $file->filename]), 'delete_url' => URL::signedRoute('file.request.delete', ['file' => $file->id])];
    }


    /**
     * Display the specified resource.
     *
     * @param $filename
     * @return Response
     * @throws FileNotFoundException
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
     * @return RedirectResponse
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
     * @return RedirectResponse
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
