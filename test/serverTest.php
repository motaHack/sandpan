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

  public function test_check_method()
  {
    $server = new TCPServer();
    $this->assertSame('GET', $server->check_method('GET / HTTP/1.1'));
    $this->assertSame('HEAD', $server->check_method('HEAD / HTTP/1.1'));
  }

  public function test_create_content_length()
  {
    $server = new TCPServer();
    $body = file_get_contents('contents/index.html');
    $this->assertSame(164, $server->create_content_length($body));
  }

  public function test_get()
  {
    $server = new TCPServer();
    $line = "GET /index.html HTTP/1.1\r\n".
            "Host: 127.0.0.1:8888\r\n".
            "User-Agent: curl/7.54.0\r\n".
            "Accept: */*  \r\n";
    $this->assertContains('hello world', $server->get($line));
  }
  public function test_head()
  {
    $server = new TCPServer();
    $line = "HEAD /index.html HTTP/1.1\r\n".
            "Host: 127.0.0.1:8888\r\n".
            "User-Agent: curl/7.54.0\r\n".
            "Accept: */*  \r\n";
    $this->assertNotContains('hello world', $server->head($line));
  }

  // public function test_head()
  // {
  //   $server = new TCPServer();
  // }
}
