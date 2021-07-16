<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Manager\EventForm as EventForm;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\EventType;
use UNL\UCBCN\Calendar as CalendarModel;

class EditEvent extends EventForm
{
    public $on_main_calendar;
    public $page;

    public function __construct($options = array()) 
    {
        parent::__construct($options);

	    $this->mode = self::MODE_UPDATE;
        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }

        if (!$this->event->userCanEdit()) {
            throw new \Exception("You do not have permission to edit this event.", 403);
        }

        if (array_key_exists('page', $_GET) && is_numeric($_GET['page']) && $_GET['page'] >= 1) {
            $this->page = $_GET['page'];
        } else {
            $this->page = 1;
        }

        $main_calendar = CalendarModel::getByID(Controller::$default_calendar_id);
        $this->on_main_calendar = $this->event->getStatusWithCalendar($main_calendar);
    }

    public function handlePost(array $get, array $post, array $files)
    {
        try {
            $this->updateEvent($post, $files);
        } catch (ValidationException $e) {
            $this->flashNotice(parent::NOTICE_LEVEL_ALERT, 'Sorry! We couldn\'t edit your event', $e->getMessage());
            throw $e;
        }

        $this->flashNotice(parent::NOTICE_LEVEL_SUCCESS, 'Event Updated', 'The event "' . $this->event->title . '" has been updated.');
        return $this->event->getEditURL($this->calendar);
    }

    private function validateEventData($post_data, $files)
    {
        # title required
        if (empty($post_data['title'])) {
            throw new ValidationException('<a href="#title">Title</a> is required.');
        }

        # if we are sending this to UNL Main Calendar, description and contact info must be given
        if (!$this->on_main_calendar) {
            if (array_key_exists('send_to_main', $post_data) && $post_data['send_to_main'] == 'on') {
                if (empty($post_data['description']) || empty($post_data['contact_name'])) {
                    throw new ValidationException('<a href="#contact-name">Contact name</a>, <a href="#description">description</a> and <a href="#imagedata">image</a> are required to recommend to UNL Main Calendar.');
                }
            }
        }

        # website must be a valid url
        if (!empty($post_data['website']) && !filter_var($post_data['website'], FILTER_VALIDATE_URL)) {
          throw new ValidationException('Event Website must be a valid URL.');
        }

	    // Validate Image
	    $this->validateEventImage($post_data, $files);
    }

    private function updateEvent($post_data, $files)
    {
        $this->setEventData($post_data, $files);
        $this->validateEventData($post_data, $files);
        $result = $this->event->update();

        # update the event type record
        $event_has_type = EventType::getByEvent_ID($this->event->id);

        if ($event_has_type !== FALSE) {
            $event_has_type->eventtype_id = $post_data['type'];
            $event_has_type->update();
        } else {
            # create the type
            $event_has_type = new EventType;
            $event_has_type->event_id = $this->event->id;
            $event_has_type->eventtype_id = $post_data['type'];

            $event_has_type->insert();
        }

        # send to main calendar if selected and not already on main calendar
        # and box is checked
        if (!$this->on_main_calendar) {
            if (array_key_exists('send_to_main', $post_data) && $post_data['send_to_main'] == 'on') {
                $this->event->considerForMainCalendar();
            }
        }

        return $result;
    }
}
