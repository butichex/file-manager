<?php

namespace dyutin\FileManager\Requests;

use Illuminate\Support\Facades\File;

class FileCopyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @codeCoverageIgnore
     * @return bool
     */
    public function authorize(): bool
    {
        if (!$this->validatePath($this->from)) {
            return false;
        }

        if (!File::exists(realpath(dirname($this->from)))) {
            return false;
        }

        return $this->validatePath($this->to);
    }

    /**
     * Get the validation rules that apply to the request.
     * @codeCoverageIgnore
     * @return array
     */
    public function rules(): array
    {
        return [
            'from' => 'required',
            'to' => 'required',
            'name' => 'required',
        ];
    }
}
