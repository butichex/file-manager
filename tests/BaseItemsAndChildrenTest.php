<?php

namespace dyutin\FileManager\Tests;


class BaseItemsAndChildrenTest extends TestCase
{


  public function test_base_items()
  {
     $this->withoutMiddleware();

     $response = $this->post('/file-manager/files',[
             'open' => [__DIR__.'/__test_path__/test-folder-1/']
          ])
          ->assertStatus(200)
          ->decodeResponseJson();

     $this->assertEquals($response[0]['path'],$this->config['paths']['base']);
  }

  public function test_children_items()
  {
     $this->withoutMiddleware();

     $path = $this->config['paths']['base'].'/test-folder-1';


     $response = $this->post('/file-manager/files/children',[
       'path' => $this->config['paths']['base'].'/test-folder-1'
     ])
     ->assertStatus(200)
     ->decodeResponseJson();

     $this->assertEquals(count($response),count(glob($path.'/*')));

     $this->assertEquals($response[0]['path'],glob($path.'/*')[0]);

  }




}
