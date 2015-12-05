<?php

    // configuration
    require("../includes/config.php");
    
    // get user and store in associative array
    $user = authenticate($plus);
   
    // select events to which a user has been invited or is a host
    $events = query("SELECT events.*, users.email AS user_email, users.name AS user_name, users.id AS user_id FROM events
                            JOIN invites
                                ON invites.event_id = events.id
                            JOIN users
                                ON users.id = events.host
                            WHERE invites.user_id = ?
                                OR events.host = ?
                            GROUP BY events.id", $user["id"], $user["id"]);
                            
    // if we have no events to list, make an empty array
    if ($events === false)
    {
        $events = [];
    }
    
    // render list of events
    render("event_list.php", ["title" => "Event List",
                              "events" => $events,
                              "user" => $user]);
?>
