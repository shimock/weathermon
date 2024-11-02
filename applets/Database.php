<?php

class Database {
    
    //This module creates a database connection
    public function databaseConnection() {
        
        // Initialise variables for the database connection
        $server = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'weathermon';        
        
        // Request new connection
        $dbConnection = new mysqli($server, $username, $password, $database);
        
        if($dbConnection){
            
            // If the connection to the database was successful, return the 
            // connection object
            return $dbConnection;
        }else{
            
            // Provide feedback to the use on the failure to establish a 
            // database connection
            die ("Filure to establish a connection to the Database");
        }
    }
}
