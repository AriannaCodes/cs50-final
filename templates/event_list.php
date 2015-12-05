<?php
if (empty($events))
{
?>
    <p>You haven't been created or been invited to any events yet!</p>
<?php
}
else
{
    foreach($events as $event) {
    ?>
        <p>
            <a href="event.php?id=<?= $event["id"] ?>"> <?= $event["name"] ?> </a> by
             <?php
                // format host differently depending on what information we have
                if ($event["user_id"] == $user["id"])
                {
                    echo "you";
                }
                else if ($event["user_name"] !== "")
                {
                    echo "<span title='{$event["user_email"]}'>{$event["user_name"]}</span>";
                }
                else
                {
                    echo "{$event["user_email"]}";
                }
                ?>
        </p>
    <?php
    }
}
?>
