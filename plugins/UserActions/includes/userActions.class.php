<?php

/**
 * Create your own plugin with these methods to catch user action data.
 *
 * @author Jason Schoeman
 */
class userActions extends PHPDS_dependant
{
	/**
	 * On successful delete the deleted users information will be contained in $userArray.
	 * 
	 * @param array $userArray
	 */
	public function userDelete ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * On successful adding a users information will be contained in $userArray.
	 *
	 * @param array $userArray
	 */
	public function userAdd ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * On successful updating a users information will be contained in $userArray.
	 *
	 * @param array $userArray
	 */
	public function userUpdate ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * User edits his preferences, users information will be contained in $userArray.
	 *
	 * @param array $userArray
	 */
	public function userEditPreferences ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * On successful register, users information will be contained in $userArray.
	 *
	 * @param array $userArray
	 */
	public function userRegister ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * On successful self verification, users information will be contained in $userArray.
	 *
	 * @param array $userid
	 */
	public function userRegisterVerified ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * User may change his own password, you might need this information.
	 *
	 * @param array $use
	 */
	public function userChangedPassword ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * When viewing user list, updating the whole lists gets send here.
	 *
	 * @param array $userArray
	 */
	public function userMultipleUpdate ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * When user is approved from pending list, this method gets called.
	 *
	 * @param array $userArray
	 */
	public function pendingUserApproved ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * When user is banned from pending list, this method gets called.
	 *
	 * @param array $userArray
	 */
	public function pendingUserBanned ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * When user is banned from pending list, this method gets called.
	 *
	 * @param array $userArray
	 */
	public function pendingUserDeleted ($userArray)
	{
		// print_r($userArray);
	}

	/**
	 * When a mass action is called on pending users this method is called.
	 *
	 * @param array $usersArray
	 * @param array du = Delete Users, bu = Ban Users, au = Approve Users, aue = Approve Users and Mail
	 */
	public function pendingUserMassAction ($usersArray, $action_type)
	{
		// print_r($usersArray);
		// print $action_type;
	}

	/**
	 * When users are imported, this method is called
	 *
	 * @param array $usersArray
	 */
	public function usersImportAction ($usersArray)
	{
		// print_r($usersArray);
	}
}
