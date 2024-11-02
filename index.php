<?php
    
    // Include file dependencies
    include_once 'applets/ContentManager.php';
    include_once 'applets/Form.php';

    // Create a content management object
    $contentManager = new ContentManager;
    
    // If the content parameter is set in the URL, customise the displayed 
    // content based on URL parameters
    if(filter_input(INPUT_GET, 'content')){
        
        // Prepare the content parameter to be used to retrieve content
        $content = filter_input(INPUT_GET, 'content');
    }else{
        
        // If the content parameter is not set, retrieve the homepage
        $content = 'dashboard';
    }
    

?>

<!DOCTYPE html>

<html>
    <head>
        <!-- Load Bootstrap Styling and Icon files here -->
        <link href="theme/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="theme/icons/font/bootstrap-icons.min.css" rel="stylesheet" type="text/css"/>
        <meta charset="UTF-8">
        <title>Weather Monitor</title>
    </head>
    <body>
        
        <!-- Present navigational content here -->
        <nav class="navbar navbar-expand-sm navbar-expand-sm bg-white shadow">
            <div class='container'>
                <div class='navbar-brand'>                    
                    <a class='nav-link fs-3' href='./'>
                        <span class='bi-cloud-sun-fill text-warning font-size pe-2'></span>
                        <span class='text-secondary'>Weather Monitor</span>
                    </a>
                </div>
                <ul class='nav'>
                    <li class='nav-item'>
                        <a class='nav-link' href='?content=current_weather'>Current Forecast</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='?content=daily_weather'>Daily Forecast</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' href='?content=search_history'>Search History</a>
                    </li>
                    <li class='nav-item'>
                        <?php
                            
                            // Check if a user is logged-in
                            if(isset($_SESSION['full_name'])){
                                
                                // If a user is logged-in show the logged-in user's full name
                                echo "<a class='nav-link' href='?content=profile'>".$_SESSION['full_name']."</a>";
                            }else{
                                
                                // If a user is not logged-in show a link to login
                                echo "<a class='nav-link' href='?content=login'>Login</a>";
                            }

                        ?>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Present all main content here -->
        <div class='container-fluid px-0'>
            <?php
                //
                if(filter_input(INPUT_GET, 'content') == 'current_weather' || filter_input(INPUT_GET, 'content') == 'daily_weather'){
                    $form = new Form;
                    echo $form->searchForm();
                }
            ?>
        </div>
        
        <!-- Present all secondary content here -->
        <div class='container mb-5'>
            <?php                
                echo $contentManager->ContentManager($content);
            ?>
        </div>
        
        <!-- Present footer information here -->
        <div class='row text-muted bg-light py-0 mt-5 border border-top w-100 mt-5 position-fixed bottom-0'>
            <p class='text-center'>Weather Monitor &copy; 2024</p>
        <div>
    </body>
    
    <!-- Load Bootstrap Script files here -->
    <script src="scripts/bootstrap.min.js" type="text/javascript"></script>
</html>
