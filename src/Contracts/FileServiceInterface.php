<?php


namespace dyutin\FileManager\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;

interface FileServiceInterface
{

    /**
     * FileService constructor.
     * @param string $base_path
     */
    public function __construct(string $base_path);

    /**
     * @param string $pattern
     * @return $this
     */
    public function setPathPattern(string $pattern);

    /**
     * @return string
     */
    public function getPathPattern(): string;

    /**
     * @param array $paths
     * @return $this
     */
    public function setHidden(array $paths);


    /**
     * @return array
     */
    public function getHidden(): array;


    /**
     * @param $path
     * @return array
     */
    public function prepareFileItem($path): array;


    /**
     * @param string $path
     * @return Collection
     */
    public function getFiles(string $path): Collection;


    /**
     * @param Collection $files
     * @param array $hidden
     * @return Collection
     */
    public function filterHidden(Collection $files, array $hidden = []): Collection;


    /**
     * @param $path
     * @param array $open
     * @return array
     */
    public function getItems($path, array $open = []): array;


    /**
     * @param array $open
     * @return array
     */
    public function getBasePathItems(array $open = []): array;


    /**
     * @param string $path
     * @param UploadedFile $file
     * @return array
     */
    public function upload(string $path, UploadedFile $file): array;

    /**
     * @param $source
     * @param $destination
     * @return array|null
     */
    public function zip($source, $destination): ?array;

    /**
     * @param string $path
     * @param string $target
     * @return array
     */
    public function unzip(string $path, string $target): array;

    /**
     * @param $path
     * @return string
     */
    public function absolutePath($path): string;
}
