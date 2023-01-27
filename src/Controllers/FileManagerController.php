<?php

namespace dyutin\FileManager\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use dyutin\FileManager\Requests\FileCopyRequest;
use dyutin\FileManager\Requests\FileCreateRequest;
use dyutin\FileManager\Requests\FileCutRequest;
use dyutin\FileManager\Requests\FileDeleteRequest;
use dyutin\FileManager\Requests\FileUploadRequest;
use dyutin\FileManager\Requests\UnzipRequest;
use dyutin\FileManager\Requests\ZipRequest;

use dyutin\FileManager\Contracts\FileServiceInterface;
use dyutin\FileManager\Services\FileService;

use dyutin\FileManager\Translation;

class FileManagerController extends Controller
{
    // @codeCoverageIgnoreStart
    private $service;

    /**
     * FileManagerController constructor.
     * @param FileServiceInterface $service
     */
    public function __construct(FileServiceInterface $service)
    {
        $this->service = $service;
    }


    /**
     * @codeCoverageIgnore
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('file-manager::index', [
            'path' => $this->service->base_path,
            'route_prefix' => config('file-manager.route.prefix', ''),
            'ds' => FileService::DS
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function showBaseItems(Request $request): JsonResponse
    {
        return response()->json($this->service->getBasePathItems($request->post('open', [])));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function children(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->getFiles($request->post('path'))->toArray()
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFileContent(Request $request): JsonResponse
    {
        $path = $this->service->absolutePath($request->post('path'));

        if (File::isFile($path)) {
            try {
                $type = File::mimeType($path);

                $text =  (
                    Str::startsWith($type, 'text') || 
                    $type == 'inode/x-empty' ||
                    in_array($type, ['application/javascript','application/json', 'application/xml'])
                );

                if ($text) {
                    $content = File::get($path);
                } elseif ($image = Str::startsWith($type, 'image')) {
                    $content = 'data:image/' . $type . ';base64,' . base64_encode(File::get($path));
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => Translation::get('file_not_read'),
                        'type' => $type,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'content' => $content,
                    'is_text' => $text,
                    'is_image' => $image,
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'path' => $path
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveFileContent(Request $request): JsonResponse
    {
        $path = $this->service->absolutePath($request->post('path'));

        $content = $request->post('content');

        if (File::isFile($path)) {
            $success = (bool) File::put($path, $content);

            return response()->json([
                'success' => $success,
                'message' => Translation::when($success, 'content_saved')->unless('content_not_saved')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => Translation::get('file_not_read')
        ]);
    }


    /**
     * @param FileCreateRequest $request
     * @return JsonResponse
     */
    public function createFile(FileCreateRequest $request): ?JsonResponse
    {
        $parent = rtrim($this->service->absolutePath($request->post('parent')), FileService::DS);

        $filename = trim($request->name, FileService::DS);

        $full_path = $parent . FileService::DS . $filename;

        try {
            if (File::exists($full_path)) {
                return response()->json([
                    'success' => false,
                    'message' => Translation::get('already_created', ['type' => 'File'])
                ]);
            }

            if (strpos($filename, FileService::DS) !== false) {
                File::ensureDirectoryExists(File::dirname($full_path));
            }

            $create = File::put($full_path, '') !== false;

            if ($create) {
                File::chmod($full_path, 0755);
            }

            return response()->json([
                'success' => $create,
                'path' => $full_path,
                'message' => Translation::when($create, 'created', ['type' => 'File'])->unless('not_created', ['type' => 'File']),
                'items' => $create ? $this->service->getBasePathItems($request->post('open', [])) : []
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param FileCreateRequest $request
     * @return JsonResponse
     */
    public function createFolder(FileCreateRequest $request): ?JsonResponse
    {
        $parent = rtrim($this->service->absolutePath($request->post('parent')), FileService::DS);

        $name = trim($request->post('name'), FileService::DS);

        $full_path = $parent . FileService::DS . $name;

        try {
            if (File::exists($full_path)) {
                return response()->json([
                    'success' => false,
                    'message' => Translation::get('already_create', ['type' => 'Folder'])
                ]);
            }


            $create = File::makeDirectory($full_path, 0755, true) !== false;


            return response()->json([
                'success' => $create,
                'path' => $full_path,
                'message' => Translation::when($create, 'created', ['type' => 'Folder'])->unless('not_created', ['type' => 'Folder']),
                'items' => $create ? $this->service->getBasePathItems($request->post('open', [])) : []
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    /**
     * @param FileCopyRequest $request
     * @return JsonResponse
     */
    public function copy(FileCopyRequest $request): JsonResponse
    {
        $path = $this->service->absolutePath($request->post('from'));

        $to = $this->service->absolutePath($request->post('to'));

        if (File::isFile($path)) {
            $target = rtrim($to, FileService::DS) . FileService::DS . $request->post('name');
            $success = File::copy($path, $target);
        //exec("cp $path $to")
        } elseif (File::isDirectory($path)) {
            $success = File::copyDirectory($path, $to);
        //exec("cp-r $path $to")
        } else {
            $success = false;
        }


        return response()->json([
            'success' => $success,
            'target' => $target ?? $to,
            'message' => Translation::when($success, 'copied')->unless('not_copied'),
            'items' => $success ? $this->service->getBasePathItems($request->post('open', [])) : []
        ]);
    }


    /**
     * @param FileCutRequest $request
     * @return JsonResponse
     */
    public function cut(FileCutRequest $request): JsonResponse
    {
        $path = $this->service->absolutePath($request->post('from'));

        $to = $this->service->absolutePath($request->post('to'));


        if (File::isFile($path)) {
            $target = rtrim($to, FileService::DS) . FileService::DS . $request->post('name');

            $success = File::move($path, $target);
        } elseif (File::isDirectory($path)) {
            $success = rename($path, $to);
        } else {
            $success = false;
        }

        return response()->json([
            'success' => $success,
            'target' => $target ?? $to,
            'message' => Translation::when($success, 'operation_success')->unless('operation_failed'),
            'items' => $success ? $this->service->getBasePathItems($request->post('open', [])) : []
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function rename(Request $request): JsonResponse
    {
        $path = $this->service->absolutePath($request->post('path'));
        $name = $this->service->absolutePath($request->post('name'));

        try {
            $success = null;

            if (File::isFile($path)) {
                $success = File::move($path, $name);
            } elseif (File::isDirectory($path)) {
                $success = File::moveDirectory($path, $name);
            }

            $message = Translation::when($success, 'rename_success')->unless('rename_failed');
        } catch (Exception $e) {
            $success = false;
            $message = $e->getMessage();
        }


        return response()->json([
            'success' => $success,
            'message' => $message,
            'items' => $success ? $this->service->getBasePathItems($request->post('open', [])) : []
        ]);
    }


    /**
     * @param FileDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(FileDeleteRequest $request): JsonResponse
    {
        $path = $this->service->absolutePath($request->post('path'));

        if (File::isFile($path)) {
            $success = File::delete($path);

            $message = Translation::when($success, 'delete_success', ['type' => 'File'])->unless('delete_failed', ['type' => 'File']);
        } elseif (File::isDirectory($path)) {
            $success = File::deleteDirectory($path);

            $message = Translation::when($success, 'delete_success', ['type' => 'Folder'])->unless('delete_failed', ['type' => 'Folder']);
        } else {
            $success = false;
            $message = Translation::get('item_not_found_or_not_read');
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
        ]);
    }


    /**
     * @param ZipRequest $request
     * @return JsonResponse
     */
    public function compress(ZipRequest $request): JsonResponse
    {
        $response = $this->service->zip(
            $this->service->absolutePath($request->post('path')),
            $this->service->absolutePath($request->post('name'))
        );

        if ($response['success']) {
            $response['items'] = $this->service->getBasePathItems($request->post('open', []));
        }

        return response()->json($response);
    }


    /**
     * @param UnzipRequest $request
     * @return JsonResponse
     */
    public function unzip(UnzipRequest $request): JsonResponse
    {
        $response = $this->service->unzip(
            $this->service->absolutePath($request->post('path')),
            $this->service->absolutePath($request->post('target'))
        );

        if ($response['success']) {
            $response['items'] = $this->service->getBasePathItems($request->post('open', []));
        }

        return response()->json($response);
    }


    /**
     * @param FileUploadRequest $request
     * @return JsonResponse
     */
    public function upload(FileUploadRequest $request): JsonResponse
    {
        [$success, $message] = $this->service->upload($request->post('target'), $request->file('file'));

        return response()->json([
            'success' => $success,
            'message' => $message,
            'items' => $success ? $this->service->getBasePathItems($request->post('open', [])) : []
        ]);
    }

    // @codeCoverageIgnoreEnd
}
