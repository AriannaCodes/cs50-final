<?php

    // configuration
    require("../includes/config.php");
    
    // get user and store in associative array
    $user = authenticate($plus);
    
    // ensure user is trying to access event
    if (!isset($_GET["id"]) || $_GET["id"] == "")
    {
        apologize("You haven't selected an event to view.");
    }
    
    // select event with id
    $event = query("SELECT events.*, users.name AS user_name, users.email AS user_email FROM events
                    JOIN users
                        ON users.id = events.host
                    WHERE events.id = ?", $_GET["id"]);
    if ($event === false)
    {
        apologize("This event does not exist.");
    }
                        
    // select all attendees of the event
    $attendees = query("SELECT * FROM users
                        JOIN invites
                            ON invites.user_id = users.id
                        WHERE invites.event_id = ?
                        ORDER BY invites.attending DESC", $_GET["id"]);
    if ($attendees === false)
    {
        apologize("This event has no attendees.");
    }
    
    // check if user can view event and has responded (default true if hosting)
    $canview = ($event[0]["host"] == $user["id"]) ? true : false;
    $attending = ($event[0]["host"] == $user["id"]) ? true : false;
    
    foreach ($attendees as $attendee)
    {
        if ($attendee["id"] == $user["id"])
        {
            $canview = true;
            $attending = $attendee["attending"];
        }
    }
    if (!$canview)
    {
        apologize("You cannot view this event.");
    }
    
    // get title of event
    if ($event[0]["user_name"] !== "")
    {
        $title = $event[0]["name"] . " by " . "<span title='" . $event[0]["user_email"] . "'>" . $event[0]["user_name"] . "</span>";
    }
    else
    {
        $title = $event[0]["name"] . " by " . "<span>" . $event[0]["user_email"] . "</span>";
    }
    
    // unserialize days and times from database into arrays
    $event[0]["days_of_week"] = unserialize($event[0]["day_of_week"]);
    $event[0]["time_of_day"] = unserialize($event[0]["time_of_day"]);
    
    // define array maps for times, days to display template
    $day_map = array(['m.png', 'Monday'], ['t.png', 'Tuesday'], ['w.png', 'Wednesday'],
                     ['t.png', 'Thursday'], ['f.png', 'Friday'], ['s.png', 'Saturday'], ['s.png', 'Sunday']);
    $time_map = array(['sunrise.png', 'Morning'], ['sun.png', 'Afternoon'], ['sunrise.png', 'Evening'], ['moon.png', 'Night']);
    
    // render event
    render("event_view.php", ["title" => $title,
                              "event" => $event[0],
                              "day_map" => $day_map,
                              "time_map" => $time_map,
                              "attendees" => $attendees,
                              "attending" => $attending]);
?>
