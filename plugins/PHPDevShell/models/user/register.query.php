<?php

/**
 * Register - Select Tokens
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_SelectTokensQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			`token_id`, `token_name`, `token_key`, `registration_option`, `available_tokens`
		FROM
			_db_core_registration_tokens
		WHERE
			registration_option = 1
		AND
			available_tokens > 0
    ";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return string
	 */
	public function invoke($parameters = null)
	{
		$select_registration_tokens = parent::invoke();
		// Set.
		$registration_selection = false;
		// Fetch results and create token options..
		if (empty($select_registration_tokens)) $select_registration_tokens = array();
		foreach ($select_registration_tokens as $reg_tokens_array) {
			// Check if we should list this as an item.
			$registration_selection .= <<<HTML
				<option value="{$reg_tokens_array['token_id']}">
					 {$reg_tokens_array['token_name']}
				</option>
HTML;
		}
		if (!empty($registration_selection)) {
			return $registration_selection;
		} else {
			return '';
		}
	}
}

/**
 * Register - Count Tokens
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_CountTokensQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			COUNT(token_id)
		FROM
			_db_core_registration_tokens
		WHERE
			registration_option = 0
		AND
			available_tokens > 0
    ";
	protected $singleRow = true;
}

/**
 * Register - User Detail
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_UserDetailQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id, user_name, user_display_name, user_email
		FROM
			_db_core_users
		WHERE
			(user_name = '%s'
		OR
			user_display_name = '%s'
		OR
			user_email = '%s')
    ";
	protected $singleRow = true;
}

/**
 * Register - Check Token
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_CheckTokenQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			token_id, user_role_id, user_group_id
		FROM
			_db_core_registration_tokens
		WHERE
			available_tokens > 0
		AND
			token_key = '%s'
    ";
	protected $singleRow = true;
}

/**
 * Register - Check Token by ID
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_CheckTokenByIdQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			token_id, user_role_id, user_group_id
		FROM
			_db_core_registration_tokens
		WHERE
			available_tokens > 0
		AND
			token_id = '%s'
    ";
	protected $singleRow = true;
}

/**
 * Register - Write Registration
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_WriteRegQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_users (user_id, user_display_name, user_name, user_password, user_email, user_group, user_role, date_registered, language, timezone, region)
		VALUES ('', '%s', '%s', '%s', '%s', '%u', '%u', '%u', '%s', '%s', '%s')
    ";
	protected $returnId = true;
}

/**
 * Register - Update Tokens
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_UpdateTokensQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			 _db_core_registration_tokens
		 SET
			 available_tokens = available_tokens - 1
		 WHERE
			 token_id = %u
    ";
}

/**
 * Register - Update Registration Queue
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_UpdateRegQueueQuery extends PHPDS_query
{
	protected $sql = "
		INSERT INTO
			_db_core_registration_queue (user_id, registration_type, token_id)
		VALUES ('%u', '%s', '%u')
    ";
}

/**
 * Register - Rollback
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_RollbackQuery extends PHPDS_query
{
	protected $sql = "ROLLBACK";
}