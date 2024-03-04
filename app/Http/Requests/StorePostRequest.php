<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{

    public static array $extentions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'mp3', 'wav', 'mp4',
        'doc', 'docx', 'pdf', 'xls', 'xlsx', 'csv',
        'zip',
    ];
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['nullable', 'string'],
            'attachments' => 'array|max:50',
            'attachments.*' => [
                'file',
                File::types(self::$extentions)->max(500 * 1024 * 1024),
            ],
            'user_id' => ['numeric'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->user()->id,
            'body' => $this->input('body') ?: ''
        ]);
    }

    public function messages()
    {
        return [
            'attachments.*' => 'File invalid',
        ];
    }
}
