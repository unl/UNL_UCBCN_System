<?php
    $calendar = $context->calendar;
    $event = $context->event;
    $post = $context->post;
?>
<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        "Create Event" => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<div>
    <form id="create-event-form" action="" method="POST" enctype="multipart/form-data" class="dcf-pt-0">
      <div class="dcf-grid dcf-col-gap-vw">
        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
        <div class="dcf-col-100% dcf-col-67%-start@md">
            <fieldset>
                <legend class="dcf-legend dcf-txt-md" style="margin-top: 0">Event Details</legend>
                <label class="dcf-label" for="title"><span class="dcf-required">*</span> Title</label>
                <input class="dcf-input-text" type="text" id="title" name="title" value="<?php echo $event->title; ?>" />

                <label class="dcf-label" for="subtitle">Subtitle</label>
                <input class="dcf-input-text" type="text" id="subtitle" name="subtitle" value="<?php echo $event->subtitle; ?>" />

                <label class="dcf-label" for="description"><span class="required-for-main-calendar dcf-required" style="display: none">* </span>Description</label>
                <textarea class="dcf-input-text" rows="4" id="description" name="description"><?php echo $event->description; ?></textarea>

                <label class="dcf-label" for="type">Type</label>
                <select class="dcf-input-select dcf-w-100%" id="type" name="type">
                    <?php foreach ($context->getEventTypes() as $type) { ?>
                      <option <?php if (isset($post['type']) && $post['type'] == $type->id) echo 'selected="selected"' ?> value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
                  <?php } ?>
                </select>
            </fieldset>

            <fieldset>
            <legend class="dcf-legend dcf-txt-md">Location, Date, and Time</legend>
                <label class="dcf-label" for="location"><span class="dcf-required">*</span> Location</label>
                <select class="dcf-input-select" id="location" name="location" cstyle="width: 100%;">
                  <optgroup label="Your saved locations">
                      <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getUserLocations() as $location): ?>
                        <option <?php if (isset($post['location']) && $post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                      <?php endforeach ?>
                    <option <?php if (isset($post['location']) && $post['location'] == 'new') echo 'selected="selected"' ?>value="new">-- New Location --</option>
                  </optgroup>
                  <optgroup label="UNL Campus locations">
                      <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_MAIN) as $location): ?>
                        <option <?php if (isset($post['location']) && $post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                      <?php endforeach ?>
                  </optgroup>
                  <optgroup label="Extension locations">
                      <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_EXTENSION) as $location): ?>
                        <option <?php if (isset($post['location']) && $post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                      <?php endforeach ?>
                  </optgroup>
                </select>

                <div id="new-location-fields" style="display: none;">
                    <h6>New Location</h6>
                    <label class="dcf-label" for="location-name"><span class="required">*</span> Name</label>
                    <input class="dcf-input-text" type="text" id="location-name" name="new_location[name]" value="<?php echo isset($post['location']['name']) ? $post['location']['name']: ''; ?>">

                    <label class="dcf-label" for="location-address-1">Address</label>
                    <input class="dcf-input-text" type="text" id="location-address-1" name="new_location[streetaddress1]" value="<?php echo isset($post['location']['streetaddress1']) ? $post['location']['streetaddress1']: ''; ?>">

                    <label class="dcf-label" for="location-address-2">Address 2</label>
                    <input class="dcf-input-text" type="text" id="location-address-2" name="new_location[streetaddress2]" value="<?php echo isset($post['location']['streetaddress2']) ? $post['location']['streetaddress2']: ''; ?>">

                    <label class="dcf-label" for="location-room">Room</label>
                    <input class="dcf-input-text" type="text" id="location-room" name="new_location[room]" value="<?php echo isset($post['location']['room']) ? $post['location']['room']: ''; ?>">

                    <label class="dcf-label" for="location-city">City</label>
                    <input class="dcf-input-text" type="text" id="location-city" name="new_location[city]" value="<?php echo isset($post['location']['city']) ? $post['location']['city']: ''; ?>">

                    <label class="dcf-label" for="location-state">State</label>
                    <input class="dcf-input-text" type="text" id="location-state" name="new_location[state]" value="<?php echo isset($post['location']['state']) ? $post['location']['state']: ''; ?>">

                    <label class="dcf-label" for="location-zip">Zip</label>
                    <input class="dcf-input-text" type="text" id="location-zip" name="new_location[zip]" value="<?php echo isset($post['location']['zip']) ? $post['location']['zip']: ''; ?>">

                    <label class="dcf-label" for="location-map-url">Map URL</label>
                    <input class="dcf-input-text" type="text" id="location-map-url" name="new_location[mapurl]" value="<?php echo isset($post['location']['mapurl']) ? $post['location']['mapurl']: ''; ?>">

                    <label class="dcf-label" for="location-webpage">Webpage</label>
                    <input class="dcf-input-text" type="text" id="location-webpage" name="new_location[webpageurl]" value="<?php echo isset($post['location']['webpageurl']) ? $post['location']['webpageurl']: ''; ?>">

                    <label class="dcf-label" for="location-hours">Hours</label>
                    <input class="dcf-input-text" type="text" id="location-hours" name="new_location[hours]" value="<?php echo isset($post['location']['hours']) ? $post['location']['hours']: ''; ?>">

                    <label class="dcf-label" for="location-directions">Directions</label>
                    <textarea class="dcf-input-text" id="location-directions" name="new_location[directions]"><?php echo isset($post['location']['directions']) ? $post['location']['directions']: ''; ?></textarea>

                    <label class="dcf-label" for="location-additional-public-info">Additional Public Info</label>
                    <input class="dcf-input-text" type="text" id="location-additional-public-info" name="new_location[additionalpublicinfo]" value="<?php echo isset($post['location']['additionalpublicinfo']) ? $post['location']['additionalpublicinfo']: ''; ?>">

                    <label class="dcf-label" for="location-type">Type</label>
                    <input class="dcf-input-text" type="text" id="location-type" name="new_location[type]" value="<?php echo isset($post['location']['type']) ?  $post['location']['type']: ''; ?>">

                    <label class="dcf-label" for="location-phone">Phone</label>
                    <input class="dcf-input-text" type="text" id="location-phone" name="new_location[phone]" value="<?php echo isset($post['location']['phone']) ? $post['location']['phone']: ''; ?>">

                    <input class="dcf-input-control" <?php if (isset($post['location_save']) && $post['location_save'] == 'on') echo 'checked="checked"'; ?> type="checkbox" id="location-save" name="location_save">
                    <label class="dcf-label" for="location-save">Save this location for future events</label>
                </div>

                <label class="dcf-label" for="room">Room</label>
                <input class="dcf-input-text"type="text" id="room" name="room" value="<?php if (isset($post['room'])) { echo $post['room']; } ?>" />

                <label class="dcf-label dcf-mt-2" for="timezone"><span class="dcf-required">*</span> Timezone</label>
                <select class="dcf-input-select" id="timezone"" name="timezone" aria-label="Timezone">
                <?php
                  $timezone = $calendar->defaulttimezone;
                  if (!empty($post['timezone'])) {
                      $timezone = $post['timezone'];
                  };
                  foreach (UNL\UCBCN::getTimezoneOptions() as $tzName => $tzValue) { ?>
                  <option <?php if ($timezone == $tzValue) echo 'selected="selected"'; ?> value="<?php echo $tzValue ?>"><?php echo $tzName ?></option>
                <?php } ?>
                </select>

                <label class="dcf-label" for="start-date" ><span class="dcf-required">*</span> Start Date &amp; Time</label>
                <div class="date-time-select">
                  <div class="date-group dcf-d-flex dcf-flex-grow-1 dcf-ai-center dcf-relative dcf-mb-4 dcf-pr-6">
                    <span class="dcf-absolute dcf-z-1 wdn-icon-calendar" aria-hidden="true"></span>
                    <input id="start-date" name="start_date" aria-label="Start Date in the format of mm/dd/yyyy" type="text" class="dcf-flex-grow-1 datepicker" value="<?php if (isset($post['start_date'])) { echo $post['start_date']; } ?>" autocomplete="off" />
                  </div>

                  <div class="time-group dcf-d-flex dcf-ai-center dcf-flex-grow-1 dcf-mb-4">

                    <span class="dcf-pr-2">@</span>

                    <select class="dcf-flex-grow-1 dcf-input-select" id="start-time-hour" name="start_time_hour" aria-label="Start Time Hour">
                        <option value="">Hour</option>
                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                        <option <?php if (isset($post['start_time_hour']) && $post['start_time_hour'] == $i) echo 'selected="selected"'; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php } ?>
                    </select> : 

                    <select class="dcf-flex-grow-1 dcf-input-select" id="start-time-minute" name="start_time_minute" aria-label="Start Time Minute">
                        <option value="">Minute</option>
                        <option <?php if (isset($post['start_time_minute']) && ($post['start_time_minute'] === 0 || $post['start_time_minute'] === '0')) echo 'selected="selected"'; ?> value="0">00</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 5) echo 'selected="selected"'; ?> value="5">05</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 10) echo 'selected="selected"'; ?> value="10">10</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 15) echo 'selected="selected"'; ?> value="15">15</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 20) echo 'selected="selected"'; ?> value="20">20</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 25) echo 'selected="selected"'; ?> value="25">25</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 30) echo 'selected="selected"'; ?> value="30">30</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 35) echo 'selected="selected"'; ?> value="35">35</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 40) echo 'selected="selected"'; ?> value="40">40</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 45) echo 'selected="selected"'; ?> value="45">45</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 50) echo 'selected="selected"'; ?> value="50">50</option>
                        <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 55) echo 'selected="selected"'; ?> value="55">55</option>
                    </select>

                    <fieldset id="start-time-am-pm" class="am_pm dcf-mb-0 dcf-pl-3 dcf-b-0">
                      <legend class="dcf-sr-only">AM/PM</legend>
                      <div class="dcf-d-flex dcf-ai-center">
                        <label class="dcf-label dcf-2nd dcf-mt-0" for="start-time-am-pm-am">AM</label>
                        <input <?php if (!isset($post) || $post['start_time_am_pm'] == 'am') echo 'checked="checked"'; ?> class="dcf-input-control dcf-flex-shrink-0" id="start-time-am-pm-am" title="AM" type="radio" value="am" name="start_time_am_pm">
                      </div>
                      <div class="dcf-d-flex dcf-ai-center">
                        <label class="dcf-label dcf-2nd dcf-mt-0" for="start-time-am-pm-pm">PM</label>
                        <input <?php if (isset($post['start_time_am_pm']) && $post['start_time_am_pm'] == 'pm') echo 'checked="checked"'; ?> class="dcf-input-control dcf-flex-shrink-0" id="start-time-am-pm-pm" type="radio" value="pm" name="start_time_am_pm">
                      </div>
                    </fieldset>
                  </div>
                </div>

                <label class="dcf-label" for="end-date">End Date &amp; Time (Optional)</label>
                <div class="date-time-select">
                  <div class="date-group dcf-d-flex dcf-flex-grow-1 dcf-ai-center dcf-relative dcf-mb-4 dcf-pr-6">
                    <span class="dcf-absolute dcf-z-1 wdn-icon-calendar" aria-hidden="true"></span>
                    <input id="end-date" name="end_date" aria-label="End Date in the format of mm/dd/yyyy" type="text" class="datepicker" value="<?php if (isset($post['end_date'])) { echo $post['end_date']; } ?>" autocomplete="off" />
                  </div>

                  <div class="time-group dcf-d-flex dcf-ai-center dcf-flex-grow-1 dcf-mb-4">

                    <span class="dcf-pr-2">@</span>

                    <select class="dcf-flex-grow-1 dcf-input-select" id="end-time-hour" name="end_time_hour" aria-label="End Time Hour">
                        <option value="">Hour</option>
                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                        <option <?php if (isset($post['end_time_hour']) && $post['end_time_hour'] == $i) echo 'selected="selected"'; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php } ?>
                    </select> :

                    <select class="dcf-flex-grow-1 dcf-input-select" id="end-time-minute" name="end_time_minute" aria-label="End Time Minute">
                        <option value="">Minute</option>
                        <option <?php if (isset($post['end_time_minute']) && ($post['end_time_minute'] === 0 || $post['end_time_minute'] === '0')) echo 'selected="selected"'; ?> value="0">00</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 5) echo 'selected="selected"'; ?> value="5">05</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 10) echo 'selected="selected"'; ?> value="10">10</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 15) echo 'selected="selected"'; ?> value="15">15</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 20) echo 'selected="selected"'; ?> value="20">20</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 25) echo 'selected="selected"'; ?> value="25">25</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 30) echo 'selected="selected"'; ?> value="30">30</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 35) echo 'selected="selected"'; ?> value="35">35</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 40) echo 'selected="selected"'; ?> value="40">40</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 45) echo 'selected="selected"'; ?> value="45">45</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 50) echo 'selected="selected"'; ?> value="50">50</option>
                        <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 55) echo 'selected="selected"'; ?> value="55">55</option>
                    </select>

                    <fieldset id="end-time-am-pm" class="am_pm dcf-mb-0 dcf-pl-3 dcf-b-0">
                      <legend class="dcf-sr-only">AM/PM</legend>
                      <div class="dcf-d-flex dcf-ai-center">
                        <label class="dcf-label dcf-2nd dcf-mt-0" for="end-time-am-pm-am">AM</label>
                        <input <?php if (empty($post) || $post['end_time_am_pm'] == 'am') echo 'checked="checked"'; ?> class="dcf-input-control dcf-flex-shrink-0" id="end-time-am-pm-am" type="radio" value="am" name="end_time_am_pm">
                      </div>
                      <div class="dcf-d-flex dcf-ai-center">
                        <label class="dcf-label dcf-2nd dcf-mt-0" for="end-time-am-pm-pm">PM</label>
                        <input <?php if (isset($post['end_time_am_pm']) && $post['end_time_am_pm'] == 'pm') echo 'checked="checked"'; ?> class="dcf-input-control dcf-flex-shrink-0" id="end-time-am-pm-pm" type="radio" value="pm" name="end_time_am_pm">
                      </div>
                    </fieldset>
                  </div>
                </div>

                <div class="section-container">
                    <input class="dcf-input-control" <?php if (isset($post['recurring'])) echo 'checked="checked"'; ?> type="checkbox" name="recurring" id="recurring">
                    <label class="dcf-label" for="recurring">This is a recurring event</label>
                    <div class="recurring-container date-time-select">                        
                        <label class="dcf-label" for="recurring-type">This event recurs </label>
                        <select id="recurring-type" name="recurring_type">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="biweekly">Biweekly</option>
                            <optgroup label="Monthly" id="monthly-group">
                            </optgroup>
                            <option value="annually">Yearly</option>
                        </select>
                        <label class="dcf-label" for="recurs-until-date">until </label><br>
                        <span style="top: .4em" class="wdn-icon-calendar" aria-hidden="true"></span>
                        <input value="<?php if (isset($post['recurs_until_date'])) { echo $post['recurs_until_date']; } ?>" id="recurs-until-date" name="recurs_until_date" type="text" class="dcf-input-text datepicker" aria-label="until this date in the format of mm/dd/yyyy" autocomplete="off" />
                    </div>
                </div>

                <label class="dcf-label" for="directions">Directions</label>
                <textarea class="dcf-input-text" id="directions" name="directions"><?php if (isset($post['directions'])) { echo $post['directions']; } ?></textarea>

                <label class="dcf-label" for="additional-public-info">Additional Public Info</label>
                <textarea class="dcf-input-text" id="additional-public-info" name="additional_public_info"><?php if (isset($post['additional_public_info'])) { echo $post['additional_public_info']; } ?></textarea>
            </fieldset>

	        <?php echo $savvy->render($context, 'EventFormImageUpload.tpl.php'); ?>
        </div>

        <div class="dcf-col-100% dcf-col-33%-end@md">
            <fieldset class="visual-island dcf-b-0">
                <legend class="dcf-legend vi-header">Sharing</legend>
                <div class="details">
                    <fieldset>
                        <legend class="dcf-legend dcf-txt-sm">Privacy</legend>
                        <label class="dcf-label">
                            <input class="dcf-input-control" type="radio" value="private" name="private_public" id="sharing-private" <?php if (!empty($post['private_public']) && $post['private_public'] == 'private') echo 'checked="checked"'; ?>>
                            Private
                        </label> 
                        <br>
                        <label class="dcf-label">
                            <input class="dcf-input-control" type="radio" value="public" name="private_public" id="sharing-public" <?php if (!empty($post['private_public']) && $post['private_public'] != 'private') echo 'checked="checked"'; ?>>
                            Public
                        </label>
                    </fieldset>

                  <fieldset id="send_to_main">
                    <legend class="dcf-legend dcf-txt-sm"><span class="dcf-required">*</span> Consider for main UNL Calendar</legend>
                      <label class="dcf-label">
                        <input class="dcf-input-control" type="radio" id="send_to_main_on" name="send_to_main" value="on" <?php if (!empty($post['send_to_main']) && $post['send_to_main'] == 'on') echo 'checked="checked"'; ?>/>
                        Yes
                      </label>
                      <br>
                      <label class="dcf-label">
                        <input class="dcf-input-control" type="radio" id="send_to_main_off" name="send_to_main" value="off"  <?php if (!empty($post['send_to_main']) && $post['send_to_main'] == 'off') echo 'checked="checked"'; ?>/>
                        No
                      </label>
                  </fieldset>
                </div>
            </fieldset>

            <fieldset class="visual-island dcf-b-0">
                <legend class="dcf-legend vi-header">Contact Info</legend>

                <div class="details">
                    <label class="dcf-label" for="contact-name"><span class="required-for-main-calendar dcf-required" style="display: none">* </span>Name</label>
                    <input class="dcf-input-text" type="text" id="contact-name" name="contact_name" value="<?php if (isset($post['contact_name'])) { echo $post['contact_name']; } ?>" />

                    <label class="dcf-label" for="contact-phone">Phone</label>
                    <input class="dcf-input-text" type="text" id="contact-phone" name="contact_phone" value="<?php if (isset($post['contact_phone'])) { echo $post['contact_phone']; } ?>" />

                    <label class="dcf-label" for="contact-email">Email</label>
                    <input class="dcf-input-text" type="text" id="contact-email" name="contact_email" value="<?php if (isset($post['contact_email'])) { echo $post['contact_email']; } ?>" />

                    <label class="dcf-label" for="website">Event Website</label>
                    <input class="dcf-input-text" type="text" id="website" name="website" value="<?php echo $event->webpageurl ?>" />
                </div>
            </fieldset>
        </div>
        <div class="dcf-col-100%">
            <button class="dcf-btn dcf-btn-primary dcf-float-left" type="submit">Submit Event</button>
        </div>
      </div>
    </form>
