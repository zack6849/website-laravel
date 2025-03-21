<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class UploadedFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'file' => $this->toArray($request),
            'view_url' => route('file.show', ['file' => $this->filename]),
            //signed route, so you can't tamper with the deletion URL and change IDs or something.
            'delete_url' => URL::signedRoute('file.delete', $this)
        ];
    }
}
