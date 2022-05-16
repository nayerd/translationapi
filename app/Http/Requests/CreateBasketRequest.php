<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBasketRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'project_id'            => 'string|nullable',
            'customer_id'           => 'string|nullable',
            'expected_due_date'     => 'date|nullable',
            'target_languages'      => 'array|min:1|nullable',
        ];
    }
}
