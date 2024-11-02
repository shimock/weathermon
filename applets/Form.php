<?php

class Form {
    
    // This module contructs the Form class. It is responsible for presenting 
    // any of the requested forms
    public function Form ($form) {
        
        // Based on the requested form retrieve the requested form
        switch($form) {
            case 'login':
                $form = $this->loginForm();
                break;
            case 'register':
                $form = $this->registerForm();
                break;
            case 'searchForm':
                $form = $this->searchForm();
                break;
            default:
                $form = 'The request returned an empty result';
        }
        
        return $form;
    }
    
    // This module returns an HTML Form to enable a use to login to the app
    public function loginForm () {
        
        // Prepare the Login Form
        $loginForm = "<div class='card mt-5 mx-auto w-25 shadow-lg'>"
            . "<div class='card-header text-center display-6 bg-primary bg-primary bg-opacity-25'>"
                . "Login"
            . "</div>"
            . "<div class='card-body'>"
                . "<form id='login_form' class='form' role='search' method='post' enctype='multipart/form-data' action='./?content=authentication'>"
                    . "<label class='form-label' for='Username'>E-Mail or Username:</label>"
                    . "<input class='form-control me-2' name='username' type='email' required>"
                    . "<label class='form-label' for='Username'>Password:</label>"
                    . "<input class='form-control me-2' name='password' type='password' required>"
                    
                . "</form>"
            . "</div>"
            . "<div class='card-footer d-flex justify-content-between'>"
                . "<a  class='btn btn-success' href='?content=register'>Register</a>"
                . "<button form='login_form' class='btn btn-primary mx-1' name='search' type='submit' value='login'>Login</button>"
                . "<a class='btn btn-danger' href='./'>Cancel</a>"
                
            . "</div>"
        . "</div>";
        return $loginForm;
    }
    
    // This module prepares an HTML Form for Registering a new user
    public function registerForm () {
        
        // Prepare the Registration Form
        $loginForm = "<div class='card mt-5 mx-auto w-25 shadow-lg'>"
            . "<div class='card-header text-center display-6 bg-success bg-gradient bg-opacity-25'>"
                . "Register"
            . "</div>"
            . "<div class='card-body'>"
                . "<form id='register_form' class='form' role='search' method='post' enctype='multipart/form-data' action='./?content=registration'>"
                    . "<label class='form-label' for='Full Name'>Full Name</label>"
                    . "<input class='form-control me-2' name='full_name' type='text' required>"
                    . "<label class='form-label' for='E-Mail'>E-Mail</label>"
                    . "<input class='form-control me-2' name='email' type='email' required>"
                    . "<label class='form-label' for='Contact Number'>Contact Number</label>"
                    . "<input class='form-control me-2' name='contact_number' type='search' required>"
                    . "<label class='form-label' for='Password'>Password</label>"
                    . "<input class='form-control me-2' name='password' type='password'>"
                    
                . "</form>"
            . "</div>"
            . "<div class='card-footer text-end'>"
                . "<button form='register_form' class='btn btn-primary bg-gradient mx-1' name='search' type='submit' value='register'>Register</button>"
                . "<a class='btn btn-danger' href='./'>Cancel</a>"
            . "</div>"
        . "</div>";
        
        return $loginForm;
    }
    
    // This module prepares a Search Form
    public function searchForm(){
        
        // Prepare a search form
        $searchForm = "<div class='container-fluid py-5 bg-light border-bottom'><div class='container'>"
                . "<form class='d-flex' role='search' method='post' enctype='multipart/form-data' action='./?content=". filter_input(INPUT_GET, 'content')."'>"
                . "<input class='form-control me-2' name='city' type='text' placeholder='City'>"
                . "<input class='form-control me-2' name='country_code' type='text' placeholder='Country Code'>"
                . "<input class='form-control' name='api_key' type='hidden' value='b47068dfa43a4a24a59bcea227202db9'>"
                . "<button class='btn btn-primary px-4' name='search' type='submit' value='search'><span class='bi-search'></span></button>"
            . "</form></div></div>";
        
        return $searchForm;
    }
}
