<p><em><?= $event["description"] ?></em></em>
<?php
// if the event's time and date have not yet been decided
if ($event["status"] == 0)
{
    ?>
    <p><img src="img/waiting.png" title="Waiting on responses..." /></p>
    <?php
    // if the user has not yet responded, give them a link
    if ($attending == 0)
    {
    ?>
        <p><a href="response.php?id=<?=$event['id']?>">Respond to event!</a></p>
    <?php
    }
    ?>
    <p><strong>Start: </strong> <?= $event["start"] ?></p>
    <p><strong>End: </strong> <?= $event["end"] ?></p>
    <p><strong>Length: </strong> <?= $event["length"] ?> hours</p>
    <p><strong>Days of week: </strong>
    <?php
    // display days of the week as images
    foreach ($event["days_of_week"] as $day)
    {
        echo "<img src='img/{$day_map[$day][0]}' title='{$day_map[$day][1]}' width='25' /> ";
    }
    ?></p>
    <p><strong>Time of day: </strong>
    <?php
    // display times of the day as images
    foreach ($event["time_of_day"] as $time)
    {
        echo "<img src='img/{$time_map[$time][0]}' title='{$time_map[$time][1]}' width='46' /> ";
    }
    ?></p>
   <?php
}
// otherwise, if the event's time and date have been decided
else
{
    ?>
    <p><img src="img/decided.png" title="Decided on date/time" /></p>
    <p><strong>Start time:</strong> <?= $event["time_start"] ?></p>
    <p><strong>End time:</strong> <?= $event["time_end"] ?></p>
    <?php
}
?>

<p><strong>Attendees:</strong> 
<?php
// display all people who are attending the event
foreach ($attendees as $attendee)
{
    // color code the attendees, depending on their responses
    $color = "gray";
    if ($attendee["attending"] == 1)
    {
        $color = "green";
    }
    if ($attendee["attending"] == -1)
    {
        $color = "red";
    }
    if ($attendee["name"] !== "")
    {
        echo "<span style='color:{$color}' title='{$attendee["email"]}'>{$attendee["name"]}</span><br />";
    }
    else
    {
        echo "<span style='color:{$color}'>{$attendee["email"]}</span><br />";
    }
}
?>
