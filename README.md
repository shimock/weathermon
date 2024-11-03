# weathermon
This Repository contains a weather application that fetches current weather data and a 16-day weather forecast for a city using the Weatherbit API

GENERAL INFORMATION:

This project has been developed using the following:

1. PHP
2. MariaDB
3. Bootstrap
4. HTML
5. CSS
6. JavaScript
   
This app was developed on the Apache XAMPP Server 8.2.4

INSTALLATION:

1. Download Apache Xampp 8.2.4 from here: https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.4/
2. Install and start the Xampp Server
   
   ![image](https://github.com/user-attachments/assets/c9de0fd0-267d-4c11-a4a5-3547439d4b82)


4. Pull the the contents of this repository WeatherMon and save it in the Xampp Server's directory (WindowsDefault: C:\xampp\htdocs\) 
5. Access you Xampp Server database (WindowsDefault: localhost/phpmadmin)
6. Import the WeatherMonSchema.sql script into the database
   
   ![image](https://github.com/user-attachments/assets/ba59b477-60d9-4526-af40-9892909c8202)

7. Go to the app using the Xampp Server's local root url (WindowsDefault: http://localhost/weathermon)

This is all you need to access the app.

THINGS TO TAKE NOTE OF:

1. If all went well in the installation you should be at this page when you access http://localhost/weathermon
   
   ![image](https://github.com/user-attachments/assets/727adad2-21c3-446d-a016-c2391a0618e4)

3.  The API will return valid data almost all the time. Sometimes, data may be valid but incorrect. E.g. fetching data for a city, Abram, in Texas, USA will return valid data for a city Abram in Great Britain.
  
     ![image](https://github.com/user-attachments/assets/12aa873f-5028-40af-bde1-8608beeac90c)
  
6.  Searching for a city name without a Country Code will still return data for that city. In later versions, this can easily be mitigated.
   
      ![image](https://github.com/user-attachments/assets/caee90c5-30bb-41e1-9503-5aad0af43964)


7. The API Key is hard-coded in the code. In later releases, the database can be used to store the api key in order to make it easier to change the api-key if need arises.

8. Historical data is only saved when a user is logged-in, however, anyone can search for weather data even when they are not logged-in.

