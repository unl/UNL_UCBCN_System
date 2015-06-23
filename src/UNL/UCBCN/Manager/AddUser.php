<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\User;
use UNL\UCBCN\Calendar;
use UNL\UCBCN\Permission;
use UNL\UCBCN\Permissions;
use UNL\UCBCN\Manager\Controller;

class AddUser implements PostHandlerInterface
{
    public $options = array();
    public $calendar;
    public $user;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 404);
        }

        $user = Auth::getCurrentUser();
        if (!$user->hasPermission(Permission::CALENDAR_EDIT_PERMISSIONS_ID, $this->calendar->id)) {
            throw new \Exception("You do not have permission to edit user permissions on this calendar.", 403);
        }

        # check if we are looking to edit a user's permissions
        if (array_key_exists('user_uid', $this->options)) {
            $this->user = User::getByUID($this->options['user_uid']);

            if ($this->user === FALSE) {
                throw new \Exception("That user could not be found.", 404);
            }
        } else {
            # we are adding a new user to the calendar
            $this->user = NULL;
        }
    }

    public function getAvailableUsers()
    {
        return $this->calendar->getUsersNotOnCalendar();
    }

    public function getAllPermissions()
    {
        return new Permissions;
    }

    private function addUser($post_data)
    {
        $user = User::getByUID($post_data['user']);

        foreach ($post_data as $key => $value) {
            if (strpos($key, 'permission_') === 0 && $value == 'on') {
                # this permission is checked
                $perm_id = (int)(substr($key, 11));
                $user->grantPermission($perm_id, $this->calendar->id);
            }
        }

        return $user;
    }

    private function updateUser($post_data)
    {
        # check the permissions that the user currently has.
        $current_permissions = $this->user->getPermissions($this->calendar->id);

        foreach($current_permissions as $permission) {
            # if this permission is not checked, remove it
            if (!(array_key_exists('permission_' . $permission->id, $post_data) && $post_data['permission_' . $permission->id] == 'on')) {
                $this->user->removePermission($permission->id, $this->calendar->id);
            }

            # we no longer need to check on this permission (for later adding)
            unset($post_data['permission_' . $permission->id]);
        }

        # add remaining permissions
        foreach ($post_data as $key => $value) {
            if (strpos($key, 'permission_') === 0 && $value == 'on') {
                # this permission is checked
                $perm_id = (int)(substr($key, 11));
                $this->user->grantPermission($perm_id, $this->calendar->id);
            }
        }

        return $this->user;
    }

    public function handlePost(array $get, array $post, array $files)
    {
        // check if we are looking to edit a user's permissions
        if (array_key_exists('user_uid', $this->options)) {
            $this->user = User::getByUID($this->options['user_uid']);
            $this->updateUser($post);
        } else {
            # we are adding a new user
            $this->user = $this->addUser($post);
        }

        //redirect
        return $this->calendar->getUsersURL();
    }
}