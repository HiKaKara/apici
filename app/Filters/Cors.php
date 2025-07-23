<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Cors implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Izinkan request dari semua sumber. Untuk production
        header('Access-Control-Allow-Origin: http://10.144.98.161:8080/api/');
        
        // Izinkan metode HTTP yang spesifik
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        
        // Izinkan header kustom yang spesifik
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        // Tangani preflight request (OPTIONS)
        if ($request->getMethod() === 'options') {
            // Hentikan eksekusi lebih lanjut dan kirim response OK
            return response()->setStatusCode(200);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}