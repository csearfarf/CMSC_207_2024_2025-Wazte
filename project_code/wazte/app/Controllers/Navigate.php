<?php

namespace App\Controllers;
use Config\Services;

class Navigate extends BaseController
{
    private $userModel = NULL;
    protected $loggedUser;

    function __construct()
    {

        $this->userModel = new \App\Models\UserModel();

    }
    public function index(): string
    {

        $session = Services::session();
        $this->loggedUser = $session->get("LoggedUserData");
        $oauthId = isset($this->loggedUser['oauth_id']) ? $this->loggedUser['oauth_id'] : null;
        $googlekey = getenv('GOOGLE_MAPS_API_KEY');
        $role = $this->userModel->getUserRole($oauthId);
        return view('navigate', [
            'googlekey' => $googlekey,
            'loggedUser' => $this->loggedUser,
            'role' => $role
        ]);
    }

    public function properties()
    {
        return $this->response->setJSON([
            [
                'position' => ['lat' => 14.584, 'lng' => 121.061],
                'price' => '$12,000/mo',
                'title' => 'Modern House',
                'address' => '939 New Brunswick Rd Apt. 282',
                'beds' => 4,
                'baths' => 2,
                'size' => 140,
                'image' => 'https://via.placeholder.com/320x180'
            ],
            // Add more properties as needed
        ]);
    }

    public function materialTypes()
    {
        $model = new \App\Models\MaterialModel();
        $materials = $model->getMaterials();
        return $this->response->setJSON($materials);
    }



}
