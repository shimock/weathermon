<?php

// Include file dependencies
include_once 'Form.php';
include_once 'Queries.php';
include_once 'Weather.php';

// This class manages the content based on href parameters passed in the the URL
class ContentManager {

    // This module constructs the response of the ContentManager object
    public function ContentManager($content) {
        
        // Based on the parameters in the URL, content will retrieved and
        // handled for presentation to the user
        switch ($content) {
            case 'current_weather':
                $display = $this->weatherForecast('current_weather');
                break;
            case 'daily_weather':
                $display = $this->weatherForecast('daily_weather');
                break;
            case 'login':
                $form = new Form;
                $display = $form->Form('login');
                break;
            case 'logout':
                $display = $this->logout();
                break;
            case 'register':
                $form = new Form;
                $display = $form->Form('register');
                break;
            case 'profile':
                $display = $this->profile();
                break;
            case 'search_history':
                $display = $this->searchHistory('search_history');
                break;
            case 'authentication':
                $display = authentication();
                break;
            case 'registration':
                $display = registration();
                break;
            default:
                $display = $this->dashboard();
        }

        return $display;
    }
    
    // This module handles requests weather data that is searched by the user
    // e.g., when the user searches for current weather information for a
    // particular city
    private function weatherForecast($forecast) {
        
        // Include the dependencies for weather information retrieval
        include_once 'Weather.php';
        
        // Initiate an instance of the weather object
        $weather = new Weather;
        
        // Check if the user initialised a search request
        if (filter_input(INPUT_POST, 'search') == 'search') {
            
            // Store information about the search parameters
            $city = filter_input(INPUT_POST, 'city');
            $country_code = filter_input(INPUT_POST, 'country_code');
            $api_key = filter_input(INPUT_POST, 'api_key');
            
            // Prepare the URL parameters for the API request URL
            $api_params = "?city=" . $city . "&country_code=" . $country_code . "&key=" . $api_key;

            return $weather->Weather($api_params);
            
        } else {
            
            // If the user has not initiated a search request provide some help
            // information about getting started on the category of weather 
            // information the user has selected to search for.
            return "<div class='container p-2 mt-5 text-center border border-info bg-info bg-opacity-25 rounded'>"
                    . "<span class='bi-info-circle pe-2 '></span>"
                    . "To get started with " . ucfirst(str_replace("_", " ", $forecast)) . " type the name of the City and the Country Code in the form above."
                . "</div>";
        }
    }
    
    //
    public function searchHistory() {

        $categoryPagination = "<nav class='p-5' aria-label='Pagination'>"
                . "<ul class='pagination justify-content-center'>"
                . "<li class='page-item'><a class='page-link' href='./?content=search_history&category=current'>Current Weather Forecast</a></li>"
                . "<li class='page-item'><a class='page-link' href='./?content=search_history&category=daily'>16-Day Weather Forecast</a></li>"
                . "</ul>"
            . "</nav>";
        
        if(isset($_SESSION['id'])){
            if(filter_input(INPUT_GET, 'category')){

                // Initiate database connection object and a weather data 
                // presentation object
                $db = new Database;
                $weather = new Weather;
                $conn = $db->databaseConnection();

                // Request data from the database. Data retrieval is based on the 
                // category and logged-in user
                $sql = "SELECT * FROM SEARCH_HISTORY WHERE USER = " . $_SESSION['id'] . " AND CATEGORY = '". filter_input(INPUT_GET, 'category')."' ORDER BY ID DESC LIMIT 5;";
                $result = $conn->query($sql);

                // Create a tab pane list to navigate through the various cities 
                // whose search results were stored to the database.
                $historyPagination[] = "<ul class='nav nav-tabs justify-content-center'>";
                $history[] = "";
                $count = 0;
                
                
                // Check if the query returned any results
                if($result->num_rows > 0){
                    // Check the database for all results meeting the search criteria
                    while ($row = $result->fetch_assoc()) {

                        // For the first record set the tab pane to active so that it 
                        // may be shown upon retrieval
                        if ($count == 0) {
                            $active = 'active';
                        } else {
                            $active = '';
                        }

                        // Set the tab or page names for the resultset
                        $historyPagination[] = "<li class='nav-item'><a class='nav-link " . $active . "' data-bs-toggle='tab' href='#" . $row['ID'] . "'>" . $row['CITY'] . ", " . $row['COUNTRY_CODE'] . "</a></li>";

                        // Customise the resultset based on the category selected.
                        if (filter_input(INPUT_GET, 'category') == 'daily') {

                            // For daily weather forecast
                            $weather->getDailyWeatherData(json_decode($row['DATA'], true));
                            $history[] = $weather->previewDailyWeatherForecast(index: $row['ID'], active: $active);
                        } else {

                            // For current weather forecast
                            $history[] = $weather->previewCurrentWeatherForecast(json_decode($row['DATA'], true), $row['ID'], $active);
                        }

                        // Create a switch to set page links to invisible except the
                        // first page
                        $count++;
                    }
                }else{
                    
                    // If there are no results for this query inform the user
                    $history[] = "<div class='container text-center border border-info bg-info bg-opacity-25 p-2 mt-3 rounded'> "
                            . "<span class='bi-info-circle pe-2 '></span>"
                            . "You do not have any saved ".ucfirst(filter_input(INPUT_GET, 'category'))." weather forecast results"
                        . "</div>";
                }

                $historyPagination[] = "</ul>";

            // Combine all components of the resultset and prepare it for 
            // presentation to the user
            return $categoryPagination
                . join($historyPagination) 
                . "<div class='tab-content'>"
                . join($history) 
                . "</div>";
            }else{

                // If the user has not selected a weather category to preview to 
                // promopt for a selection of a category
                echo $categoryPagination
                    . "<div class='text-center bg-info border border-info bg-opacity-25 p-2 rounded-2'>"
                        . "<span class='bi-info-circle pe-2 '></span>"
                        . "<span class='opacity-100'>Select a weather category to preview previously saved search results if available</span>"
                    . "</div>";
            }
        }else{
            echo "<div class='container p-2 mt-5 bg-warning bg-opacity-25 rounded-2 shadow-sm text-center'>"
            . "<span class='bi-exclamation-triangle fs-1 px-2'></span>"
            . "<p class='h5 p-3 font-size-lg'>Please Login in or Register</p>"
            . "<p>It looks like you are not logged-in yet. Please <a href='./?content=login'>login</a> or <a href='./?content=register'>register</a> to view Search History.</p>"
        . "</div>";
        }
    }
    
