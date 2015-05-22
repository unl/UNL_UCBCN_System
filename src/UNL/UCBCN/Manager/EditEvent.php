<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Calendar\EventTypes;
use UNL\UCBCN\Location;
use UNL\UCBCN\Locations;
use UNL\UCBCN\Event;
use UNL\UCBCN\Event\EventType;
use UNL\UCBCN\Event\Occurrence;
use UNL\UCBCN\User;

class EditEvent implements PostHandlerInterface
{
    public $options = array();
    public $calendar;
    public $event;
    public $on_main_calendar;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);
        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $this->event = Event::getByID($this->options['event_id']);
        if ($this->event === FALSE) {
            throw new \Exception("That event could not be found.", 404);
        }

        $main_calendar = CalendarModel::getByID(Controller::$default_calendar_id);
        $this->on_main_calendar = $this->event->getStatusWithCalendar($main_calendar);
    }

    public function handlePost(array $get, array $post, array $files)
    {
        $this->updateEvent($_POST);
        return $this->calendar->getManageURL();
    }

    public function getEventTypes()
    {
        return new EventTypes(array());
    }

    public function getLocations()
    {
        $user = Auth::getCurrentUser();
        return new Locations(array('user_id' => $user->uid));
    }

    private function updateEvent($post_data) 
    {
        $this->event->title = $post_data['title'];
        $this->event->subtitle = $post_data['subtitle'];
        $this->event->description = $post_data['description'];

        $this->event->listingcontactname = $post_data['contact_name'];
        $this->event->listingcontactphone = $post_data['contact_phone'];
        $this->event->listingcontactemail = $post_data['contact_email'];

        $this->event->webpageurl = $post_data['website'];
        $this->event->approvedforcirculation = $post_data['private_public'] == 'public' ? 1 : 0;
        $result = $this->event->update();

        # update the event type record
        $event_has_type = EventType::getByEvent_ID($this->event->id);
        $event_has_type->eventtype_id = $post_data['type'];
        $event_has_type->update();

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