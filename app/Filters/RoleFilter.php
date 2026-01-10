<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */

    /**
     * Before filter - check user role
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // If no arguments passed, allow all authenticated users
        if (empty($arguments)) {
            return $request;
        }

        // Get user role from session
        $userRole = session()->get('role');

        // Check if user role is in allowed roles
        if (!in_array($userRole, $arguments)) {
            // Redirect to access denied page or dashboard
            return redirect()->to('/access-denied')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
        return $request;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */

    /**
     * After filter
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do Something here if needed
        return $response;
    }
}
