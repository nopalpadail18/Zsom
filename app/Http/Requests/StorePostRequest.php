<?php

namespace App\Http\Requests;

use App\Http\Enums\GroupUserStatus;
use App\Models\GroupUsers;
use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

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
            'preview' => ['nullable', 'array'],
            'preview_url' => ['nullable', 'string'],
            'attachments' => [
                'array',
                'max:10',
                function ($attribute, $value, $fail) {
                    $totalSize = collect($value)->sum(fn (UploadedFile $file) => $file->getSize());

                    if ($totalSize > 5 * 1024 * 1024) { //5MB
                        $fail('File too large');
                    }
                }
            ],
            'attachments.*' => [
                'file',
                File::types(self::$extentions)
            ],
            'user_id' => ['numeric'],
            'group_id' => ['nullable', 'exists:groups,id', function ($attribute, $value, \Closure $fail) {
                $gropUser = GroupUsers::where('user_id', Auth::id())
                    ->where('group_id', $value)
                    ->where('status', GroupUserStatus::APPROVED->value)
                    ->exists();

                if (!$gropUser) {
                    $fail('You don\'t have access to create post in this group');
                }
            }],
        ];
    }

    public function prepareForValidation()
    {
        $body = $this->input('body') ?: '';
        $previewUrl = $this->input('preview_url') ?: '';

        $trimmedBody = trim(strip_tags($body));

        if ($trimmedBody === $previewUrl) {
            $body = '';
        }

        $this->merge([
            'user_id' => Auth::id(),
            'body' => $body,
        ]);
    }

    public function messages()
    {
        return [
            'attachments.*.file'    => 'Each file must be a valid file',
            'attachments.*.mimes'   => 'Invalid file type foe attachment',
        ];
    }
}
