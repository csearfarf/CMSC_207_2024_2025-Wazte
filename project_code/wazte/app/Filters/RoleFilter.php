<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use Config\Services;
use CodeIgniter\Filters\FilterInterface;
use App\Models\UserModel;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = Services::session();
        $loggedUser = $session->get("LoggedUserData");
        

        if (!$loggedUser) {
            return redirect()->to('/login');
        }

        $oauthId = $loggedUser['oauth_id'] ?? null;

   

        if (!$oauthId) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $role = $userModel->getUserRole($oauthId);

        // Convert to int for reliable comparison
        $role = (int) $role;

        // If no role found or doesn't match allowed roles
        if (!$role || ($arguments && !in_array($role, array_map('intval', $arguments)))) {
            return redirect()->to('/unauthorized');
        }

        // Allow access
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after
    }
}
