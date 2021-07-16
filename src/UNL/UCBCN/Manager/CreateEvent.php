<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Manager\EventForm as EventForm;
use UNL\UCBCN as BaseUCBCN;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\EventType;
use UNL\UCBCN\Event\Occurrence;

class CreateEvent extends EventForm
{
    public function __construct($options = array()) 
    {
        parent::__construct($options);
        $this->mode = self::MODE_CREATE;
        $this->event = new Event;
    }

    public function handlePost(array $get, array $post, array $files)
    {
        try {
            $new_event = $this->createEvent($post, $files);
        } catch (ValidationException $e) {
            $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Sorry! We couldn\'t create your event', $e->getMessage());
            throw $e;
        }
        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Event Created', 'Your event "' . $new_event->title . '" has been created.');

        # redirect
        return $this->calendar->getManageURL(TRUE);
    }

    private function validateEventData($post_data, $files) 
    {
        # title, start date, location are required
        if (empty($post_data['title']) || empty($post_data['location']) || empty($post_data['start_date'])) {
            throw new ValidationException('<a href="#title">Title</a>, <a href="#location">location</a>, and <a href="#start-date">start date</a> are required.');
        }

        # if we are sending this to UNL Main Calendar, description and contact info must be given
        if (array_key_exists('send_to_main', $post_data) && $post_data['send_to_main'] == 'on') {
            if (empty($post_data['description']) || empty($post_data['contact_name'])) {
                throw new ValidationException('<a href="#contact-name">Contact name</a> and <a href="#description">description</a> are required to recommend to UNL Main Calendar.');
            }
        }

        # timezone must be valid
        if (empty($post_data['timezone']) || !(in_array($post_data['timezone'], BaseUCBCN::getTimezoneOptions()))) {
            throw new ValidationException('The timezone is invalid.');
        }

        # end date must be after start date
        $start_date = $this->calculateDate($post_data['start_date'], 
            $post_data['start_time_hour'], $post_data['start_time_minute'], 
            $post_data['start_time_am_pm']);

        $end_date = $this->calculateDate($post_data['end_date'], 
            $post_data['end_time_hour'], $post_data['end_time_minute'], 
            $post_data['end_time_am_pm']);

        if ($start_date > $end_date) {
            throw new ValidationException('Your <a href="#end-date">end date/time</a> must be on or after the <a href="#start-date">start date/time</a>.');
        }

        # check that recurring events have recurring type and correct recurs until date
        if (array_key_exists('recurring', $post_data) && $post_data['recurring'] == 'on') {
            if (empty($post_data['recurring_type']) || empty($post_data['recurs_until_date'])) {
                throw new ValidationException('Recurring events require a <a href="#recurring-type">recurring type</a> and <a href="#recurs-until-date">date</a> that they recur until.');
            }

            $recurs_until = $this->calculateDate($post_data['recurs_until_date'], 11, 59, 'PM');
            if ($start_date > $recurs_until) {
                throw new ValidationException('The <a href="#recurs-until-date">"recurs until date"</a> must be on or after the start date.');
            }
        }

        # check that a new location has a name
        if ($post_data['location'] == 'new' && empty($post_data['new_location']['name'])) {
            throw new ValidationException('You must give your new location a <a href="#location-name">name</a>.');
        }

        # website must be a valid url
        if (!empty($post_data['website']) && !filter_var($post_data['website'], FILTER_VALIDATE_URL)) {
          throw new ValidationException('Event Website must be a valid URL.');
        }

	    # send to main is required
	    if (empty($post_data['send_to_main'])) {
		    throw new ValidationException('<a href="send_to_main">Consider for main calendar</a> is required.');
	    }

        # Validate Image
        $this->validateEventImage($post_data, $files);
    }

    private function calculateDate($date, $hour, $minute, $am_or_pm)
    {
        # defaults if NULL is passed in
        $hour = $hour == NULL ? 12 : $hour;
        $minute = $minute == NULL ? 0 : $minute;
        $am_or_pm = $am_or_pm == NULL ? 'am' : $am_or_pm;

        $date = strtotime($date . ' ' . $hour . ':' . $minute . ':00 ' . $am_or_pm);
        return date('Y-m-d H:i:s', $date);
    }

    private function createEvent($post_data, $files) 
    {
        $user = Auth::getCurrentUser();

        # tricky: if end date is empty, we want that to be the same as the start date
        # if the end time is also empty, then be sure to set the am/pm appropriately
        if (empty($post_data['end_date'])) {
            $post_data['end_date'] = $post_data['start_date'];
        }
        if (empty($post_data['end_time_hour']) && empty($post_data['end_time_minute'])) {
            $post_data['end_time_hour'] = $post_data['start_time_hour'];
            $post_data['end_time_minute'] = $post_data['start_time_minute'];
            $post_data['end_time_am_pm'] = $post_data['start_time_am_pm'];
        }

        # by setting and then validating, we allow the event on the form to have the entered data
        # so if the validation fails, the form shows with the entered data
        $this->setEventData($post_data, $files);
        $this->validateEventData($post_data, $files);

        $result = $this->event->insert($this->calendar, 'create event form');

        # add the event type record
        $event_has_type = new EventType;
        $event_has_type->event_id = $this->event->id;
        $event_has_type->eventtype_id = $post_data['type'];

        $event_has_type->insert();

        # add the event date time record
        $event_datetime = new Occurrence;
        $event_datetime->event_id = $this->event->id;

        # check if this is to use a new location
        if ($post_data['location'] == 'new') {
            # create a new location
	        $location = LocationUtility::addLocation($post_data, $user);
            $event_datetime->location_id = $location->id;
        } else {
            $event_datetime->location_id = $post_data['location'];
        }

        # set the start date and end date
        $event_datetime->starttime = $this->calculateDate($post_data['start_date'], 
            $post_data['start_time_hour'], $post_data['start_time_minute'], 
            $post_data['start_time_am_pm']);

        $event_datetime->endtime = $this->calculateDate($post_data['end_date'], 
            $post_data['end_time_hour'], $post_data['end_time_minute'], 
            $post_data['end_time_am_pm']);

        if (array_key_exists('recurring', $post_data) && $post_data['recurring'] == 'on') {
            $event_datetime->recurringtype = $post_data['recurring_type'];
            $event_datetime->recurs_until = $this->calculateDate(
                $post_data['recurs_until_date'], 11, 59, 'PM');
            if ($event_datetime->recurringtype == 'date' || 
                $event_datetime->recurringtype == 'lastday' || 
                $event_datetime->recurringtype == 'first' ||
                $event_datetime->recurringtype == 'second' ||
                $event_datetime->recurringtype == 'third'|| 
                $event_datetime->recurringtype == 'fourth' ||
                $event_datetime->recurringtype == 'last') {
                    $event_datetime->rectypemonth = $event_datetime->recurringtype;
                    $event_datetime->recurringtype = 'monthly';
            }
        } else {
            $event_datetime->recurringtype = 'none';
        }
        $event_datetime->timezone = $post_data['timezone'];
        $event_datetime->room = $post_data['room'];
        $event_datetime->directions = $post_data['directions'];
        $event_datetime->additionalpublicinfo = $post_data['additional_public_info'];

        $event_datetime->insert();

        if (array_key_exists('send_to_main', $post_data) && $post_data['send_to_main'] == 'on') {
            $this->event->considerForMainCalendar();
        }

        return $this->event;
    }
}
