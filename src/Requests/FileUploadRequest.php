<?php

namespace dyutin\FileManager\Requests;

class FileUploadRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     * @codeCoverageIgnore
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->validatePath($this->target);
    }

    /**
     * Get the validation rules that apply to the request.
     * @codeCoverageIgnore
     * @return array
     */
    public function rules(): array
    {
        return [
            'target' => 'required'
        ];
    }
}
