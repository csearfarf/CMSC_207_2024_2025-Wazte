<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use App\Models\LocationModel;
use App\Models\FacilityModel;
use App\Models\FacilityTagsModel;

class Facility extends ResourceController
{
    protected $loggedUser;

    public function createNewFacility()
    {
        // 1) session & input
        $session = Services::session();
        $this->loggedUser = $session->get("LoggedUserData");
        $now = date("Y-m-d H:i:s");
        $post = $this->request->getPost()
            ?: json_decode(file_get_contents('php://input'), true);

        // 2) validation
        $rules = [
            'name' => 'required|min_length[3]',
            'lat' => 'required|decimal',
            'lng' => 'required|decimal',
            'address' => 'required|min_length[5]',
            'description' => 'required|min_length[3]',
            'contact' => 'required|regex_match[/^(?:09\d{9}|63\d{10})$/]',
            'businessHours' => 'required',
            'materials' => 'required'
        ];
        if (!$this->validate($rules)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors()
                ]);
        }

        // 3) find-or-create location
        $locM = new LocationModel();
        $existing = $locM
            ->where('lat', $post['lat'])
            ->where('lng', $post['lng'])
            ->first();

        if ($existing) {
            $locId = $existing['location_ID'];
        } else {
            $locId = $locM->insert([
                'lat' => $post['lat'],
                'lng' => $post['lng'],
                'address' => $post['address'],
            ]);
        }

        // 4) create facility
        $facM = new FacilityModel();
        $facId = $facM->insert([
            'locationID' => $locId,
            'dateadded' => $now,
            'AddedBy' => $this->loggedUser['user_ID'],
            'contactNo' => $post['contact'],
            'name' => $post['name'],
            'Description' => $post['description'],
            'BusinessHours' => $post['businessHours'],
        ]);

        // 5) attach tags
        if (!empty($post['materials']) && is_array($post['materials'])) {
            $ftM = new FacilityTagsModel();
            foreach ($post['materials'] as $tagId) {
                $ftM->insert([
                    'facility_ID' => $facId,
                    'tags_ID' => (int) $tagId,
                ]);
            }
        }

        // 6) response
        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status' => 'success',
                'message' => 'Facility created successfully'
            ]);
    }
}
