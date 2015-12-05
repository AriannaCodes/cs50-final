<p>View <a href="event.php?id=<?= $event["id"] ?>"><?= $event["name"] ?>.</a></p>
<p>Click the correct button below to tell the host whether or not you would like to attend this event!</p>
<div style="width: 210px; height: 40px; margin:auto;">
    <form action="response.php" id="newevent" method="post" style="float:left">
        <fieldset>
            <input type="hidden" name="id" value="<?= $event["id"] ?>">
            <input type="hidden" name="attending" value="true" />
            <div class="form-group">
                <button type="submit" class="btn-attending">Attending</button>
            </div>
        </fieldset>
    </form>
    <form action="response.php" id="newevent" method="post" style="float:right">
        <fieldset>
            <input type="hidden" name="id" value="<?= $event["id"] ?>">
            <input type="hidden" name="attending" value="false">
            <div class="form-group">
                <button type="submit" class="btn-notattending">Not Attending</button>
            </div>
        </fieldset>
    </form>
</div>
