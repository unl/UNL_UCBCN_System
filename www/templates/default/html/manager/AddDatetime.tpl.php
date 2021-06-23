<?php
    $calendar = $context->calendar;
    $event = $context->event;
    $datetime = $context->event_datetime;

    if ($datetime->starttime == NULL) {
        $start_time = '';
        $start_date =  '';
        $start_hour = '';
        $start_minute = -1;
        $start_am_pm = 'am';
    } else {
        $start_time = strtotime($datetime->starttime);
        $start_date = date('m/d/Y', $start_time);
        $start_hour = date('h', $start_time);
        $start_minute = date('i', $start_time);
        $start_am_pm = date('a', $start_time);
    }

    if ($datetime->endtime == NULL) {
        $end_time = '';
        $end_date =  '';
        $end_hour = '';
        $end_minute = -1;
        $end_am_pm = 'am';
    } else {
        $end_time = strtotime($datetime->endtime);
        $end_date = date('m/d/Y', $end_time);
        $end_hour = date('h', $end_time);
        $end_minute = date('i', $end_time);
        $end_am_pm = date('a', $end_time);
    }

    $recurs_until_date = date('m/d/Y', strtotime($datetime->recurs_until));
?>
<?php
    $last_crumb = NULL;
    if ($context->recurrence_id != NULL) {
        $last_crumb = 'Edit a Single Instance from Recurring Event';
    } else {
        $last_crumb = $datetime->id == NULL ? 'Add a Location, Date, and Time' : 'Edit Location, Date, and Time'; 
    }

    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        'Edit "' . $event->title . '"' => $event->getEditURL($context->calendar),
        $last_crumb => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h2 class="wdn-brand">
