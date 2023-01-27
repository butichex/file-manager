<?php

namespace dyutin\FileManager\Tests;


use Throwable;

class OperationTest extends TestCase
{


    /**
     * @throws Throwable
     */
    public function test_create_file(): void
    {
        $this->withoutMiddleware();

        $parent = $this->config['paths']['base'];

        $name = 'test_created_file.txt';


        $response = $this->post('/file-manager/files/create/file', compact('parent', 'name'))
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertTrue($response['success']);

        if ($response['success']) {
            $this->deleteTest($response['path']);
        }

    }

    /**
     * @throws Throwable
     */
    public function test_create_folder(): void
    {
        $this->withoutMiddleware();

        $parent = $this->config['paths']['base'];

        $name = 'test_created_folder';

        $response = $this->post('/file-manager/files/create/folder', compact('parent', 'name'))
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertTrue($response['success']);

        if ($response['success']) {
            $this->deleteTest($response['path']);
        }

    }


    /**
     * @throws Throwable
     */
    public function test_copy(): void
    {
        $this->withoutMiddleware();

        $from = $this->config['paths']['base'] . DIRECTORY_SEPARATOR . 'test-file.txt';
        $to = $this->config['paths']['base'];
        $name = 'test-file-copy.txt';

        $response = $this->post('/file-manager/files/copy', compact('from', 'to', 'name'))
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertTrue($response['success'] ?? false);

        if ($response['success']) {
            $this->deleteTest($response['target']);
        }
    }

    /**
     * @throws Throwable
     */
    public function test_rename(): void
    {
        $this->withoutMiddleware();

        $path = $this->config['paths']['base'] . '/test-file.txt';
        $name = $path;


        $response = $this->post('/file-manager/files/rename',compact('path','name'))
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertTrue($response['success']);

    }


    /**
     * @param $path
     * @throws Throwable
     */
    public function deleteTest($path): void
    {
        $response = $this->post('/file-manager/files/delete', compact('path'))
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertTrue($response['success']);
    }


}
