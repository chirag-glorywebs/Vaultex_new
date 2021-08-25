<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkOrder extends FormRequest
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
        $rules = [
            'product_or_category_details' => 'required',
            'quantity' => 'required',
            'brand' => 'required',
            'name' => ['required', 'min:2', 'max:50',
                'regex:/^[a-zA-Z. \s-]+$/'],
            'email' => 'required|email',
            'phone' => [
                'required',
                'numeric',
                'digits_between:10,20'],
            'description' => [
                'required',
                'min:2',
                'max:255',
                //'regex:/^[A-Za-z0-9 ]+$/',
            ]
        ];
       /* foreach ($this->request->get('product_or_category_details') as $key => $val) {
            if (empty($val['product_or_category_details'])) {
                $rules['product_or_category_details. ' . $key] = 'required';
            }
        }*/
        /*     foreach ($this->request->get('quantity') as $key => $val) {
                 $rules['quantity. ' . $key] = 'required';
             }
             foreach ($this->request->get('brand') as $key => $val) {
                 $rules['brand. ' . $key] = 'required';
             }*/
        return $rules;
    }

    public function messages()
    {
        return [
            'product_or_category_details.required' => 'The Product or Category field is required.',
            'brand.required' => 'The Brand field is required.',
            'quantity.required' => 'The Quantity field is required.',
            'name.required' => 'The Name field is required.',
            'name.min' => 'The Name may not be less than 2 characters.',
            'name.max' => 'The Name may not be greater than 50 characters.',
            'name.regex' => 'Name should contain alphabets, white space and . operator only',
            'phone.required' => 'The Phone field is required.',
            'phone.numeric' => 'The Phone must be numeric.',
            'phone.digits_between' => 'The Phone must be between 10 and 20 digits.',
            'description.required' => 'The Description field is required.',
            'description.min' => 'The Description may not be less than 2 characters.',
            'description.max' => 'The Description may not be greater than 255 characters.',
        ];
    }
}
