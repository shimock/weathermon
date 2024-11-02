<?php

// Start a session to store user login data
session_start();

// Include dependancies
include_once 'applets/Database.php';

// Check if the user has created a login request
if (filter_input(INPUT_POST, 'login') == 'login') {
    
    // Login the user
    $this->authentication();
}

// Check if a registration request has been made
if (filter_input(INPUT_POST, 'register') == 'register') {
    
    // Register the user
    $this->registration();
}

// This module authenticates user login details and logins the user if the 
// credentials are valid
function authentication() {

    // Intiate a database connection
    $db = new Database;
    $conn = $db->databaseConnection();
    
    // Prepare the submitted username and password from the login form
    $username = filter_input(INPUT_POST, 'username');
    $password = filter_input(INPUT_POST, 'password');
    
    // Query the datbase for the existence of the provided username
    $sql = "SELECT * FROM USER WHERE E_MAIL = '" . $username . "';";
    $result = $conn->query($sql);
    
    // Check if the query for the username returned any results
    if ($result->num_rows > 0) {
        
        // If records exist store the result in an array
        $row = $result->fetch_assoc();
                
        // Check the password atribute of the returned resultset and verify if
        // matches the provided password
        if(password_verify($password, $row['PASSWORD'])){
            
            // If the password is valid, save the user credentials in a session
            $_SESSION['id'] = $row['ID'];
            $_SESSION['full_name'] = $row['FULL_NAME'];
            $_SESSION['email'] = $row['E_MAIL'];
            
            // Redirect to the homepage when the credentials are valid
            header('location: ./');
        }else{
            // Show a warning to the user on failure and request the user to
            // retry the login. This action can be handled better with the use
            // of a more concise module
            echo "<div class='container p-5 mt-5 bg-warning bg-opacity-25 rounded-2 shadow-sm text-center'>"
                . "<span class='bi-exclamation-triangle px-2'></span>"
                . "<p class='h5 p-3 font-size-lg'>User could not be found</p>"
                . "<p>Please try to <a href='./?content=login'>login again</a> or <a href='./?content=register'>register</a> if you do not have an account.</p>"
            . "</div>";
        }
    } else {
        
        // When user credentials are invalid or could not be found in the
        // database, show a warning to the user. It is important to show a 
        // general non-specific message to avoid providing sensitive information
        // to the user
        echo "<div class='container p-5 mt-5 bg-warning bg-opacity-25 rounded-2 shadow-sm text-center'>"
            . "<span class='bi-exclamation-triangle px-2'></span>"
            . "<p class='h5 p-3 font-size-lg'>User could not be found</p>"
            . "<p>Please try to <a href='./?content=login'>login again</a> or <a href='./?content=register'>register</a> if you do not have an account.</p>"
        . "</div>";
    }
}

// This module handles user registration
function registration() {
    
    // Create a connection to the database
    $db = new Database;
    $conn = $db->databaseConnection();
    
    // Store submitted data from the form in variables
    $fullName = filter_input(INPUT_POST, 'full_name');
    $contactNumber = filter_input(INPUT_POST, 'contact_number');
    $email = filter_input(INPUT_POST, 'email');
    $rawPassword = filter_input(INPUT_POST, 'password');
    
    // Hash the password to avoid it being saved as plain text. This adds a 
    // security later to the confidentiality of user data.    
    $password = $password = password_hash($rawPassword, PASSWORD_DEFAULT);
    
    // Create a database query to store the registion information into the 
    // database
    $sql = "INSERT INTO USER (`FULL_NAME`,`CONTACT_NUMBER`,`E_MAIL`,`PASSWORD`,`CREATED`) "
        . "VALUES ('".$fullName."','".$contactNumber."','".$email."','".$password."', CURDATE());";
    
    // Check the query result
    if($conn->query($sql) == TRUE){
        
        // If the query was successful, inform the user
        echo "<div class='container p-5 mt-5 bg-success bg-opacity-25 rounded-2 shadow-sm text-center'>"
            . "<span class='bi-exclamation-triangle fs-1 px-2 text-success'></span>"
            . "<p class='h5 p-3 font-size-lg'>Registration completed successfully!</p>"
            . "<p><a href='./?content=login' class='btn btn-primary'>Login</a></p>"
        . "</div>";
    }else{
        
        // If the query failed to insert the information, show the user a 
        // warning
        echo "<div class='container p-5 mt-5 bg-warning bg-opacity-25 rounded-2 shadow-sm text-center'>"
            . "<span class='bi-exclamation-triangle fs-1 px-2'></span>"
            . "<p class='h5 p-3 font-size-lg'>Registration could not be completed.</p>"
            . "<p>Please try to  <a href='./?content=register'>register</a> once more or contact the Systems Administrator.</p>"
        . "</div>";
    }
}
