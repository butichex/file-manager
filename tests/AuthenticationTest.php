<?php

namespace dyutin\FileManager\Tests;


class AuthenticationTest extends TestCase
{

  public function test_auth()
  {
     $response = $this->post('/file-manager/files',[],[
       'Accept' => 'application/json',
     ]);

     $response->assertUnauthorized();
  }

  public function test_base_path_authorize()
  {
     $this->withoutMiddleware();

     $response = $this->post('/file-manager/files/create/file',[
       'path' => $this->config['paths']['base'],
       'name' => $this->config['paths']['base'].'/../hack.sh'
     ]);

     $response->assertStatus(403);
  }


}
