<?php

namespace App\Controllers;

class Navigate extends BaseController
{
    public function index(): string
    {
        $googlekey =  getenv('GOOGLE_MAPS_API_KEY');
        return view('navigate',[
            'googlekey'         => $googlekey
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
