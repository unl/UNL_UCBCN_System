<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Location as Location;
use UNL\UCBCN\Locations;

class LocationUtility
{
	public static function addLocation(array $post_data, $user)
	{
		$allowed_fields = array(
			'name',
			'streetaddress1',
			'streetaddress2',
			'room',
			'city',
			'state',
			'zip',
			'mapurl',
			'webpageurl',
			'hours',
			'directions',
			'additionalpublicinfo',
			'type',
			'phone',
		);

		$location = new Location;

		foreach ($allowed_fields as $field) {
			$value = $post_data['new_location'][$field];
			if (!empty($value)) {
				$location->$field = $value;
			}
		}

		if (array_key_exists('location_save', $post_data) && $post_data['location_save'] == 'on') {
			$location->user_id = $user->uid;
		}
		$location->standard = 0;

		$location->insert();

		return $location;
	}

	public static function getUserLocations()
	{
		$user = Auth::getCurrentUser();
		return new Locations(array('user_id' => $user->uid));
	}

	public static function getStandardLocations($display_order)
	{
		return new Locations(array(
			'standard' => true,
			'display_order' => $display_order,
		));
	}
}
