<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Keep-Alive Filter
 * 
 * Prevents ERR_CONNECTION_RESET by maintaining connection
 * Sets keep-alive headers to prevent early disconnection
 */
class KeepAliveFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Prevent PHP from terminating connection early
        ignore_user_abort(true);
        set_time_limit(300);
        
        // Set keep-alive headers before any output
        if (!headers_sent()) {
            header('Connection: keep-alive');
            header('Keep-Alive: timeout=300, max=100');
        }
        
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Ensure response has keep-alive headers
        $response->setHeader('Connection', 'keep-alive');
        $response->setHeader('Keep-Alive', 'timeout=300, max=100');
        
        // Prevent early connection close
        $response->setHeader('X-Accel-Buffering', 'no');
        
        return $response;
    }
}
