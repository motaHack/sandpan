<?php

class TestTCPServer
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

    public function run(Closure $code)
    {
        echo ">>start";
        while ($remote = socket_accept($this->sock)) {
            while ($line = socket_read($remote, 1024)) {
                $code($remote, $this->sock);
                break;
            }
            echo "<<end";
        }
    }
}
