<?php

/**
 * config.php
 *
 * Computer Science 50
 * Arianna Benson
 *
 * Configures pages.
 */

// display errors, warnings, and notices
ini_set("display_errors", true);
error_reporting(E_ALL);

// requirements
require("constants.php");
require("functions.php");
require_once realpath(dirname(__FILE__) . '/../autoload.php');

// enable sessions
session_start();

// require authentication for all pages except /login.php, /logout.php, and /register.php
if (!in_array($_SERVER["PHP_SELF"], ["/user.php"]))
{
    if (empty($_SESSION["access_token"]))
    {
        redirect("user.php");
    }
}

// authenticate user with Google API
$client_id = '223565699514-qjglliq9ff4pdf0khmjvk7jo0n8dbnv3.apps.googleusercontent.com';
$client_secret = 'vt7a8VhLkFh3azbP_-p3X7v2';
$redirect_uri = 'http://localhost/user.php';

// make API request on behalf of user
$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope("https://www.googleapis.com/auth/plus.me");
$client->addScope("https://www.googleapis.com/auth/plus.login");
$client->addScope("https://www.googleapis.com/auth/userinfo.email");
$client->addScope("https://www.googleapis.com/auth/userinfo.profile");
$client->addScope("https://www.googleapis.com/auth/calendar");

// create services
$plus = new Google_Service_Plus($client);
$cal = new Google_Service_Calendar($client);

// set access token, if not yet set
if (isset($_SESSION['access_token']) && $_SESSION['access_token'])
{
  $client->setAccessToken($_SESSION['access_token']);
}
else
{
  $authUrl = $client->createAuthUrl();
}
?>
