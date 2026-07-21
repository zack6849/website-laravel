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
            'file' => $this->resource->toArray(),
            'user' => $this->user,
            'view_url' => route('file.show', ['file' => $this->filename]),
            // ShareX stores this locally for later use, so the API response keeps
            // a permanent signed URL while the web UI can use temporary links.
            'delete_url' => URL::signedRoute('file.delete', $this),
        ];
    }
}
