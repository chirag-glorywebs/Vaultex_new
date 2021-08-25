<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGeneralSettings extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'phone_number' => [
                'bail',
                'nullable',
                'regex:/^[+0-9 ]{2,20}+$/',
            ],
            'email' => [
                'bail',
                'nullable',
                'email',
            ],
            'logo' => 'bail|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'bail|nullable|max:2048',
            'footer_image' => 'bail|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phone_number.regex' => 'The Phone Number must be numeric with min 2 and max 20 digits.',
            'logo.max' => 'The Logo size may not be greater than 2 MB.',
            'logo.mimes' => 'The Logo must be valid jpeg,png,jpg,gif,svg extension',
            'favicon.max' => 'The Favicon size may not be greater than 2 MB.',
            'favicon.mimes' => 'The Favicon must be valid jpeg,png,jpg,gif,svg extension',
            'footer_image.max' => 'The Footer Image size may not be greater than 2 MB.',
            'footer_image.mimes' => 'The Footer Image must be valid jpeg,png,jpg,gif,svg extension',
        ];
    }
}