    // This module presents some information abour the application when the user
    // initially starts the app
    public function dashboard() {
        
        // Create some placeholder or banner information or some important 
        // information
        $banner = "<div class='container my-5 p-5 bg-primary bg-gradient text-center text-light rounded'>"
                . "<p class='pt-5 display-6'>Welcome to Weather Monitor</p>"
                . "<p class='pb-5'><i>Your weather monitoring companion</i></p>"
            . "</div>";
        
        // Display some features of the app
        $features = "<div class='row g-3'>"
            . "<div class='col-4'>"
                . "<div class='card border-0 text-center'>"
                    . "<div class='card-text text-center fs-1'>"
                        . "<span class='bi-snow2 text-center text-info display-1'></span>"
                    . "</div>"
                    . "<div class='card-body'>"
                        . "<h5 class='pt-5'>Current Weather Forecast</h5>"
                        . "<p class='pt-3'>With Weather Monitor you can preview the"
                            . " current weather for the day. You can also"
                            . " preview the weather forecast for the next 16 Days.</p>"
                    . "</div>"
                . "</div>"
            . "</div>"
            . "<div class='col-4'>"
                . "<div class='card border-0 text-center'>"
                    . "<div class='card-text text-center fs-1'>"
                        . "<span class='bi-sun text-center text-warning display-1'></span>"
                    . "</div>"
                    . "<div class='card-body'>"
                        .  "<h5 class='pt-5'>16-Day Weather Forecast</h5>"
                        . "<p class='pt-3'>With Weather Monitor you have the capability to view"
                            . " the weather forecast for the next 16 days!"
                            . " You are able to preview this data for any city you choose. </p>"
                    . "</div>"
                . "</div>"
            . "</div>"
            . "<div class='col-4'>"
                . "<div class='card border-0 text-center'>"
                    . "<div class='card-text text-center fs-1'>"
                        . "<span class='bi-thermometer text-center text-danger display-1'></span>"
                    . "</div>"
                    . "<div class='card-body'>"
                        . "<h5 class='pt-5'>Search History</h5>"
                        . "<p class='pt-3'>Weather Monitor also keeps a record of the weather"
                            . " data for up to 5 of the most recent cities you have "
                            . " searched data for. Check your search history. </p>"
                    . "</div>"
                . "</div>"
            . "</div>"
        . "</div>";

        return $banner . $features;
    }
    
    // This module displays a profile for the logged-in user
    public function profile() {
        
        // Create a database connection
        $db = new Database;
        $conn = $db->databaseConnection();
        $profileDetails[] = '';
        
        // Create a database query to fetch profile information
        $sql = "SELECT * FROM USER WHERE ID = ".$_SESSION['id'].";";
        $result = $conn->query($sql);
        
        // The result will most likely be present because this module is called
        // from a logged-in session
        while ($row = $result->fetch_assoc()){
            
            // Store the fetched data for preview
            $profileDetails[] = "<div class='container border-0 border-bottom p-0 pt-5 mt-5'>"
                    . "<p class='display-6'>".$row['FULL_NAME']."</p>"
                    . "<p>".$row['CONTACT_NUMBER']."</p>"
                    . "<p>".$row['E_MAIL']."</p>"
                . "</div>";
            
            // Enable the user to also logout by adding a logout button
            $profileDetails[] = "<div class='py-3 align-content-sm-end'>"
                    . "<a class='btn btn-primary bg-gradient' href='./?content=logout'>Logout</a>"
                . "</div>";
        }
        
        return join($profileDetails);
    }
    
    // This module clears all session variables and logs the user out of the app
    public function logout () {
        
        // Unset the session variables
        session_unset();
        
        // Discard the session
        session_destroy();
        
        // Redirect the app to the homepage
        header("location: ./");
    }
}
