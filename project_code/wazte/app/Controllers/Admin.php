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
    protected $sciSessionsModel;

    public function __construct()
    {
        // Instantiate the UserModel.
        $this->userModel = new \App\Models\UserModel();
        // Instantiate the UserModel.
        $this->materialModel = new \App\Models\MaterialModel();

        $this->sciSessionsModel = new \App\Models\SciSessionsModel();
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
        $googlekey = getenv('GOOGLE_MAPS_API_KEY');

        // Load models for counting
        $facilityModel = new \App\Models\FacilityModel();
        $userModel = new \App\Models\UserModel();

        // Totals
        $totalFacilities = $facilityModel->countAll();
        $totalFacilitator = $userModel->where('roleID', 2)->countAllResults();
        $totalUsers = $userModel->where('roleID', 3)->countAllResults();
        $totalBlank = $userModel->where('roleID', 4)->countAllResults();
        $title = "Dashboard";
        // Check login
        if ($this->oauthId !== null) {
            $output = view('shared/dashboard_header', ['title' => $title])
                . view('shared/dashboard_sidenav', [
                    'role' => $this->role,
                    'current_name' => $this->currentName,
                    'rolename' => $this->rolename
                ])
                . view('admin/dashboard/index', [
                    'totalFacilities' => $totalFacilities,
                    'totalFacilitator' => $totalFacilitator,
                    'totalUsers' => $totalUsers,
                    'totalBlank' => $totalBlank
                ])
                . view('shared/dashboard_footer', [
                    'googlekey' => $googlekey
                ]);

            return $this->response
                ->setStatusCode(200)
                ->setBody($output);
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
        $title = "Manage Facilities";

        if ($this->oauthId !== null) {
            $output = view('shared/dashboard_header', ['title' => $title])
                . view('shared/dashboard_sidenav', [
                    'role' => $this->role,
                    'current_name' => $this->currentName,
                    'rolename' => $this->rolename
                ])
                . view('admin/facility/index', [
                    'materials' => $materials
                ])
                . view('shared/dashboard_footer', [
                    'googlekey' => $googlekey
                ]);

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
        $title = "Manage Users";
        if ($this->oauthId !== null) {
            $output = view('shared/dashboard_header', ['title' => $title])
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


    public function recentSessions()
    {
        $sessM = new \App\Models\SciSessionsModel();
        $allRows = $sessM->orderBy('timestamp', 'DESC')->findAll();

        $perUser = [];
        foreach ($allRows as $r) {
            $decoded = $this->decodeSessionData($r['data']);

            // who is this?
            $uid = $decoded['LoggedUserData']['user_ID'] ?? null;
            if (!$uid)
                continue;

            // grab the regenerate time instead of DB timestamp
            $ts = (int) ($decoded['__ci_last_regenerate'] ?? $r['timestamp']);

            // only keep the first (i.e. newest) entry per user
            if (!isset($perUser[$uid])) {
                $perUser[$uid] = [
                    'email' => $decoded['LoggedUserData']['email'] ?? null,
                    'previous_url' => $decoded['_ci_previous_url'] ?? null,
                    'timestamp' => $ts,
                ];
            }
        }

        // now build + sort + limit
        $list = [];
        foreach ($perUser as $info) {
            $list[] = [
                'email' => $info['email'],
                'previous_url' => $info['previous_url'],
                'last_login' => date('Y-m-d H:i:s', $info['timestamp']),
            ];
        }
        usort($list, fn($a, $b) => strcmp($b['last_login'], $a['last_login']));
        $top5 = array_slice($list, 0, 5);

        return $this->response
            ->setStatusCode(200)
            ->setJSON($top5);
    }


    /**
     * Helper: safely decode a PHP session‑style blob.
     */
    private function decodeSessionData(string $blob): array
    {
        // back up any live session
        $backup = $_SESSION ?? [];
        $_SESSION = [];

        // decode directly into $_SESSION
        session_decode($blob);
        $decoded = $_SESSION;

        // restore
        $_SESSION = $backup;
        return $decoded;
    }


}
