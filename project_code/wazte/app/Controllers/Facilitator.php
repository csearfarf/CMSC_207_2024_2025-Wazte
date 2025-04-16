<?php

namespace App\Controllers;

use Config\Services;

class Facilitator extends BaseController
{
    protected $userModel;
    protected $loggedUser;
    protected $oauthId;
    protected $currentName;
    protected $role;
    protected $rolename;  // Human readable role name

    public function __construct()
    {
        // Instantiate the UserModel.
        $this->userModel = new \App\Models\UserModel();
    }

    /**
     * Loads user details from the session.
     *
     * This method retrieves the logged user data from the session, extracts the OAuth ID and name,
     * then obtains the numeric role from the model and uses the global getRolename() helper to get 
     * the human-readable role name.
     *
     * @return void
     */
    private function loadUserDetails(): void
    {
        $session = Services::session();
        $this->loggedUser  = $session->get("LoggedUserData");
        $this->oauthId     = isset($this->loggedUser['oauth_id']) ? $this->loggedUser['oauth_id'] : null;
        $this->currentName = isset($this->loggedUser['name']) ? $this->loggedUser['name'] : null;

        if ($this->oauthId !== null) {
            $this->role = $this->userModel->getUserRole($this->oauthId);
            // Use the global helper function to convert numeric role to its name.
            $this->rolename = getRolename($this->role);
        }
    }

    /**
     * Facilitator Dashboard.
     *
     * Loads common header, sidenav (passing role, current name, and role name), 
     * dashboard content, and footer views.
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function index()
    {
        $this->loadUserDetails();

        if ($this->oauthId !== null) {
            $output = view('shared/dashboard_header')
                    . view('shared/dashboard_sidenav', [
                        'role'         => $this->role,
                        'current_name' => $this->currentName,
                        'rolename'     => $this->rolename
                    ])
                    . view('facilitator/dashboard/index')
                    . view('shared/dashboard_footer');

            return $this->response->setStatusCode(200)->setBody($output);
        } else {
            return redirect()->to(base_url("login/logout"));
        }
    }

    /**
     * Facilitator Facility Page.
     *
     * Loads common header, sidenav (passing role, current name, and role name), 
     * facility content, and footer views.
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function facility()
    {
        $this->loadUserDetails();

        if ($this->oauthId !== null) {
            $output = view('shared/dashboard_header')
                    . view('shared/dashboard_sidenav', [
                        'role'         => $this->role,
                        'current_name' => $this->currentName,
                        'rolename'     => $this->rolename
                    ])
                    . view('facilitator/facility/index')
                    . view('shared/dashboard_footer');

            return $this->response->setStatusCode(200)->setBody($output);
        } else {
            return redirect()->to(base_url("login/logout"));
        }
    }



    /**
     * Facilitator Inquiries Page.
     *
     * Loads common header, sidenav (passing role, current name, and role name), 
     * facility content, and footer views.
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function inquiries()
    {
        $this->loadUserDetails();

        if ($this->oauthId !== null) {
            $output = view('shared/dashboard_header')
                    . view('shared/dashboard_sidenav', [
                        'role'         => $this->role,
                        'current_name' => $this->currentName,
                        'rolename'     => $this->rolename
                    ])
                    . view('facilitator/inquiries/index')
                    . view('shared/dashboard_footer');

            return $this->response->setStatusCode(200)->setBody($output);
        } else {
            return redirect()->to(base_url("login/logout"));
        }
    }
}
