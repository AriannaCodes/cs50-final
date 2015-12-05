<?php

    // configuration
    require("../includes/config.php"); 
    
    // get user and store in associative array
    $user = authenticate($plus);

    // if user reached page via GET (as by clicking a link or via redirect)
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        // render form to find stock
        render("event_form.php", ["title" => "Create New Event"]);
    }

    // else if user reached page via POST (as by submitting a form via POST)
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // validate submission
        if ($_POST["name"] == "")
        {
            apologize("Your event needs a name.");
        }
        else if (!($start = strtotime($_POST["start"])))
        {
            apologize("You must input a valid start date.");
        }
        else if (!($end = strtotime($_POST["end"])))
        {
            apologize("You must input a valid end date.");
        }
        else if (time() > $start)
        {
            apologize("Your event must take place in the present.");
        }
        else if ($end < $start)
        {
            apologize("Your event must end after it begins.");
        }
        else if (!isset($_POST["day"]))
        {
            apologize("You must select at least one day of the week.");
        }
        else if (!isset($_POST["time"]))
        {
            apologize("You must select at least one time of day.");
        }
        else if ($_POST["attendees"] == "")
        {
            apologize("You must add at least one attendee to your event.");
        }    
        // split up all attendees' emails
        $emails = preg_split("/[\s,]+/", $_POST["attendees"]);
        foreach ($emails as $email)
        {
            // validate attendee's email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                apologize("The email {$email} is not a valid email.");
            }
        }
        
        // insert event into database
        $q = query("INSERT INTO events(name, description, start, end, day_of_week, time_of_day, length, host) VALUES(?,?,?,?,?,?,?,?)",
                $_POST["name"], $_POST["description"], date("Y-m-d", $start), date("Y-m-d", $end), serialize($_POST["day"]),
                serialize($_POST["time"]), $_POST["length"], $user["id"]);
        $event_id = query("SELECT LAST_INSERT_ID() as id");
        if (($q === false) || ($event_id === false))
        {
            apologize("Event failed to insert.");
        }
        
        // invite attendees to event
        foreach ($emails as $email)
        {
            // insert attendee if they don't already exist
            $q = query("INSERT INTO users(email) VALUES(?) ON DUPLICATE KEY UPDATE email = email", $email);
            if ($q === false)
            {
                apologize("User failed to insert.");
            }

            // get user's ID 
            $attendee = query("SELECT id, name FROM users WHERE email = ? LIMIT 1", $email);
            if ($attendee === false)
            {
                apologize("The user with email {$email} could not be found.");
            }

            // insert user's invite into database
            $q = query("INSERT INTO invites (event_id, user_id)
                  VALUES(?,?)", $event_id[0]["id"], $attendee[0]["id"]);
            if ($q === false)
            {
                apologiz("Invite failed to insert.");
            }
            
            // email user with invite
            email($email, "Invite to {$_POST["name"]}", "<p>Hello {$attendee[0]["name"]},</p>
                                                        <p>You've been invited to {$user["name"]}'s event, {$_POST["name"]}.</p>
                                                        <p>Click <a href='response.php?id={$event_id[0]["id"]}'>here</a> to respond!</p>
                                                        <p>Sincerely,
                                                        Merge</p>");
        }
        
        // redirect user to page for newly created event
        redirect("/event.php?id=" . $event_id[0]["id"]);
    }
?>
