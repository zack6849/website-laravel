<?php

namespace App\Http\Controllers;

use App\File;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function foo\func;

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
        return view('files.index', ['files' => auth()->user()->files()->paginate(15)]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->hasFile('file')){
            return redirect(route('file.create'))->with('error', 'No file provided');
        }
        if(!$request->file('file')->isValid()){
            return redirect(route('file.create'))->with('error', 'Failed to save file, invalid file.');
        }

        $uploaded_file = $request->file("file");
        $name = $uploaded_file->getClientOriginalName();
        while(File::whereOriginalFilename($name)->exists()){
            $name = Str::random(16) . '' . \Illuminate\Support\Facades\File::extension($uploaded_file->getClientOriginalName());
        }

        $file = new File();
        $file->file_location =  $uploaded_file->store('public/user_uploads');
        $file->filename = str_replace("public/user_uploads/", '', $file->file_location);
        $file->original_filename = $name;
        $file->mime = $uploaded_file->getMimeType();
        $file->user_id = auth()->user()->id;
        $file->save();
        return redirect(route('file.index'));
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $location
     * @return \Illuminate\Http\Response
     */
    public function show($location)
    {
        $file = File::whereFilename($location)->firstOrFail();
        $fs =  Storage::getDriver();
        $stream = $fs->readStream($file->file_location);
        return response()->stream(function () use ($stream){
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $file->mime,
            'Content-disposition' => 'inline; filename="' . $file->original_filename . '"'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
