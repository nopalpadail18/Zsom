<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateGroupsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\Groups $group */
        $group = $this->route('groups');

        return $group->isAdmin(Auth::id());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'auto_approval' => ['required', 'boolean'],
            'about' => ['nullable', 'max:255'],
        ];
    }
    public function prepareForValidation(): void
    {
        $this->merge([
            'about' => nl2br($this->about),
        ]);
    }
}
