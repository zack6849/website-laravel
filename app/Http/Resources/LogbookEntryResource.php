<?php

namespace App\Http\Resources;

use App\Models\LogbookEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin LogbookEntry
 */
class LogbookEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        //alias the callsign fields
        $value = array_merge(parent::toArray($request), [
            'from_callsign' => $this->station->name,
            'to_callsign' => $this->callee->name
        ]);
        $value['icon_size'] = 0.025;
        $value['icon'] = 'pin';
        if($this->category == 'POTA'){
            $value['icon'] = 'tree';
            $value['icon_size'] = 0.25;
        }
        unset($value['station']);
        unset($value['callee']);
        return $value;
    }
}
