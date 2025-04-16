<?php

namespace App\Controllers;

class Error extends BaseController
{
    public function unauthorized()
    {   
      
        // Build the HTML output with your header, error view, and footer.
        $output = view('shared/index_header') 
        . view('errors/unauthorized') 
        . view('shared/index_footer');

        // Set the HTTP status code to 401 (Unauthorized) and output the combined content.
        return $this->response->setStatusCode(401)->setBody($output);
    }

}
