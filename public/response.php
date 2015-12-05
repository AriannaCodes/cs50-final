<?php

    // configuration
    require("../includes/config.php");
    
    // get user and store in associative array
    $user = authenticate($plus);
    
    // ensure user is trying to access event
    if (($_SERVER['REQUEST_METHOD'] == "GET") && (!isset($_GET["id"]) || $_GET["id"] == ""))
    {
        apologize("You haven't selected an event to which to respond.");
    }
    
    // get event's ID, regardless of use of POST or GET
    $id = (isset($_POST["id"])) ? $_POST["id"] : $_GET["id"];

    // select event with id, host, and attendee
    $event = query("SELECT events.*, users.name AS user_name, users.email AS user_email FROM events
                    JOIN users
                        ON users.id = events.host
                    WHERE events.id = ?", $id);
    if ($event === false)
    {
        apologize("This event does not exist.");
    }
                        
    // select attendee of the event
    $attendee = query("SELECT * FROM users
                        JOIN invites
                            ON invites.user_id = users.id
                        WHERE invites.event_id = ?
                            AND invites.user_id = ?
                        ORDER BY invites.attending DESC", $id, $user["id"]);
    if ($attendee === false)
    {
        apologize("You cannot RSVP to this event.");
    }
    
    // if user reached page via GET (as by clicking a link or via redirect)
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        render("response_form.php", ["title" => "Respond to Invitation", "event" => $event[0]]);
    }
    
    // else if user reached page via POST (as by submitting a form via POST)
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {      
        // convert string for $attending into 1 or -1 value
        $attending = ($_POST["attending"] == "true") ? 1 : -1;
    
        // change user's invite to either attending or not attending
        $q = query("UPDATE invites SET attending = ? WHERE user_id = ?", $attending, $user["id"]);
        if ($q === false)
        {
            apologize("Your invite failed to update. Are you sure you were invited to this event?");
        }
        
        // check if we can determine event time given the number of users
        $attendees = query("SELECT * FROM users
                            JOIN invites
                                ON invites.user_id = users.id
                            WHERE invites.event_id = ? AND attending = 1
                            ORDER BY invites.attending DESC", $id);
        if ($attendees === false)
        {
            apologize("We couldn't figure out how many attendees are at the event.");
        }
        
        // if not everyone has replied to the event, redirect back to event's page
        $numbers = query("SELECT COUNT(*) AS num FROM invites WHERE event_id = ? AND attending = 0", $id);
        if ($numbers[0]["num"] != 0)
        {
            redirect("/event.php?id=" . $id);
        }
        
        // make a list of users' calendars that we want to access
        $cals = array();
        for ($i = 0; $i < sizeof($attendees); $i++)
        {
            $item = new Google_Service_Calendar_FreeBusyRequestItem();
            $item->setId($attendees[$i]["email"]);
            $cals[] = $item;
        }
        
        // make request to Google
        $req = new Google_Service_Calendar_FreeBusyRequest();
        $req->setTimeMin(date(DateTime::ATOM, strtotime($event[0]["start"])));
        $req->setTimeMax(date(DateTime::ATOM, strtotime($event[0]["end"])));
        $req->setItems($cals);
        $query = $cal->freebusy->query($req);
        $numcals = sizeof($query);
        
        // go through each user's calendars
        $busy;
        foreach ($query["calendars"] as $key => $calendar)
        {
            // iterate through each interval of business
            foreach ($calendar["busy"] as $object)
            {
                // for all the time intervals in which they are busy, increase the number of busy people
                for ($i = strtotime($object->start); $i < strtotime($object->end); $i = $i + 60*30)
                {
                    // increase the count of people busy at a given time
                    if (isset($busy[$i]))
                    {
                        $busy[$i]++;
                    }
                    else
                    {
                        $busy[$i] = 1;
                    }
                }
            }
            
        }
        
        
        // unserialize information about the timing of the event
        $day_of_week = unserialize($event[0]["day_of_week"]);
        $time_of_day = unserialize($event[0]["time_of_day"]);
        
        // determine possible times for meeting to take place
        $posstimes;
        // iterate through days in interval given by event author
        for ($day = strtotime($event[0]["start"]); $day < strtotime($event[0]["end"]); $day = $day + 60*60*24)
        {
            // check if day is selected
            if (isset($day_of_week[(date('N', $day) - 1)]))
            {
                // night, from 12 midnight to 6 am, in increments of 30 minutes
                if (isset($time_of_day[0]))
                {
                    for ($i = $day; $i < $day + 60*60*6; $i = $i + 60*30)
                    {
                        $posstimes[] = $i;
                    }
                }
                // morning, from 6 am to 12 noon, in increments of 30 minutes
                if (isset($time_of_day[2]))
                {
                    for ($i = $day + 60*60*6; $i < $day + 60*60*12; $i = $i + 60*30)
                    {
                        $posstimes[] = $i;
                    }
                }
                // afternoon, from 12 noon to 6 pm, in increments of 30 minutes
                if (isset($time_of_day[2]))
                {
                    for ($i = $day + 60*60*12; $i < $day + 60*60*18; $i = $i + 60*30)
                    {
                        $posstimes[] = $i;
                    }
                }
                // evening, from 6pm to 12 midnight, in increments of 30 minutes
                if (isset($time_of_day[3]))
                {
                    for ($i = $day + 60*60*18; $i < $day + 60*60*24; $i = $i + 60*30)
                    {
                        $posstimes[] = $i;
                    }
                }
            }
        }
       
        // set the default time of meeting as the first 50 mins of interval
        $starttime = $posstimes[0];
        $endtime = $starttime + 60 * $event[0]["length"];
        
        if (isset($busy[$starttime]))
        {
            $lowest = $busy[$starttime];
        }
        else
        {
            $lowest = 0;
        }
        
        // look through other times to find the time with the fewest people
        foreach ($posstimes as $time)
        {
            if (!isset($busy[$time]))
            {
                $busy[$time] = 0;
            }
            if ($busy[$time] < $lowest)
            {
                $starttime = $time;
                $lowest = $busy[$time];
            }
            if ($busy[$time] == $lowest)
            {
                $endtime = $time + 60 * $event[0]["length"];
            }
        }
        
        // update event status
        $q = query("UPDATE events SET status = 1, time_start = ?, time_end = ? WHERE id = ?",
                         date('Y-m-d H:i:s', $starttime), date('Y-m-d H:i:s', $endtime), $id);
        if ($q === false)
        {
            apologize("Your event could not save properly.");
        }
        
        // email people about event times
        foreach ($attendees as $attendee)
        {
            email($attendee["email"], "Event has been scheduled!", "<p>Hello {$attendee["name"]},</p>
                <p>Earlier on, you responded, {$user["name"]}'s event, {$event[0]["name"]}. We have now scheduled it!</p>
                <p><strong>Starting time</strong>: " . date('Y-m-d H:i:s', $starttime) . " </p>
                <p><strong>Ending time</strong>: " . date('Y-m-d H:i:s', $endtime) . " </p>
                <p>Click <a href='response.php?id={$id}'>here</a> to view the event!</p>
                <p>Sincerely,
                Merge</p>");
        }
        
        // redirect back to event page
        redirect("/event.php?id=" . $id);
    }
?>
