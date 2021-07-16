<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar as CalendarModel;
use UNL\UCBCN\Calendar\EventTypes;
use UNL\UCBCN\Permission;

class EventForm extends PostHandler
{
	const MODE_CREATE = 'MODE_CREATE';
	const MODE_UPDATE = 'MODE_UPDATE';

	public $options = array();
	public $calendar;
	public $event;
	public $post;
	public $mode;

	public function __construct($options = array())
	{
		$this->options = $options + $this->options;
		$this->calendar = CalendarModel::getByShortName($this->options['calendar_shortname']);

		if ($this->calendar === FALSE) {
			throw new EventFormException("That calendar could not be found.", 404);
		}

		$user = Auth::getCurrentUser();
		if (!$user->hasPermission(Permission::EVENT_CREATE_ID, $this->calendar->id)) {
			throw new EventFormException("You do not have permission to create an event on this calendar.", 403);
		}
	}

	public function getEventTypes()
	{
		return new EventTypes(array());
	}

	protected function setEventData($post_data)
	{
		$this->event->title = empty($post_data['title']) ? NULL : $post_data['title'];
		$this->event->subtitle = empty($post_data['subtitle']) ? NULL : $post_data['subtitle'];
		$this->event->description = empty($post_data['description']) ? NULL : $post_data['description'];

		$this->event->listingcontactname = empty($post_data['contact_name']) ? NULL : $post_data['contact_name'];
		$this->event->listingcontactphone = empty($post_data['contact_phone']) ? NULL : $post_data['contact_phone'];
		$this->event->listingcontactemail = empty($post_data['contact_email']) ? NULL : $post_data['contact_email'];

		$this->event->webpageurl = empty($post_data['website']) ? NULL : $post_data['website'];
		$this->event->approvedforcirculation = !empty($post_data['private_public']) && $post_data['private_public'] == 'private' ? 0 : 1;

		# for extraneous data aside from the event (location, type, etc)
		$this->post = $post_data;
	}

	protected function validateEventImage($post_data, $files) {
		if (!empty($post_data['cropped_image_data'])) {
			$this->setCroppedImage($post_data);
		} else if ($this->mode === self::MODE_UPDATE && array_key_exists('remove_image', $post_data) && $post_data['remove_image'] == 'on') {
			$this->removeImage($post_data);
		} else if (isset($files['imagedata']) && is_uploaded_file($files['imagedata']['tmp_name'])) {
			$this->setUploadImage();
		} else if (isset($files['imagedata']) && $files['imagedata']['error'] == UPLOAD_ERR_INI_SIZE) {
			throw new ValidationException('Your image file size was too large. It must be 2 MB or less. Try a tool like <a target="_blank" href="http://www.imageoptimizer.net">Image Optimizer</a>.');
		} else if ($this->mode === self::MODE_CREATE && $post_data['send_to_main'] === 'on') {
			throw new ValidationException('A image is required for events considered for main UNL Calendar.');
		} else if ($this->mode === self::MODE_UPDATE && empty($this->event->imagedata) && ($this->on_main_calendar || isset($post_data['send_to_main']))) {
			throw new ValidationException('A image is required for events considered for main UNL Calendar.');
		}
	}

	private function setCroppedImage($post_data) {
		$image_parts = explode(";base64,", $post_data['cropped_image_data']);
		$image_type_aux = explode("image/", $image_parts[0]);
		$image_type = $image_type_aux[1];
		$image_base64 = base64_decode($image_parts[1]);
		$this->event->imagemime = $image_type;
		$this->event->imagedata = $image_base64;
	}

	private function removeImage($post_data) {
		if ($this->on_main_calendar || isset($post_data['send_to_main'])) {
			throw new ValidationException('Image can not be removed. Image is required for events considered for main UNL Calendar.');
		} else {
			$this->event->imagemime = NULL;
			$this->event->imagedata = NULL;
		}
	}

	private function setUploadImage() {
		$uploadFile = new FileUpload('imagedata', FileUpload::TYPE_IMAGE);
		if ($uploadFile->isValid()) {
			$uploadFile->compressImage();
			$this->event->imagemime = $uploadFile->getType();
			$this->event->imagedata = file_get_contents($uploadFile->getPath());
		} else {
			$message = 'Your uploaded image has error(s): <ul>';
			foreach($uploadFile->getValidationErrors() as $error) {
				$message .= '<li>' . $error . '</li>';
			}
			$message .= '</ul>';
			throw new ValidationException($message);
		}
	}
}
