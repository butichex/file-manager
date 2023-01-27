<?php

namespace dyutin\FileManager\Requests;

use Illuminate\Support\Str;

class FileDeleteRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     * @codeCoverageIgnore
     * @return bool
     */
    public function authorize(): bool
    {
        return Str::of(realpath(dirname($this->path)))->startsWith($this->getClientBasePath());
    }

    /**
     * Get the validation rules that apply to the request.
     * @codeCoverageIgnore
     * @return array
     */
    public function rules(): array
    {
        return [
            'path' => 'required|string'
        ];
    }
}
