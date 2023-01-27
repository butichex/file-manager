<?php

namespace dyutin\FileManager\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use dyutin\FileManager\Exceptions\FileManagerException;
use Illuminate\Support\Str;

/**
 * @property string path
 * @property string target
 * @property string parent
 * @property string name
 * @property string from
 * @property string to
 */

abstract class FormRequest extends BaseFormRequest
{

    /**
     * @return bool|string
     * @codeCoverageIgnore
     */
    protected function getClientBasePath()
    {
        if (!$path = realpath(config('file-manager.paths.base', null))) {
            throw new FileManagerException('File manager base path not found');
        }
        return $path;
    }


    /**
     * @param $path
     * @return bool
     * @codeCoverageIgnore
     */
    protected function validatePath($path): bool
    {
        $realpath = realpath($path) ?: realpath(dirname($path));

        return Str::of($realpath)->startsWith($this->getClientBasePath());
    }
}
