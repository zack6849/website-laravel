<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\UploadedFileResource;
use App\Models\File;
use App\Services\FileUploadService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class FileController extends Controller
{

    public function __construct(
        private FileUploadService $uploadService
    )
    {

    }

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
        $file = $this->uploadService->storeUploadedFile($request->file('file'));
        if ($request->expectsJson()) {
            return new UploadedFileResource($file);
        }
        return redirect(route('file.index'));
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
        $success = $this->uploadService->delete($file);
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
