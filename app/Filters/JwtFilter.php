<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JwtLib;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwt = new JwtLib();
        $auth = $jwt->validateRequest($request);

        if (! $auth['valid']) {
            return service('response')
                ->setJSON(['error' => $auth['error']])
                ->setStatusCode($auth['status']);
        }

        // attach decoded user to request (for controller access)
        $request->user = $auth['data'];
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing here
    }
}
