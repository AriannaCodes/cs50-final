<?php
/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
 
 /*
 modified by Arianna Benson for CS50 Final Project
 */

require("../includes/config.php");

// check if user wants to log out
if (isset($_REQUEST['logout']))
{
    unset($_SESSION['access_token']);
}

// if this page is being accessed through oAuth flow
if (isset($_GET['code']))
{
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

// if the user has not yet signed in
if (isset($authUrl))
{
    render("decision.php", ["title" => "Log In", "url" => $authUrl]);
}
else
{
    // attempt to get user's info, or log them out to try again
    try
    {
        $me = $plus->people->get("me");
    }
    catch (Exception $e)
    {
        redirect("user.php?logout");
    }
    $email = $me['modelData']["emails"][0]["value"];
    $name = $me['displayName'];
    
    // insert user into table, or set existing user to be registered
    $q = query("INSERT INTO users(name, email, registered) VALUES(?,?,1)
           ON DUPLICATE KEY UPDATE registered=1, name=?", $name, $email, $name);
    if ($q === false)
    {
        apologize("User failed to insert.");
    }   
    redirect("index.php");
}
?>

