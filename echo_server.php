<?php

require_once 'server.php';

$server = new TestTCPServer();
$server->run(function($remote, $sock) {
  $body = file_get_contents('html/index.html');
  $header = "HTTP/1.1 200 OK\r\n".
    "Content-Type: text/html; charset=UTF-8\r\n".
    "Content-Length: ".strlen($body)."\r\n".
    "Connection: Close\r\n";
  $msg = $header . "\r\n" . $body . "\r\n";
    socket_write($remote, $msg);
    socket_close($remote);
});
