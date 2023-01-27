<?php

namespace dyutin\FileManager\Requests;

class FileCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @codeCoverageIgnore
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->validatePath($this->parent . DIRECTORY_SEPARATOR . $this->name);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function rules(): array
    {
        return [
            'parent' => 'required|string'
        ];
    }
}
