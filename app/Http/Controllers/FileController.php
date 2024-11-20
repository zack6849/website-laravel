<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Models\File;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class FileController extends Controller
{

    public function index(): Renderable
    {
        return view('files.index');
    }


    public function create(): Renderable
    {
        return view('files.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UploadFileRequest $request)
    {
        $uploaded_file = $request->file("file");
        $name = $uploaded_file->getClientOriginalName();
        $storagePath = config('upload.storage.path');
        //create a new filename if there's a duplicate, since it's effectively a slug.
        while (File::whereOriginalFilename($name)->exists()) {
            $name = implode(".", [Str::random(),  $uploaded_file->getClientOriginalExtension()]);
        }
        $file = new File();
        $file->file_location = $uploaded_file->storeAs($storagePath, $name);
        $file->filename = str_replace($storagePath . DIRECTORY_SEPARATOR, '', $file->file_location);
        $file->original_filename = $name;
        $file->mime = $uploaded_file->getMimeType();
        $file->user_id = auth()->user()->id;
        $file->size = Storage::size($file->file_location);
        $file->save();
        if($request->expectsJson()){
            return [
                'file' => $file,
                'view_url' => route('file.show', ['file' => $file->filename]),
                //signed route, so you can't tamper with the deletion URL and change IDs or something.
                'delete_url' => URL::signedRoute('file.delete', $file)
            ];
        }
        return redirect(route('file.index'));
    }

    public function show(File $file)
    {
        //stream the file, useful for large files.
        $stream = Storage::getDriver()->readStream($file->file_location);
        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $file->mime,
            'Content-disposition' => 'inline; filename="' . $file->original_filename . '"'
        ]);
    }

    /**
     * Remove the specified resource from storage via API
     *
     * @param Request $request
     * @param File $file
     * @return RedirectResponse|string[]
     */
    public function destroy(Request $request, File $file)
    {
        $success = false;
        if (Storage::exists($file->file_location)) {
            $success = Storage::delete($file->file_location);
        }
        $success = $success && $file->delete();
        if (!$success) {
            if ($request->expectsJson()) {
                return ['status' => 'File deletion failed'];
            }
            return Redirect::to(route('file.index'))->with('status', 'File deletion failed :(');
        }
        if ($request->expectsJson()) {
            return ['status' => 'file deleted'];
        }
        return Redirect::to(route('file.index'))->with('status', 'Your file was deleted!');
    }


    /**
     * GET request to delete the file
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     * @throws \Exception
     */
    public function delete(Request $request, File $file)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }
        return view('files.delete', compact('file'));
    }
}
