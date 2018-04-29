<?php

class TCPServer
{
    public $sock = NULL;

    public function __construct($addr = "127.0.0.1", $port = 8888)
    {
        $sock = $this->newSocket($addr, $port);
        $this->sock = $sock;
    }

    public function newSocket($addr, $port)
    {
        if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            throw new \Exception("socket_create() failed: ".socket_strerror(socket_last_error()));
        }

        if ((socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1)) === false) {
            throw new \Exception("socket_set_option() failed: ".socket_strerror(socket_last_error()));
        }

        if ((socket_bind($sock, $addr, $port)) === false) {
            throw new \Exception("socket_bind() failed: ".socket_strerror(socket_last_error($sock)));
        }

        if (socket_listen($sock) === false) {
            throw new \Exception("socket_listen() failed: ".socket_strerror(socket_last_error($sock)));
        }

        return $sock;
    }

    public function run()
    {
      while ($remote = socket_accept($this->sock)) {
        while ($line = socket_read($remote, 1024)) {
          $method = $this->check_method($line);
          $response = '';
          if ($method == 'GET') {
            $response = $this->get($line);
          } elseif($method == 'HEAD') {
            $response = $this->head($line);
          }
          socket_write($remote, $response);
          socket_close($remote);
          break;
        }
      }
    }

    function check_method($line)
    {
      preg_match('/^.+\sHTTP\//',$line,$matches,PREG_OFFSET_CAPTURE);
      if (preg_match('/GET/',$matches[0][0]) == 1) {
        return 'GET';
      } else {
        return 'HEAD';
      }
    }

    function get($line)
    {
      $path = $this->get_request_path($line);
      $body = file_get_contents($path);
      $header = $this->create_header($path,$body);
      $response = $header . $body . "\r\n";
      return $response;
    }

    function head($line)
    {
      $path = $this->get_request_path($line);
      $body = file_get_contents($path);
      $header = $this->create_header($path, $body);
      return $header;
    }

    function get_request_path($line)
    {
      $root_dir = './contents';
      preg_match('/^.+\sHTTP\/.*/',$line,$matches,PREG_OFFSET_CAPTURE);
      $head_line = $matches[0][0];
      $paths = preg_split('/\s/',$head_line);
      $path = $root_dir.$paths[1];
      return $path;
    }

    function create_header($path, $body)
    {
      $content_type = $this->check_content_type($path);
      $status_code = $this->create_status_code();
      $header =
      "HTTP/1.0 ".$status_code."\r\n".
      "Content-Type: ".$content_type."; charset=UTF-8\r\n".
      "Content-Length: ".strlen($body)."\r\n".
      "Connection: Close\r\n\r\n";
      return $header;
    }

    function create_status_code ()
    {
      return '200 OK';
    }

    function check_content_type ($path)
    {
      $extension = end(explode(".",$path,3));
      if ($extension == 'html') {
        return 'text/html';
      } elseif ($extension == 'css') {
        return 'text/css';
      } elseif ($extension == 'js') {
        return 'text/javascript';
      } elseif (preg_match('/gif|jpeg|jpg|png|bmp/',$extension) == 1) {
        return 'image/'.$extension;
      }
      return 'text/html';
    }
}
