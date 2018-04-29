<?php
use PHPUnit\Framework\TestCase;
require_once 'server.php';

class serverTest extends TestCase
{
  public function test_create_status_code()
  {
    $server = new TCPServer();
    $this->assertSame('200 OK', $server->create_status_code());
  }

  public function test_check_content_type()
  {
    $server = new TCPServer();
    $this->assertSame('image/png', $server->check_content_type('icon.png'));
    $this->assertSame('image/jpeg', $server->check_content_type('image/test/test.jpeg'));
    $this->assertSame('text/html', $server->check_content_type('a/b/c/e.html'));
    $this->assertSame('text/javascript', $server->check_content_type('test.js'));
    $this->assertSame('text/html', $server->check_content_type('hi.php'));
  }

  public function test_get ()
  {
    $server = new TCPServer();
    echo $server->check_content_type('contents/index.html');
    $this->assertContains('hello world', $server->get('contents/index.html'));
  }

}
