<?php
use PHPUnit\Framework\TestCase;
require_once '../hello.php';

class helloTest extends TestCase
{
  public function testHelloWorld()
  {
    $helloClass = new hello();
    $this->assertSame('hello,world', $helloClass->helloworld());
  }
}