<?php echo $last_crumb ?>
</h2>
<form id="add-datetime-form" action="" method="POST">
  <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
  <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <fieldset class="dcf-b-0">
        <label class="dcf-label" for="location"><span class="dcf-required">*</span> Location</label>
        <select class="dcf-input-select" id="location" name="location" style="width: 100%;">
              <?php if ($datetime->id != NULL): ?>
              <optgroup label="Current location">
                  <option selected="selected" value="<?php echo $datetime->location_id ?>"><?php echo $datetime->getLocation()->name; ?></option>
              <?php endif; ?>
              <optgroup label="Your saved locations">
                  <?php foreach ($context->getUserLocations() as $location): ?>
                      <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                  <?php endforeach; ?>
                  <option value="new">-- New Location --</option>
              </optgroup>
              <optgroup label="UNL Campus locations">
                  <?php foreach ($context->getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_MAIN) as $location): ?>
                      <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                  <?php endforeach; ?>
              </optgroup>
              <optgroup label="Extension locations">
                  <?php foreach ($context->getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_EXTENSION) as $location): ?>
                      <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                  <?php endforeach; ?>
              </optgroup>
        </select>

        <div id="new-location-fields" style="display: none;">
            <h6>New Location</h6>
            <label class="dcf-label" for="location-name"><span class="required">*</span> Name</label>
            <input class="dcf-input-text" type="text" id="location-name" name="new_location[name]">

            <label class="dcf-label" for="location-address-1">Address</label>
            <input class="dcf-input-text" type="text" id="location-address-1" name="new_location[streetaddress1]">

            <label class="dcf-label" for="location-address-2">Address 2</label>
            <input class="dcf-input-text" type="text" id="location-address-2" name="new_location[streetaddress2]">

            <label class="dcf-label" for="location-room">Room</label>
            <input class="dcf-input-text" type="text" id="location-room" name="new_location[room]">

            <label class="dcf-label" for="location-city">City</label>
            <input class="dcf-input-text" type="text" id="location-city" name="new_location[city]">

            <label class="dcf-label" for="location-state">State</label>
            <input class="dcf-input-text" type="text" id="location-state" name="new_location[state]">

            <label class="dcf-label" for="location-zip">Zip</label>
            <input class="dcf-input-text" type="text" id="location-zip" name="new_location[zip]">

            <label class="dcf-label" for="location-map-url">Map URL</label>
            <input class="dcf-input-text" type="text" id="location-map-url" name="new_location[mapurl]">

            <label class="dcf-label" for="location-webpage">Webpage</label>
            <input class="dcf-input-text" type="text" id="location-webpage" name="new_location[webpageurl]">

            <label class="dcf-label" for="location-hours">Hours</label>
            <input class="dcf-input-text" type="text" id="location-hours" name="new_location[hours]">

            <label class="dcf-label" for="location-directions">Directions</label>
            <textarea class="dcf-input-text" id="location-directions" name="new_location[directions]"></textarea>

            <label class="dcf-label" for="location-additional-public-info">Additional Public Info</label>
            <input class="dcf-input-text" type="text" id="location-additional-public-info" name="new_location[additionalpublicinfo]">

            <label class="dcf-label" for="location-type">Type</label>
            <input class="dcf-input-text" type="text" id="location-type" name="new_location[type]">

            <label class="dcf-label" for="location-phone">Phone</label>
            <input class="dcf-input-text" type="text" id="location-phone" name="new_location[phone]">

            <input class="dcf-input-control" type="checkbox" id="location-save" name="location_save">
            <label class="dcf-label" for="location-save">Save this location for future events</label>
        </div>

        <label class="dcf-label" for="room">Room</label>
        <input class="dcf-input-text" type="text" id="room" name="room" value="<?php echo $datetime->room; ?>" />

        <label class="dcf-label dcf-mt-2" for="timezone"><span class="dcf-required">*</span> Timezone</label>
        <select class="dcf-input-select" id="timezone"" name="timezone" aria-label="Timezone">
          <?php
          $timezone = $calendar->defaulttimezone;
          if (!empty($datetime->timezone)) {
              $timezone = $datetime->timezone;
          };
          foreach (UNL\UCBCN::getTimezoneOptions() as $tzName => $tzValue) { ?>
            <option <?php if ($timezone == $tzValue) echo 'selected="selected"'; ?> value="<?php echo $tzValue ?>"><?php echo $tzName ?></option>
          <?php } ?>
        </select>

        <label class="dcf-label" for="start-date" ><span class="dcf-required">*</span> Start Date &amp; Time</label>
        <div class="date-time-select">
          <div class="date-group dcf-flex-grow-1 dcf-datepicker">
            <input id="start-date"  value="<?php echo $start_date; ?>" name="start_date" aria-label="Start Date in the format of mm/dd/yyyy" type="text" class="" autocomplete="off" />
          </div>

          <div class="time-group dcf-d-flex dcf-ai-center dcf-flex-grow-1 dcf-mb-4">

            <span class="dcf-pl-2 dcf-pr-2">@</span>

            <select class="dcf-flex-grow-1 dcf-input-select"  id="start-time-hour" name="start_time_hour" aria-label="Start time hour">
              <option value="">Hour</option>
              <?php for ($i = 1; $i <= 12; $i++) { ?>
                <option <?php if ($i == $start_hour) echo 'selected="selected"'; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
              <?php } ?>
            </select> :

            <select class="dcf-flex-grow-1 dcf-input-select" id="start-time-minute" name="start_time_minute" aria-label="End time minute">
              <option value="">Minute</option>
              <?php for ($i = 0; $i < 60; $i+=5): ?>
                <option <?php if ($i == $start_minute) echo 'selected="selected"'; ?> value="<?php echo $i; ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
              <?php endfor; ?>
            </select>

            <fieldset id="start-time-am-pm" class="am_pm dcf-mb-0 dcf-pl-3 dcf-b-0">
              <legend class="dcf-sr-only">AM/PM</legend>
              <div class="dcf-d-flex dcf-ai-center">
                <label class="dcf-label dcf-2nd dcf-mt-0" for="start-time-am-pm-am">AM</label>
                <input <?php if ($start_am_pm == 'am') echo 'checked="checked"'; ?> class="dcf-input-control dcf-flex-shrink-0" id="start-time-am-pm-am" title="AM" type="radio" value="am" name="start_time_am_pm">
              </div>
              <div class="dcf-d-flex dcf-ai-center">
                <label class="dcf-label dcf-2nd dcf-mt-0" for="start-time-am-pm-pm">PM</label>
                <input <?php if ($start_am_pm == 'pm') echo 'checked="checked"'; ?> class="dcf-input-control dcf-flex-shrink-0" id="start-time-am-pm-pm" type="radio" value="pm" name="start_time_am_pm">
              </div>
            </fieldset>
          </div>
        </div>

        <label class="dcf-label" for="end-date">End Date &amp; Time (Optional)</label>
        <div class="date-time-select">
          <div class="date-group dcf-flex-grow-1 dcf-datepicker">
            <input id="end-date"  value="<?php echo $end_date; ?>" name="end_date" aria-label="End Date in the format of mm/dd/yyyy" type="text" autocomplete="off" />
          </div>

          <div class="time-group dcf-d-flex dcf-ai-center dcf-flex-grow-1 dcf-mb-4">

            <span class="dcf-pl-2 dcf-pr-2">@</span>

            <select class="dcf-flex-grow-1 dcf-input-select" id="end-time-hour" name="end_time_hour" aria-label="End time hour">
                <option value="">Hour</option>
                <?php for ($i = 1; $i <= 12; $i++) { ?>
                  <option <?php if ($i == $end_hour) echo 'selected="selected"'; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                <?php } ?>
            </select> :

            <select class="dcf-flex-grow-1 dcf-input-select" id="end-time-minute" name="end_time_minute" aria-label="End time minute">
                <option value="">Minute</option>
                <?php for ($i = 0; $i < 60; $i+=5): ?>
                    <option <?php if ($i == $end_minute) echo 'selected="selected"'; ?> value="<?php echo $i; ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                <?php endfor; ?>
            </select>

            <fieldset id="start-time-am-pm" class="am_pm dcf-mb-0 dcf-pl-3 dcf-b-0">
              <legend class="dcf-sr-only">AM/PM</legend>
              <div class="dcf-d-flex dcf-ai-center">
                <label class="dcf-label dcf-2nd dcf-mt-0" for="end-time-am-pm-am">AM</label>
                <input <?php if ($end_am_pm == 'am') echo 'checked="checked"'; ?> class="dcf-input-control dcf-flex-shrink-0" id="end-time-am-pm-am" type="radio" value="am" name="end_time_am_pm">
              </div>
              <div class="dcf-d-flex dcf-ai-center">
                <label class="dcf-label dcf-2nd dcf-mt-0" for="end-time-am-pm-pm">PM</label>
                <input <?php if ($end_am_pm == 'pm') echo 'checked="checked"'; ?> class="dcf-input-control dcf-flex-shrink-0" id="end-time-am-pm-pm" type="radio" value="pm" name="end_time_am_pm">
              </div>
            </fieldset>
          </div>
        </div>

        <?php if ($context->recurrence_id == NULL) : ?>
            <div class="section-container">
                <input <?php if ($datetime->recurringtype != 'none' && $datetime->recurringtype != NULL) echo 'checked="checked"' ?> type="checkbox" name="recurring" id="recurring">
                <?php $recurringType = (empty($datetime->recurringtype) || strtolower($datetime->recurringtype) == 'none') ? '' : $datetime->recurringtype; ?>
                <label class="dcf-label" for="recurring">This is a recurring <?php echo $recurringType; ?> event</label>
                <div class="recurring-container date-time-select">                        
                    <label class="dcf-label dcf-d-inline-block" for="recurring-type">This event recurs </label>
                    <select class="dcf-input-select" id="recurring-type" name="recurring_type">
                      <option value="daily" <?php if($datetime->recurringtype == "daily") { echo 'selected="selected"'; } ?>>Daily</option>
                      <option value="weekly" <?php if($datetime->recurringtype == "weekly") { echo 'selected="selected"'; } ?>>Weekly</option>
                      <option value="biweekly" <?php if($datetime->recurringtype == "biweekly") { echo 'selected="selected"'; } ?>>Biweekly</option>
                      <optgroup label="Monthly" id="monthly-group">
                      </optgroup>
                      <option value="annually" <?php if($datetime->recurringtype == "biweekly") { echo 'selected="selected"'; } ?>>Yearly</option>
                    </select>
                    <div class="dcf-datepicker">
                        <label class="dcf-label dcf-d-inline-block" for="recurs-until-date">until </label><br>
                        <input value="<?php if ($datetime->recurringtype != 'none' && $datetime->recurringtype != NULL) { echo $recurs_until_date; } ?>" id="recurs-until-date" name="recurs_until_date" type="text" aria-label="Until date in the format of mm/dd/yyyy" autocomplete="off" />
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <label class="dcf-label" for="directions">Directions</label>
        <textarea class="dcf-input-text" id="directions" name="directions"><?php echo $datetime->directions; ?></textarea>

        <label class="dcf-label" for="additional-public-info">Additional Public Info</label>
        <textarea class="dcf-input-text" id="additional-public-info" name="additional_public_info"><?php echo $datetime->additionalpublicinfo; ?></textarea>
    </fieldset>

    <button class="dcf-btn dcf-btn-primary dcf-float-left" type="submit">Submit</button>
