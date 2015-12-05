<script>
$(function() {
    // apply jquery datepicker to start and end dates
    $("#start").datepicker();
    $("#end").datepicker();
    $("#length").spinner({ step: 0.5 });
    // make sure all checkboxes are unchecked upon viewing page
    $("input[type=checkbox]").prop('checked', false);
});
</script>

<form action="newevent.php" id="newevent" method="post">
    <fieldset>
        <div class="form-group">
            <input autofocus class="form-control" name="name" placeholder="Event Name" type="text"/>
        </div>
        <div class="form-group">
            <input autofocus class="form-control" name="description" placeholder="Description" type="text"/>
        </div>
        <div class="form-group">
            <input autofocus class="form-control" name="start" id="start" placeholder="Starting Date" type="text"/>
        </div>
        <div class="form-group">
            <input autofocus class="form-control" name="end" id="end" placeholder="Ending Date" type="text"/>
        </div>
        <div class="form-group">
                <input id="day-0" name="day[]" value="0" type="checkbox" />
                <input id="day-1" name="day[]" value="1" type="checkbox" />
                <input id="day-2" name="day[]" value="2" type="checkbox" />
                <input id="day-3" name="day[]" value="3" type="checkbox" />
                <input id="day-4" name="day[]" value="4" type="checkbox" />
                <input id="day-5" name="day[]" value="5" type="checkbox" />
                <input id="day-6" name="day[]" value="6" type="checkbox" />
                <img width="25" src="/img/m.png" title="Monday" onclick="toggleBox(this, '#day-0')" />
                <img width="25" src="/img/t.png" title="Tuesday" onclick="toggleBox(this, '#day-1')" />
                <img width="25" src="/img/w.png" title="Wednesday" onclick="toggleBox(this, '#day-2')" />
                <img width="25" src="/img/t.png" title="Thursday" onclick="toggleBox(this, '#day-3')" />
                <img width="25" src="/img/f.png" title="Friday" onclick="toggleBox(this, '#day-4')" />
                <img width="25" src="/img/s.png" title="Saturday" onclick="toggleBox(this, '#day-5')" />
                <img width="25" src="/img/s.png" title="Sunday" onclick="toggleBox(this, '#day-6')" />
            </table>
        </div>
        <div class="form-group">
                <input id="time-0" name="time[]" value="0" type="checkbox" />
                <input id="time-1" name="time[]" value="1" type="checkbox" />
                <input id="time-2" name="time[]" value="2" type="checkbox" />
                <input id="time-3" name="time[]" value="3" type="checkbox" />
                <img width="46" src="/img/sunrise.png" title="Morning" onclick="toggleBox(this, '#time-0')" />
                <img width="46" src="/img/sun.png" title="Afternoon" onclick="toggleBox(this, '#time-1')" />
                <img width="46" src="/img/sunrise.png" title="Evening" onclick="toggleBox(this, '#time-2')" />
                <img width="46" src="/img/moon.png" title="Night" onclick="toggleBox(this, '#time-3')" />
            </table>
        </div>
        <div class="form-group">
            <input autofocus class="form-control" name="length" id="length" placeholder="Hours" type="text"/>
        </div>
        <div id="attendees" class="form-group">
            <input autofocus class="form-control" name="attendees" placeholder="Attendees" type="text"/>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-default">Create Event</button>
        </div>
    </fieldset>
</form>
