<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddDocumentBasketRequest extends FormRequest
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
            'file_id'               => 'string|nullable',
            'file_name'             => 'string|nullable',
            'file_type'             => 'string|nullable',
            'file_content'          => 'string|nullable',
            'comments'              => 'string|nullable',
        ];
    }
}
