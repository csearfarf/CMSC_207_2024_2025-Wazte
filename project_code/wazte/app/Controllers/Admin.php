<?php

namespace App\Controllers;

use Config\Services;

class Admin extends BaseController
{
    protected $userModel;
    protected $materialModel;
    protected $loggedUser;
    protected $oauthId;
    protected $currentName;
    protected $role;
    protected $rolename;

    public function __construct()
    {
        // Instantiate the UserModel.
        $this->userModel = new \App\Models\UserModel();
        // Instantiate the UserModel.
        $this->materialModel = new \App\Models\MaterialModel();
    }

    /**
     * Load and set user details from the session.
     *
     * This method retrieves the logged user data from the session, sets the OAuth ID and 
     * current user name, obtains the user's role from the model, and gets the corresponding 
     * role name using the global helper function getRolename().
     */
    private function loadUserDetails()
    {
        $session = Services::session();
        $this->loggedUser = $session->get("LoggedUserData");
        $this->oauthId = isset($this->loggedUser['oauth_id']) ? $this->loggedUser['oauth_id'] : null;
        $this->currentName = isset($this->loggedUser['name']) ? $this->loggedUser['name'] : null;

        // Only attempt to get role if OAuth ID is available.
        if ($this->oauthId !== null) {
            $this->role = $this->userModel->getUserRole($this->oauthId);
            // Use the global helper getRolename() to convert the role to a human-readable string.
            $this->rolename = getRolename($this->role);
        }
    }

    /**
     * Display the Admin dashboard.
     *
     * Loads common header, sidenav, main content, and footer views.
     * Passes along the role, current name, and role name to the sidenav view.
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function index()
    {
        $this->loadUserDetails();

        // Check if the user is logged in.
        if ($this->oauthId !== null) {
            $output = view('shared/dashboard_header')
                . view('shared/dashboard_sidenav', [
                    'role' => $this->role,
                    'current_name' => $this->currentName,
                    'rolename' => $this->rolename
                ])
                . view('admin/dashboard/index')
                . view('shared/dashboard_footer');

            return $this->response->setStatusCode(200)->setBody($output);
        } else {
            return redirect()->to(base_url("login/logout"));
        }
    }

    /**
     * Display the Facility page.
     *
     * Loads common header, sidenav, facility content, and footer views.
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function facility()
    {

        $googlekey = getenv('GOOGLE_MAPS_API_KEY');

        $this->loadUserDetails();


        //fetch materials in MaterialModel
        $materials = $this->materialModel->getMaterials();

        if ($this->oauthId !== null) {
            $output = view('shared/dashboard_header', [
                'googlekey' => $googlekey
            ])
                . view('shared/dashboard_sidenav', [
                    'role' => $this->role,
                    'current_name' => $this->currentName,
                    'rolename' => $this->rolename
                ])
                . view('admin/facility/index', [
                    'materials' => $materials
                ])
                . view('shared/dashboard_footer');

            return $this->response->setStatusCode(200)->setBody($output);
        } else {
            return redirect()->to(base_url("login/logout"));
        }
    }

    /**
     * Display the Users page.
     *
     * Loads common header, sidenav, users content, and footer views.
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function users()
    {
        $this->loadUserDetails();

        if ($this->oauthId !== null) {
            $output = view('shared/dashboard_header')
                . view('shared/dashboard_sidenav', [
                    'role' => $this->role,
                    'current_name' => $this->currentName,
                    'rolename' => $this->rolename
                ])
                . view('admin/users/index')
                . view('shared/dashboard_footer');

            return $this->response->setStatusCode(200)->setBody($output);
        } else {
            return redirect()->to(base_url("login/logout"));
        }
    }
}
