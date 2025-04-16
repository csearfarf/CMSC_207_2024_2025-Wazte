<?php

namespace App\Controllers;

use Google\Client;
use Google\Service\Oauth2;
use Config\Services;

class Login extends BaseController
{

    private $userModel=NULL;
	private $googleClient=NULL;
	function __construct(){

		$this->userModel = new \App\Models\UserModel();
        $this->googleClient = new \Google_Client();

        // Retrieve Google API credentials from the environment.
        // Make sure these variables are defined in your .env file.
        $clientId     = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirectUri  = env('GOOGLE_DEFAULT_REDIRECT_URI');

        // Set up the Google Client with the environment variables.
        $this->googleClient->setClientId($clientId);
        $this->googleClient->setClientSecret($clientSecret);
        $this->googleClient->setRedirectUri($redirectUri);

		$this->googleClient->addScope("email");
		$this->googleClient->addScope("profile");

	}
	/**
     * Main entry point for login.
     *
     * If the user is already logged in (as determined by session data), this method
     * checks the user role and redirects accordingly. Otherwise, it builds the login
     * page (with the Google authentication button).
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function index()
    {
        $session = Services::session();

        // Check if the user is already logged in.
        if ($session->get("LoggedUserData")) {
            $loggedUser = $session->get("LoggedUserData");
            $oauthId = isset($loggedUser['oauth_id']) ? $loggedUser['oauth_id'] : null;

            if ($oauthId !== null) {
                // Get the user's role and redirect accordingly.
                $role = $this->userModel->getUserRole($oauthId);
                return $this->redirectBasedOnRole($role);
            } else {
                // If logged user data is incomplete, force logout.
                return redirect()->to(base_url("login/logout"));
            }
        } else {
            // Prepare data for the login view, including the Google login button.
            $data['googleButton'] = '<a href="'.$this->googleClient->createAuthUrl().'" class="btn btn-md btn-docs btn-outline-white animate-up-2 mr-3"><i class="fas fa-fingerprint mr-2"></i>Login</a>';

            // Build the output HTML using concatenated views.
            $output = view('shared/index_header') 
                    . view('welcome_message', $data) 
                    . view('shared/index_footer');

            // Return the HTML output.
            return $output;
        }
    }

    /**
     * Display the choose user type screen.
     *
     * If the user is not logged in, forces a logout with an appropriate flash error.
     * Otherwise, builds the choose type view.
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function chooseUserType()
    {
        // Check if the user is logged in; if not, force re-login.
        if (!session()->get("LoggedUserData")) {
            session()->setFlashData("Error", "You have Logged Out, Please Login Again.");
            return redirect()->to(base_url());
        }

        // Build the output HTML with header, user type view and footer.
        $output = view('shared/index_header') 
                . view('login/usertype') 
                . view('shared/index_footer');

        return $output;
    }

    /**
     * Process Google login.
     *
     * Fetches the Google access token using the auth code, retrieves user information,
     * updates or inserts user data in the database, sets session data, and then redirects
     * based on the user role.
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function loginWithGoogle()
    {
        try {
            // Attempt to fetch the access token using the provided auth code.
            $token = $this->googleClient->fetchAccessTokenWithAuthCode($this->request->getVar('code'));
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // On exception, set an error message and redirect to the login page.
            session()->setFlashData("Error", $e->getMessage());
            return redirect()->to(base_url("login"));
        }

        // Check for errors in the token response.
        if (!isset($token['error'])) {
            // Set the access token and store it in the session.
            $this->googleClient->setAccessToken($token['access_token']);
            session()->set("AccessToken", $token['access_token']);

            // Get user information from Google.
            $googleService = new \Google\Service\Oauth2($this->googleClient);
            $data = $googleService->userinfo->get();
            $currentDateTime = date("Y-m-d H:i:s");
            $userdata = [];

            if ($this->userModel->isAlreadyRegister($data['id'])) {
                // If the user is already registered, update their data.
                $userdata = [
                    'oauth_id'    => $data['id'],
                    //'name'        => $data['givenName'] . " " . $data['familyName'],
                    'email'       => $data['email'],
                    'profile_img' => $data['picture'],
                    'updated_at'  => $currentDateTime
                ];
                $this->userModel->updateUserData($userdata, $data['id']);
            } else {
                // Otherwise, insert new user data.
                $userdata = [
                    'oauth_id'    => $data['id'],
                    'name'        => $data['givenName'] . " " . $data['familyName'],
                    'email'       => $data['email'],
                    'profile_img' => $data['picture'],
                    'created_at'  => $currentDateTime
                ];
                $this->userModel->insertUserData($userdata);
            }


            $existingUser = $this->userModel->viewUserByEmail($data['email']);

            if ($existingUser) {
                // Manually populate each piece of session data from the existing user array.
                $sessionData = [];
                $sessionData['user_ID']    = $existingUser['user_ID'];
                $sessionData['oauth_id']   = $data['id'];
                $sessionData['name']       = $existingUser['Name']; // Or $existingUser['Name'] if that is your key.
                $sessionData['email']      = $existingUser['Email'];
                $sessionData['profile_img']= $existingUser['profile_img'];
                $sessionData['roleID']     = $existingUser['roleID'];
                
                // Save the manually populated user data to the session.
                session()->set("LoggedUserData", $sessionData);

                // Get user role and redirect accordingly.
                $loggedUser = session()->get("LoggedUserData");
                $role = $this->userModel->getUserRole($loggedUser['oauth_id']);
                return $this->redirectBasedOnRole($role);
            }


            
        } else {
            // In case of token error, set flash message and redirect to welcome.
            session()->setFlashData("Error", "Something went wrong: " . $token['error_description']);
            return redirect()->to(base_url("welcome_message"));
        }
    }

    /**
     * Process logout.
     *
     * Removes session data for the logged user and access token.
     * If removal is successful, sets a success flash message and redirects to login.
     * If removal fails, sets an error message and redirects to the profile page.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function logout()
    {
        // Remove session variables.
        session()->remove('LoggedUserData');
        session()->remove('AccessToken');

        // Check if both variables have been removed.
        if (!(session()->get('LoggedUserData') || session()->get('AccessToken'))) {
            session()->setFlashData("Success", "Logout Successful");
            return redirect()->to(base_url("login"));
        } else {
            session()->setFlashData("Error", "Failed to Logout, Please Try Again");
            return redirect()->to(base_url("profile"));
        }
    }

    /**
     * Redirect user based on their role.
     *
     * Uses a switch statement to send the user to the appropriate URL.
     *
     * @param int $role The user role identifier.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    private function redirectBasedOnRole(int $role)
    {
        switch ($role) {
            case 1:
                return redirect()->to(base_url("admin"));
            case 2:
                return redirect()->to(base_url("facilitator"));
            case 3:
                return redirect()->to(base_url("user"));
            case 4:
                return redirect()->to(base_url("login/chooseusertype"));
            default:
                // For unexpected roles, force logout.
                return redirect()->to(base_url("login/logout"));
        }
    }

    /**
     * Choose type based on a parameter (1 or 0).
     *
     * This function accepts a parameter as a URL segment, validates it using the validation library,
     * and returns a JSON error response if invalid. If valid, it checks the logged user’s role.
     * For authorized users (role 4), it builds the redirection URL based on the parameter and returns a JSON
     * response (for AJAX/Axios calls) or performs a normal redirect.
     *
     * @param mixed $choice Expected to be '1' or '0'
     * @return \CodeIgniter\HTTP\ResponseInterface|\CodeIgniter\HTTP\RedirectResponse|string
     */
    public function chooseType($choice = null)
    {
        // Load required services.
        $session = Services::session();

        // Sanitize the choice parameter: cast to string and trim whitespace.
        $choice = trim((string)$choice);

        // Prepare the data array and set the validation rules.
        $data = ['choice' => $choice];
        $rules = ['choice' => 'required|in_list[1,0]'];

        // Use the validation service directly to validate our custom data.
        $validation = Services::validation();
        $validation->setRules($rules);

        if (!$validation->run($data)) {
            // If validation fails, get the error message.
            $errors = $validation->getErrors();
            $errorMessage = isset($errors['choice']) ? $errors['choice'] : 'Please select a valid user type.';

            // Since this request is expected to be AJAX (Axios), return a JSON response with status 400.
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => $errorMessage
                        ]);
        }

        // Get the logged user details.
        $loggedUser = $session->get("LoggedUserData");
        $oauthId = isset($loggedUser['oauth_id']) ? $loggedUser['oauth_id'] : null;

        if ($oauthId !== null) {
            $role = $this->userModel->getUserRole($oauthId);

            // Allow only users with role 4 to choose a type.
            if ($role == "4" || $role === 4) {
                // Determine redirection URL based on the validated choice.
                // as facilitator into role = 2 
                if ($choice === '1') {
                    
                    $currentDateTime = date("Y-m-d H:i:s");
                    $redirectUrl = base_url("facilitator"); // Modify URL as needed.
                    $userdata = [
                        'roleID'    => "2",
                        'updated_at'  => $currentDateTime
                    ];
                    $this->userModel->updateUserData($userdata,   $oauthId);


                } else { 
                    // $choice === '0'
                    // as user into role = 3 
                    $currentDateTime = date("Y-m-d H:i:s");
                    $redirectUrl = base_url("user"); // Modify URL as needed.
                    $userdata = [
                        'roleID'    => "3",
                        'updated_at'  => $currentDateTime
                    ];
                    $this->userModel->updateUserData($userdata,   $oauthId);
                }

                
                return $this->response->setStatusCode(200)
                ->setJSON([
                    'status'      => 'success',
                    'redirectUrl' => $redirectUrl
                ]);
            } else {
                // Build an unauthorized error page.
                $output = view('shared/index_header')
                        . view('errors/unauthorized') 
                        . view('shared/index_footer');

                return $this->response->setStatusCode(401)->setBody($output);
            }
        } else {
            // Build an unauthorized error page for missing user data.
            $output = view('shared/index_header')
                    . view('errors/unauthorized') 
                    . view('shared/index_footer');

            return $this->response->setStatusCode(401)->setBody($output);
        }
    }


    public function dump(){
            // Alternatively, use var_dump:
            var_dump(session()->get("LoggedUserData"));
            return;
    }



}
