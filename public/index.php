<?php

    // configuration
    require("../includes/config.php");
    
    // get user and store in associative array
    $user = authenticate($plus);
    
    // render home page
    render("home.php", ["title" => "Home Page", "user" => $user]);
?>