</div>
<?php
$recurringType = !empty($post['recurring_type']) ? $post['recurring_type'] : NULL;
$page->addScriptDeclaration("
WDN.initializePlugin('jqueryui', [function() {  
    $ = require('jquery');
    $('.datepicker').datepicker();
    $(\"LINK[href^='//unlcms.unl.edu/wdn/templates_4.0/scripts/plugins/ui/css/jquery-ui.min.css']\").remove();

    $('#start-date').change(function (change) {
        setRecurringOptions($(this), $('#monthly-group'), '" . $recurringType . "');
    });

    setRecurringOptions($('#start-date'), $('#monthly-group'));
    $('#recurring-type').val(\"". $recurringType ."\");

    $('#location').change(function (change) {
        if ($(this).val() == 'new') {
            $('#new-location-fields').show();
        } else {
            $('#new-location-fields').hide();
        }
    });

    $('#location').change();
}]);

function isUrlValid(url) {
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}

require(['jquery'], function ($) {

    $('input[type=radio][name=send_to_main]').change(function() {
        if (this.value == 'on') {
            $('.required-for-main-calendar').show();
        }
        else if (this.value == 'off') {
            $('.required-for-main-calendar').hide();
        }
    });

    $('#create-event-form').submit(function (submit) {
        var errors = [];

        // validate required fields
        if ($('#title').val() == '' || $('#location').val() == '' || $('#start-date').val() == '') {
            if ($('#title').val() == '') {
                notifier.mark_input_invalid($('#title'));
            }
            if ($('#location').val() == '') {
                notifier.mark_input_invalid($('#location'));
            }
            if ($('#start-date').val() == '') {
                notifier.mark_input_invalid($('#start-date'));
            }
            errors.push('<a href=\"#title\">Title</a>, <a href=\"#location\">location</a>, and <a href=\"#start-date\">start date</a> are required.');
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

        // Must select whether to consider for main calendar
        if ($('input[name=\"send_to_main\"]:checked').val() === undefined) {
            notifier.mark_input_invalid($('#send_to_main_on'));
            errors.push('<a href=\"#send_to_main\">Consider for main calendar</a> is required.');
        } else if ($('input[name=\"send_to_main\"]:checked').val() === 'on') {
            if ($('#description').val().trim() == '') {
                notifier.mark_input_invalid($('#description'));
                errors.push('<a href=\"#description\">Description</a> is required when event is considered for main calendar.');
            }
            if ($('#contact-name').val().trim() == '') {
                notifier.mark_input_invalid($('#contact-name'));
                errors.push('<a href=\"#contact-name\">Contact Name</a> is required when event is considered for main calendar.');
            }
            if ($('#cropped-image-data').val().trim() == '' && $('#imagedata').val().trim() == '') {
                notifier.mark_input_invalid($('#imagedata'));
                errors.push('<a href=\"#imagedata\">Image</a> is required when event is considered for main calendar.');
            }
        }

        var websiteURL = $('#website').val();
        if (websiteURL != '' && !isUrlValid(websiteURL)) {
            notifier.mark_input_invalid($('#website'));
            errors.push('<a href=\"#website\">Event Website</a> is not a valid URL.');
        }

        if (errors.length > 0) {
            submit.preventDefault();
            notifier.alert('Sorry! We couldn\'t create your event', '<ul><li>' + errors.join('</li><li>') + '</li></ul>');
        }
    });
});");
?>
