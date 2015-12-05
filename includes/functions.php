<?php

    /**
     * functions.php
     *
     * Computer Science 50
     * Problem Set 7
     *
     * Helper functions.
     */
    
    require("libphp-phpmailer/class.phpmailer.php");
    require_once("constants.php");
    

    /**
     * Apologizes to user with message.
     */
    function apologize($message)
    {
        render("apology.php", ["message" => $message]);
        exit;
    }

    /**
     * Facilitates debugging by dumping contents of variable
     * to browser.
     */
    function dump($variable)
    {
        require("../templates/dump.php");
        exit;
    }

    /**
     * Logs out current user, if any.  Based on Example #1 at
     * http://us.php.net/manual/en/function.session-destroy.php.
     */
    function logout()
    {
        // unset any session variables
        $_SESSION = [];

        // expire cookie
        if (!empty($_COOKIE[session_name()]))
        {
            setcookie(session_name(), "", time() - 42000);
        }

        // destroy session
        session_destroy();
    }

    /**
     * Executes SQL statement, possibly with parameters, returning
     * an array of all rows in result set or false on (non-fatal) error.
     */
    function query(/* $sql [, ... ] */)
    {
        // SQL statement
        $sql = func_get_arg(0);

        // parameters, if any
        $parameters = array_slice(func_get_args(), 1);

        // try to connect to database
        static $handle;
        if (!isset($handle))
        {
            try
            {
                // connect to database
                $handle = new PDO("mysql:dbname=" . DATABASE . ";host=" . SERVER, USERNAME, PASSWORD);

                // ensure that PDO::prepare returns false when passed invalid SQL
                $handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
            }
            catch (Exception $e)
            {
                // trigger (big, orange) error
                trigger_error($e->getMessage(), E_USER_ERROR);
                exit;
            }
        }

        // prepare SQL statement
        $statement = $handle->prepare($sql);
        if ($statement === false)
        {
            // trigger (big, orange) error
            trigger_error($handle->errorInfo()[2], E_USER_ERROR);
            exit;
        }

        // execute SQL statement
        $results = $statement->execute($parameters);

        // return result set's rows, if any
        if ($results !== false)
        {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            return false;
        }
    }

    /**
     * Redirects user to destination, which can be
     * a URL or a relative path on the local host.
     *
     * Because this function outputs an HTTP header, it
     * must be called before caller outputs any HTML.
     */
    function redirect($destination)
    {
        // handle URL
        if (preg_match("/^https?:\/\//", $destination))
        {
            header("Location: " . $destination);
        }

        // handle absolute path
        else if (preg_match("/^\//", $destination))
        {
            $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            header("Location: $protocol://$host$destination");
        }

        // handle relative path
        else
        {
            // adapted from http://www.php.net/header
            $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
            header("Location: $protocol://$host$path/$destination");
        }

        // exit immediately since we're redirecting anyway
        exit;
    }

    /**
     * Renders template, passing in values.
     */
    function render($template, $values = [])
    {
        $links = [];
        // render links only if the user is logged in
        if ($template != "decision.php")
        {
            $links[] = ["title" => "Events", "url" => "/events.php"];
            $links[] = ["title" => "Make Event", "url" => "/newevent.php"];
            $links[] = ["title" => "Logout", "url" => "/user.php?logout"];
        }
        // if template exists, render it
        if (file_exists("../templates/$template"))
        {
            // extract variables into local scope
            extract($values);

            // render header
            require("../templates/header.php");

            // render template
            require("../templates/$template");

            // render footer
            require("../templates/footer.php");
        }

        // else err
        else
        {
            trigger_error("Invalid template: $template", E_USER_ERROR);
        }
    }
    
    /**
        Returns an associative array that describes the user.
    */
    function authenticate($plus)
    {
        // attempt to authenticate the user using Google+
        try
        {
            $me = $plus->people->get("me");
        }
        catch(Exception $e)
        {
            // if the Google Plus object is not working, reauthenticate user by logging out
            redirect("/user.php?logout");
        }
        
        // select user and their information
        $user_row = query("SELECT id FROM users WHERE email = ?", $me['modelData']["emails"][0]["value"]);
        if ($user_row === false)
        {
            // redirect user to be logged in
            redirect("/user.php");
        }
        
        // format information in easily-accessible form
        $user = ["name" => $me['displayName'],
                 "email" => $me['modelData']["emails"][0]["value"],
                 "id" => $user_row[0]["id"]];
                 
        // return user information to function caller
        return $user;
    }
    
    /**
        Sends an email to a given email address.
    */
    function email($email, $title, $contents)
    {
        $mail = new PHPMailer();
        
        // using the harvard server
        $mail->IsSMTP();
        $mail->Host = "smtp.fas.harvard.edu";

        // set actual contents of email
        $mail->SetFrom("ariannabenson@gmail.com");
        $mail->AddAddress($email);
        $mail->Subject = $title;
        $mail->Body = $contents;

        // send mail and die otherwise
        if ($mail->Send() === false)
            die($mail->ErrorInfo . "\n");
    
    }

?>
