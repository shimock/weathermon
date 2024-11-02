<?php

// Base on the layout of this file, weather data can be split and reassigned at
// multiple points in the document. The arrays below store this data as it is
// being split and reassigned so that no data is lost. These have been declared 
// with a global scope.
$currentWeatherData = $dailyWeatherData = array();

// Define a class to handle the retrieval and presentation of weather data
class Weather {
    
    // This function contructs and handles data requests to this class
    public function Weather($url_params) {
        
        // Retrieve the content request for each request
        $content = filter_input(INPUT_GET, 'content');
        
        // Determine the response based on the content requested
        switch ($content) {
            case 'current_weather':                
                $weather = $this->currentWeather($url_params);
                break;
            case 'daily_weather':
                $weather = $this->dailyWeather($url_params);
                break;
            default:
                // If the request is not defined, give some feedback to the
                // user. At this state, a 404 response can also suffice.
                $weather = "Could not understand your request";
        }
        
        return $weather;
    }
    
    // This module is responsible for obtaining data from weatherbit.io base on
    // the set of parameters provided in the URI. This module receives a URL and
    // responds with json data obtained from the API
    public function getData($url) {
        
        // Initialise a cURL session 
        $curl = curl_init();
        
        // Set options for the cURL
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "cache-control: 600"
                ),
        ));
        
        // Store the response
        $response = curl_exec($curl);
        
        // Since the response in json, convert it to a regular array in order to
        // make it easier to work with the data.
        $data = json_decode($response, true);
        
        return $data;
    }
    
    // This module writes the searched results to the database. The saved data 
    // is serialised with the user's id and a timestamp. If a user is not
    // logged-in, the searched data is not saved.
    public function saveSearchResults($category, $city, $country_code, $user, $data) {
        
        // Initiate a database connection object
        $db = new Database;
        $conn = $db->databaseConnection();
        
        // Convert the weather data to json to make it easier to be writted to
        // the database. It would also be important at this stage to prepare
        // and filter out unwanted characters which may be found in the data
        $json = json_encode($data);
        
        // Prepare a database insert command
        $sql = "INSERT INTO SEARCH_HISTORY (`USER`,`CITY`,`COUNTRY_CODE`,`DATA`,`CATEGORY`,`CREATED`)"
                . "VALUES (" . $user . ", '" . $city . "', '" . $country_code . "', '" . $json . "', '" . $category . "', CURDATE());";
        
        // If the inertion of data to the database is successfull, provide
        // feedback to the user. It would be important to also format this
        // output e.g., using a pop-up, to enhance the user's experience.
        if ($conn->query($sql)) {
            
            // Info about a successful database data insertion can be displayed 
            // here. Leaving this blank will perform a silent inertion.
            // Silent: echo "Insert Successful";
        } else {
            // Info about an unsuccessful database data insertion can be displayed 
            // here. Leaving this blank will perform a silent inertion.
            // Silent: echo "Insert Failed";
        }
    }
    
    // This module prepares a request for Current Weather. It prepares and sends
    // an API Request for current weather and stores the received data
    public function currentWeather($url_params) {
        
        // URL for API request
        $url = 'https://api.weatherbit.io/v2.0/current' . $url_params;
        
        // Store the received data from the API request
        $data = $this->getData($url);
        
        // Check if any data was retrieved from the API
        if (isset($data)) {
            
            // If some data was retried from the API, check if a user is
            // logged-in.
            if (isset($_SESSION['id'])) {
                
                // If a user is logged-in request to save the retrieved data
                // to the database.
                $this->saveSearchResults('current', $data['data'][0]['city_name'], $data['data'][0]['country_code'], $_SESSION['id'], $data);
            }
            
            // Format the retrieved data so that the user can preview and 
            // understand it
            $currentWeather = $this->previewCurrentWeatherForecast($data, 'current', '');
        } else {
            
            // If there was no response from the API request, provide feedback
            // to the user. A 404 notice would not be apropriate at this stage,
            // rather a notice explaining the the failure to retrieve data. This
            // resoponse can also be made a bit more informative to enhance the
            // user's experience.
            $currentWeather = "<div class='container p-2 mt-5 text-center border border-warning bg-warning bg-opacity-25 rounded'>"
                    . "<span class='bi-exclamation-triangle pe-2 '></span>"
                    . "A response could not be received. Please ensure that you are connected to the internet and try again."
                . "</div>";
        }

        return $currentWeather;
    }
    
    // This module presents the retrived current weather data in a format that
    // can be easily understood by the user. Several enhancements can be made to
    // the code presented here.
    public function previewCurrentWeatherForecast($data, $index, $active) {
        
        // Initialise an array to hold the formating of the retrieved current
        // weather data.
        $currentWeather[] = "<div class='tab-pane container ".$active."' id='".$index."'>";
        $currentWeather[] = "<div class='display-6 pt-5 pb-2'><small>".$data['data'][0]['city_name'].", ".$data['data'][0]['country_code']."</small></div>";
        $currentWeather[] = "<table class='table table-responsive table-striped'>";
        $currentWeather[] = "<thead><tr><th>Weather</td><th>Value</td></tr></thead>";

        $currentWeather[] = "<tbody><tr><td>Temperature</td><td>" . $data['data'][0]['temp'] . "</td></tr>"
                . "<tr><td>Feels Like Temperature</td><td>" . $data['data'][0]['app_temp'] . "</td></tr>"
                . "<tr><td>Weather Description</td><td>" . $data['data'][0]['weather']['description'] . "</td></tr>"
                . "<tr><td>Wind Speed</td><td>" . $data['data'][0]['wind_spd'] . "</td></tr>"
                . "<tr><td>Humidity</td><td>" . $data['data'][0]['rh'] . "</td></tr>"
                . "<tr><td>Air Quality Index</td><td>" . $data['data'][0]['aqi'] . "</td></tr>"
                . "<tr><td>City Name</td><td>" . $data['data'][0]['city_name'] . "</td></tr>"
                . "<tr><td>Country Code</td><td>" . $data['data'][0]['country_code'] . "</td></tr>"
                . "<tr><td>Time Observed (GMT)</td><td>" . $data['data'][0]['ob_time'] . "</td></tr></tbody>";

        $currentWeather[] = "</table></div>";

        return join($currentWeather);
    }
    
    // This module retrieves daily weather data. It prepares a URL to make an 
    // API request
    public function dailyWeather($url_params) {
        
        // URL for API request
        $url = 'https://api.weatherbit.io/v2.0/forecast/daily' . $url_params;
        
        // Store the received data from the API request
        $data = $this->getData($url);
        
        // Check if some data has been received from the API Request
        if(isset($data)){
            
            // If data has been received, check if a user is logged-in
            if (isset($_SESSION['id'])) {
                
                // If a user is logged-in request to write the retrieved to the 
                // database
                $this->saveSearchResults('daily', $data['city_name'], $data['country_code'], $_SESSION['id'], $data);
            }
            
            // Get daily weather data and store in global
            // dailyWeatherData variable.
            $this->getDailyWeatherData($data);
            
            // Get formatted daily weather data
            $dailyWeather = $this->previewDailyWeatherForecast('','');
            
        } else {
            
            // If there was no response from the API request provide feedback to
            // the user
            $dailyWeather = "<div class='container p-2 mt-5 text-center border border-warning bg-warning bg-opacity-25 rounded'>"
                    . "<span class='bi-exclamation-triangle pe-2 '></span>"
                    . "A response could not be received. Please ensure that you are connected to the internet and try again.";
        }
        
        return $dailyWeather;
    }
    
    // This module sets the retrieved weather data and stores it into the global
    // dailyWeatherData array
    public function getDailyWeatherData($data) {
        
        // Prepare the dailyWeatherData global variable to receive the data
        global $dailyWeatherData;
        
        // Initialise a counter to be used to reference the array via the 
        // array keys
        $count = 0;
        
        // Create some header information for the presented daily weather data
        $dailyWeatherData['city_name'] = $data['city_name'];
        $dailyWeatherData['country_code'] = $data['country_code'];
        $dailyWeatherData['timezone'] = $data['timezone'];
        
        // Store the data into the global dailyWeatherData variable. This step 
        // may not be a functionally important step, but it is used to store the 
        // data in a controlled format for later referencing purposes
        foreach ($data['data'] as $key => $value) {
            
            if (is_array($value)) {
                
                // The retrieved data contains an array of arrays. This step 
                // changes this format into an easier format for reference 
                // purposes
                $this->setDailyWeatherData($count, $value);
                
            } else {
                
                // If no array of arrays are encountered, the raw data is written to 
                // the global dailyWeatherData array
                $dailyWeatherData[$count][$key] = $value;
            }
            
            // Once the first iteration is complete, the counter is incremented
            // in preperation for the next array key if present.
            $count++;
        }

        return $dailyWeatherData;
    }
    
    // This module converts an array of arrays encounterred in the data 
    // retrieved from the API into an easy-to-reference format
    public function setDailyWeatherData($index, $data) {
        
        // Prepare the global dailyWeatherData array
        global $dailyWeatherData;
        
        // Inspect the provided data
        foreach ($data as $key => $value) {
            
            //Check if the provided data is an array
            if (is_array($value)) {
                
                //Whenever, an array of arrays is encountered in the
                // data, this module references itself to dismantle the nested
                // array
                $this->setDailyWeatherData($index, $value);
                
            } else {
                
                // When no arrays are encountered, the raw data is written to 
                // the global dailyWeatherData array
                $dailyWeatherData[$index][$key] = $value;
            }
        }
        return $dailyWeatherData;
    }
    
    // This module prepares the data written to the global dailyWeatherData for 
    // presentation
    public function previewDailyWeatherForecast($index, $active) {
        
        // Prepare the global dailyWeatherData
        global $dailyWeatherData;
        
        // Initialise an array to store the html preview of the data stored in 
        // the global dailyWeatherData array. The data is presented in a table
        // format
        $preview[] = "<div class='tab-pane container ".$active."' id='".$index."' role='tabpanel'>";
        $preview[] = "<div class='display-6 pt-5 pb-2'><small>16-Day Weather Forecast</small></div>";
        $preview[] = "<div class='text-muted pt-5 pb-2'>"
                . "<p>City Name: ".$dailyWeatherData['city_name']."</p>"
                . "<p>Country Code: ".$dailyWeatherData['country_code']."</p>"
                . "<p>Time Zone: ".$dailyWeatherData['timezone']."</p>"
            . "</div>";
        $preview[] = "<table class='table table-responsive table-striped table-borderless'>";
        $preview[] = "<thead class='table-secondary'>"
                . "<tr>"
                . "<th>Date</th><th>Maximum Temperature</th><th>Minimum Temperature</th><th>Precipitation</th><th>UV Index</th><th>Weather Description</th><th>Wind Direction</th><th>Wind Speed</th>"
                . "</tr>"
            . "</thead><tbody>";
        
        // Loop through all the attributes in the stored data
        for ($i = 0; $i < 16; $i++) {
            $preview[] = "<tr>";
            foreach ($dailyWeatherData[$i] as $key => $value) {
                
                // Preview only attributes that are required. At this stage
                // unrequired attributes are discarded
                if ($key == 'datetime' || $key == 'max_temp' || $key == 'min_temp' || $key == 'description' || $key == 'precip' || $key == 'uv' || $key == 'wind_cdir_full' || $key == 'wind_spd') {
                    $preview[] = "<td>" . $value . "</td>";
                }
            }
            $preview[] = "</tr>";
        }
        $preview[] = "</tbody></table></div>";
        

        return join($preview);
    }
}
