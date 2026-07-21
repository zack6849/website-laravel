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
            'callsign' => ['required', 'string', 'max:32'],
            'frequency' => ['required', 'string', 'max:32'],
            'band' => ['required', 'string', 'max:32'],
            'modeDetail' => ['required', 'string', 'max:64'],
            'time' => ['required', 'date'],
            'spotterEntity' => ['required', 'string', 'max:128'],
            'spotter' => ['required', 'string', 'max:32'],
        ];
    }
}
