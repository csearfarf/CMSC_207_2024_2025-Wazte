<?php

namespace App\Controllers;

use Config\Services;

class Users extends BaseController
{
    protected $userModel;
    protected $loggedUser;
    protected $oauthId;
    protected $currentName;
    protected $role;
    protected $rolename;

    public function __construct()
    {
        // Instantiate the UserModel for user operations.
        $this->userModel = new \App\Models\UserModel();
    }

    /**
     * Get all users.
     *
     * Retrieves all user data from the database, adds dropdown
     * actions for editing and deleting each user, and returns the data as JSON.
     */
    public function getUsers()
    {
        // Retrieve all user data.
        $users = $this->userModel->findAll();

        // Loop through each user and add dropdown actions and role name.
        foreach ($users as &$user) {
            // Generate the dropdown HTML.
            $dropdown = '<div class="dropdown">' .
                            '<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown">' .
                                '<i class="fas fa-ellipsis-v"></i>' .
                            '</a>' .
                            '<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">' .
                                '<a class="dropdown-item" href="#" onclick="editUser(' . $user['user_ID'] . ')">Edit</a>' .
                                '<a class="dropdown-item" href="#" onclick="deleteUser(' . $user['user_ID'] . ')">Delete</a>' .
                            '</div>' .
                        '</div>';

            // Assign the dropdown and role name.
            $user['actions'] = $dropdown;
            $user['rolename'] = getRolename($user['roleID']);
        }

        // Return the data as a JSON response.
        return $this->response->setJSON($users);
    }

    /**
     * Create a new user.
     *
     * Validates the input data, checks if the email is already registered,
     * and inserts new user data into the database.
     */
    public function createNewUser()
    {
        // Get the current date and time.
        $currentDateTime = date("Y-m-d H:i:s");

        // Attempt to retrieve POST data (for application/x-www-form-urlencoded).
        $data = $this->request->getPost();

        // If no form data is found, try to decode JSON input.
        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true);
        }

        // Define the validation rules.
        $rules = [
            'name'    => 'required|min_length[3]',
            'email'   => 'required|valid_email',
            'role'    => 'required|in_list[1,2,3,4]',
            'picture' => 'permit_empty'
        ];

        // Validate the incoming data.
        if (!$this->validate($rules)) {
            // Combine error messages into a single string.
            $errors = $this->validator->getErrors();
            $errorMessage = implode(" ", $errors);

            // Return a JSON response with status 400 if validation fails.
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => $errorMessage
                        ]);
        }

        // Check if the email is already registered.
        if ($this->userModel->isAlreadyRegisterByEmail($data['email'])) {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => $data['email'] . ' already registered in our system.'
                        ]);
        }

        // Prepare the user data for insertion.
        $userdata = [
            'Name'        => $data['name'],
            'Email'       => $data['email'],
            'roleID'      => (int)$data['role'],
            'profile_img' => isset($data['picture']) ? $data['picture'] : '',
            'created_at'  => $currentDateTime
        ];

        // Insert the new user data.
        $this->userModel->insertNewUserData($userdata);

        // Return a success JSON response.
        return $this->response->setStatusCode(200)
                    ->setJSON([
                        'status'  => 'success',
                        'message' => 'Successfully created'
                    ]);
    }

    /**
     * Delete a user.
     *
     * Deletes a user based on the ID provided in the URL.
     * Returns a JSON response indicating success or error.
     *
     * @param int $id User ID passed via the URL.
     */
    public function deleteUser($id = null)
    {
        // Validate the user ID.
        if (empty($id) || !is_numeric($id)) {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => 'Invalid user ID.'
                        ]);
        }

        // Attempt to delete the user.
        if ($this->userModel->deleteUserByID((int)$id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } else {
            return $this->response
                        ->setStatusCode(500)
                        ->setJSON([
                            'status' => 'error',
                            'message' => 'Failed to delete user'
                        ]);
        }
    }


    /**
     * View a user.
     *
     * View a user based on the ID provided in the URL.
     * Returns a JSON response indicating success or error.
     *
     * @param int|null $id User ID passed via the URL.
     */
    public function viewUser($id = null)
    {
        // Validate the user ID.
        if (empty($id) || !is_numeric($id)) {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => 'Invalid user ID.'
                        ]);
        }

        // Fetch user details using the model function.
        $user = $this->userModel->viewUserByID((int)$id);

        if ($user) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $user
            ]);
        } else {
            return $this->response
                        ->setStatusCode(404)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => 'User not found.'
                        ]);
        }
    }

    /**
     * Update a user.
     *
     * Processes POST data (or raw JSON) to update user details.
     * Validates input fields, calls the model to update the data, 
     * and returns a JSON response with HTTP status code 400 if any error occurs
     * or 200 if the update is successful.
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function updateUser() {
        // Retrieve POST data; if empty, attempt to decode JSON body.
        $data = $this->request->getPost();
        if (empty($data)) {
            $data = json_decode($this->request->getBody(), true);
        }
        
        // Validate required fields.
        if (
            empty($data['user_ID']) ||
            empty($data['Name']) ||
            empty($data['Email']) ||
            empty($data['roleID'])
        ) {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => 'Missing required fields. !!!!!'
                        ]);
        }

        // Validate email format.
        if (!filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => 'Invalid email address.'
                        ]);
        }

        // Prepare the user data for update.
        $userdata = [
            'Name'        => $data['Name'],
            'Email'       => $data['Email'],
            'profile_img' => isset($data['profile_img']) ? $data['profile_img'] : '',
            'roleID'      => $data['roleID'],
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        // Call the model function to update data by user ID.
        if ($this->userModel->updateUserDataByID((int)$data['user_ID'], $userdata)) {
            return $this->response
                        ->setStatusCode(200)
                        ->setJSON([
                            'status'  => 'success',
                            'message' => 'User updated successfully.'
                        ]);
        } else {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => 'Failed to update user.'
                        ]);
        }
    }



}