</form>
<br>

<?php
$recurringCode = '';
$rectypemonth = '';
if ($datetime->recurringtype != 'none' && $datetime->recurringtype != NULL) {
  if ($datetime->recurringtype == 'monthly') {
      $rectypemonth = $datetime->rectypemonth;
  }
  $recurringCode = "setRecurringOptions($('#start-date'), $('#monthly-group'), '" . $rectypemonth. "');";
}
$page->addScriptDeclaration("
require(['jquery'], function ($) {

    // DCF Date Picker
    WDN.initializePlugin('datepickers');

    $('#location').change(function(change) {
        if ($('#location').val() == 'new') {
            $('#new-location-fields').show();
        } else {
            $('#new-location-fields').hide();
        }
    });

    $('#location').change();

    $('#recurring').change(function () {
        if (this.checked) {
            setRecurringOptions($('#start-date'), $('#monthly-group'), '" . $recurringType . "');
        }
    });

    $('#start-date').change(function (change) {
        setRecurringOptions($(this), $('#monthly-group'), '" . $rectypemonth. "');
    });

    " . $recurringCode . "

    $('#add-datetime-form').submit(function (submit) {
        errors = [];

        // validate required fields
        if ($('#location').val() == '' || $('#start-date').val() == '') {
            if ($('#location').val() == '') {
                notifier.mark_input_invalid($('#location'));
            }
            if ($('#start-date').val() == '') {
                notifier.mark_input_invalid($('#start-date'));
            }
            errors.push('<a href=\"#location\">Location</a> and <a href=\"#start-date\">start date</a> are required.');
        }

        var start = new Date($('#start-date').val());
        if ($('#start-date').val() != '') {
            // validate end date is after start date and the time is afterward accordingly
            if ($('#end-date').val() != '') {
                var end = new Date($('#end-date').val());

                // translate times from inputs. Blank hour = 12, blank minute = 0, blank am/pm = am
                var start_am_pm = $('#start-time-am-pm-pm').is(':checked') ? 'pm' : 'am';
                var start_hour = $('#start-time-hour').val() != '' ? parseInt($('#start-time-hour').val()) % 12 : 0;
                start_hour = start_am_pm == 'pm' ? start_hour + 12 : start_hour;
                var start_minute = $('#start-time-minute').val() != '' ? parseInt($('#start-time-minute').val()) : 0;
                start.setHours(start_hour);
                start.setMinutes(start_minute);

                var end_am_pm = $('#end-time-am-pm-pm').is(':checked') ? 'pm' : 'am';
                var end_hour = $('#end-time-hour').val() != '' ? parseInt($('#end-time-hour').val()) % 12 : 0;
                end_hour = end_am_pm == 'pm' ? end_hour + 12 : end_hour;
                var end_minute = $('#end-time-minute').val() != '' ? parseInt($('#end-time-minute').val()) : 0;
                end.setHours(end_hour);
                end.setMinutes(end_minute);

                if (start > end) {
                    notifier.mark_input_invalid($('#end-date'));
                    errors.push('Your <a href=\"#end-date\">end date/time</a> must be on or after the <a href=\"#start-date\">start date/time</a>.');
                }
            }
        }

        // if recurring is checked, there must be a recurring type and the recurs_until date must be on
        // or after the start date
        if ($('#start-date').val() != '') {
            if ($('#recurring').is(':checked')) {
                if ($('#recurring-type').val() == '' || $('#recurs-until-date').val() == '') {
                    if ($('#recurring-type').val() == '') {
                        notifier.mark_input_invalid($('#recurring-type'));
                    }
                    if ($('#recurs-until-date').val() == '') {
                        notifier.mark_input_invalid($('#recurs-until-date'));
                    }
                    errors.push('Recurring events require a <a href=\"#recurring-type\">recurring type</a> and <a href=\"#recurs-until-date\">date</a> that they recur until.');
                }

                // check that the recurs until date is on or after the start date
                start.setHours(0);
                start.setMinutes(0);
                var until = new Date($('#recurs-until-date').val());

                if (start > until) {
                    notifier.mark_input_invalid($('#recurs-until-date'));
                    errors.push('The <a href=\"#recurs-until-date\">\"recurs until date\"</a> must be on or after the start date.');
                }
            }
        }

        // new locations must have a name
        if ($('#location').val() == 'new' && $('#location-name').val() == '') {
            notifier.mark_input_invalid($('#location-name'));
            errors.push('You must give your new location a <a href=\"#location-name\">name</a>.');
        }

        if (errors.length > 0) {
            submit.preventDefault();
            notifier.alert('Sorry! We couldn\'t create your event', '<ul><li>' + errors.join('</li><li>') + '</li></ul>');
        }
    });
});");

?>
