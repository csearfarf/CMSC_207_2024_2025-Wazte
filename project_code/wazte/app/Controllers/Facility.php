<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use App\Models\LocationModel;
use App\Models\FacilityModel;
use App\Models\FacilityTagsModel;
use App\Models\TagModel;
use App\Models\UserModel;

class Facility extends ResourceController
{
    protected $loggedUser;
    protected $role;

    public function __construct()
    {
        $session = Services::session();
        $this->loggedUser = $session->get('LoggedUserData') ?? [];
        $this->role = (int) ($this->loggedUser['roleID'] ?? 0);
    }

    public function createNewFacility()
    {
        $post = $this->request->getPost() ?: json_decode(file_get_contents('php://input'), true);
        $now = date('Y-m-d H:i:s');
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
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // find‑or‑create location
        $locM = new LocationModel();
        $existing = $locM->where('lat', $post['lat'])
            ->where('lng', $post['lng'])
            ->first();
        $locId = $existing
            ? $existing['location_ID']
            : $locM->insert([
                'lat' => $post['lat'],
                'lng' => $post['lng'],
                'address' => $post['address']
            ]);

        // create facility
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

        // attach tags
        if (is_array($post['materials'])) {
            $ftM = new FacilityTagsModel();
            foreach ($post['materials'] as $t) {
                $ftM->insert([
                    'facility_ID' => $facId,
                    'tags_ID' => (int) $t
                ]);
            }
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Facility created successfully'
        ], 200);
    }

    public function saveEditFacility()
    {
        $post = $this->request->getPost() ?: json_decode(file_get_contents('php://input'), true);
        $rules = [
            'facility_ID' => 'required|integer',
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
            return $this->failValidationErrors($this->validator->getErrors());
        }
        if (!is_array($post['materials'])) {
            return $this->failValidationErrors(['materials' => 'Materials must be an array of IDs.']);
        }

        $facM = new FacilityModel();
        $fac = $facM->find($post['facility_ID']);
        if (!$fac) {
            return $this->failNotFound('Facility not found');
        }
        // facilitators can only edit their own
        if ($this->role === 2 && $fac['AddedBy'] !== $this->loggedUser['user_ID']) {
            return $this->failForbidden('You may only edit your own facilities.');
        }

        // 1) update the location record
        (new LocationModel())
            ->update($fac['locationID'], [
                'lat' => $post['lat'],
                'lng' => $post['lng'],
                'address' => $post['address']
            ]);

        // 2) prepare facility update data, preserving AddedBy
        $updateData = [
            'name' => $post['name'],
            'Description' => $post['description'],
            'contactNo' => $post['contact'],
            'BusinessHours' => $post['businessHours'],
            // preserve the original creator:
            'AddedBy' => $fac['AddedBy'],
        ];
        $facM->update($post['facility_ID'], $updateData);

        // 3) refresh pivot tags
        $ftM = new FacilityTagsModel();
        $ftM->where('facility_ID', $post['facility_ID'])->delete();
        foreach ($post['materials'] as $t) {
            $ftM->insert([
                'facility_ID' => $post['facility_ID'],
                'tags_ID' => (int) $t
            ]);
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Facility updated successfully'
        ], 200);
    }

    public function deleteFacility($id = null)
    {
        if (!is_numeric($id)) {
            return $this->failValidationErrors(['facility_ID' => 'Invalid facility ID.']);
        }
        $facM = new FacilityModel();
        $fac = $facM->find($id);
        if (!$fac) {
            return $this->failNotFound("Facility #{$id} not found");
        }
        if ($this->role === 2 && $fac['AddedBy'] !== $this->loggedUser['user_ID']) {
            return $this->failForbidden('You may only delete your own facilities.');
        }

        // delete pivot then facility
        (new FacilityTagsModel())
            ->where('facility_ID', $id)
            ->delete();
        $facM->delete($id);

        return $this->respondDeleted([
            'status' => 'success',
            'message' => 'Facility deleted successfully'
        ]);
    }

    public function listFacilities()
    {
        $facM = new FacilityModel();

        // 1) Grab the HTTP referrer header (page that made this request)
        $referrer = $this->request->getServer('HTTP_REFERER') ?? '';
        // 2) Extract just the path (e.g. "/facilitator" or "/other/page")
        $refPath = parse_url($referrer, PHP_URL_PATH) ?: '';

        // 3) If the referrer path ends with "/facilitator" or "/facilitator/index", return all
        if (preg_match('#/facilitator(?:/index)?$#', $refPath)) {
            $all = $facM->findAll();
        } else {
            // 4) Otherwise, restrict by role (role 2 only sees their own adds)
            if ($this->role === 2) {
                $all = $facM
                    ->where('AddedBy', $this->loggedUser['user_ID'])
                    ->findAll();
            } else {
                $all = $facM->findAll();
            }
        }

        // 5) Build out your response payload as before
        $locM = new LocationModel();
        $ftM = new FacilityTagsModel();
        $tagM = new TagModel();
        $out = [];

        foreach ($all as $fac) {
            $loc = $locM->find($fac['locationID']);
            $pts = $ftM->where('facility_ID', $fac['facility_ID'])->findAll();

            $tags = [];
            foreach ($pts as $t) {
                $tg = $tagM->find($t['tags_ID']);
                $tags[] = [
                    'facilityTags_ID' => $t['facilityTags_ID'],
                    'tags_ID' => $t['tags_ID'],
                    'Material' => $tg['Material'] ?? null,
                    'icon' => $tg['icon'] ?? null,
                ];
            }

            $out[] = [
                'facility_ID' => $fac['facility_ID'],
                'locationID' => $fac['locationID'],
                'dateAdded' => $fac['dateAdded'],
                'AddedBy' => $fac['AddedBy'],
                'contactNo' => $fac['contactNo'],
                'name' => $fac['name'],
                'Description' => $fac['Description'],
                'BusinessHours' => $fac['BusinessHours'],
                'location' => [
                    'location_ID' => $loc['location_ID'],
                    'lat' => $loc['lat'],
                    'lng' => $loc['lng'],
                    'address' => $loc['address'],
                ],
                'tags' => $tags,
            ];
        }

        return $this->respond($out, 200);
    }




    public function listFacilitiesForUsers()
    {
        $facM = new FacilityModel();
        $locM = new LocationModel();
        $ftM = new FacilityTagsModel();
        $tagM = new TagModel();
        $userM = new UserModel(); // Make sure this exists

        $all = $facM->findAll();
        $out = [];

        foreach ($all as $fac) {
            $loc = $locM->find($fac['locationID']);
            $pts = $ftM->where('facility_ID', $fac['facility_ID'])->findAll();
            $user = $userM->getWhere(['user_ID' => $fac['AddedBy']])->getRowArray();

            $tags = [];
            foreach ($pts as $t) {
                $tg = $tagM->find($t['tags_ID']);
                $tags[] = [
                    'facilityTags_ID' => $t['facilityTags_ID'],
                    'tags_ID' => $t['tags_ID'],
                    'Material' => $tg['Material'] ?? null,
                    'icon' => $tg['icon'] ?? null,
                ];
            }

            $out[] = [
                'facility_ID' => $fac['facility_ID'],
                'locationID' => $fac['locationID'],
                'dateAdded' => $fac['dateAdded'],
                'AddedBy' => $fac['AddedBy'],
                'facilitator_email' => $user['Email'] ?? null,
                'contactNo' => $fac['contactNo'],
                'name' => $fac['name'],
                'Description' => $fac['Description'],
                'BusinessHours' => $fac['BusinessHours'],
                'location' => [
                    'location_ID' => $loc['location_ID'],
                    'lat' => $loc['lat'],
                    'lng' => $loc['lng'],
                    'address' => $loc['address'],
                ],
                'tags' => $tags,
            ];
        }

        return $this->respond($out, 200);
    }




    public function selectFacility($id = null)
    {
        $facM = new FacilityModel();
        $fac = $facM->find($id);
        if (!$fac) {
            return $this->failNotFound("Facility #{$id} not found");
        }
        if ($this->role === 2 && $fac['AddedBy'] !== $this->loggedUser['user_ID']) {
            return $this->failForbidden('You may only view your own facilities.');
        }

        $loc = (new LocationModel())->find($fac['locationID']);
        $pts = (new FacilityTagsModel())
            ->where('facility_ID', $fac['facility_ID'])
            ->findAll();

        $tags = [];
        foreach ($pts as $t) {
            $tg = (new TagModel())->find($t['tags_ID']);
            $tags[] = [
                'facilityTags_ID' => $t['facilityTags_ID'],
                'tags_ID' => $t['tags_ID'],
                'Material' => $tg['Material'] ?? null,
                'icon' => $tg['icon'] ?? null,
            ];
        }

        $out = [
            'facility_ID' => $fac['facility_ID'],
            'locationID' => $fac['locationID'],
            'dateAdded' => $fac['dateAdded'],
            'AddedBy' => $fac['AddedBy'],
            'contactNo' => $fac['contactNo'],
            'name' => $fac['name'],
            'Description' => $fac['Description'],
            'BusinessHours' => $fac['BusinessHours'],
            'location' => [
                'location_ID' => $loc['location_ID'],
                'lat' => $loc['lat'],
                'lng' => $loc['lng'],
                'address' => $loc['address'],
            ],
            'tags' => $tags,
        ];

        return $this->respond($out, 200);
    }
}
