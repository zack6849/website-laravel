<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HamAlertSpotStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //sure, why not.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'callsign' => 'required|string',
            'frequency' => 'required|string',
            'band' => 'required|string',
            'modeDetail' => 'required|string',
            'time' => 'required|string',
            'spotterEntity' => 'required|string',
            'spotter' => 'required|string'
        ];
    }
}
