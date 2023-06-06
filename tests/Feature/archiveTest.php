<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\ArchiveController;
use Tests\TestCase;

class archiveTest extends TestCase
{
  use RefreshDatabase;
  /**
  * A basic feature test example.
  *
  * @return void
  */
  public function testExample()
  {
    $ArchiveController= new ArchiveController;
    $test=$ArchiveController->test();
    $this->assertEmpty($test);
  }
}
