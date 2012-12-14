<?php

/**
 * Registration Token List: Lists all available tokens for administration purposes.
 * @author Jason Schoeman
 * @return string
 */
class RegTokenList extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Header information
		$this->template->heading(_('Registration Token Listing'));

		// Check if we need to create the token url for copy and paste.
		if (!empty($this->security->get['ck'])) {
			// Do query for token key values.
			$token_key_ = $this->db->selectQuick('_db_core_registration_tokens', 'token_key', 'token_id', $this->security->get['ck']);
			if ($token_key_) {
				// Require registration page.
				$settings['registration_page'] = $this->db->essentialSettings['registration_page'];
				// Compile token URL.
				$token_url_ = $this->configuration['absolute_url'] . '/index.php?m=' . $settings['registration_page'] . '&token_key=' . $token_key_;
				// Return URL message.
				$this->template->message(sprintf(_('Provide the following registration URL to newly registered users, who you wish to be automatically moved using token id (%s) :<br />%s'), $this->security->get['ck'], "<input type=text size=100 value=$token_url_ class=boxdisabled readonly>"));
			}
		}

		if (!empty($this->security->get['drg'])) {
			// Now we can delete registration token.
			$deleted_registration_token = $this->db->deleteQuick('_db_core_registration_tokens', 'token_id', $this->security->get['drg'], 'token_name');

			if ($deleted_registration_token) {
				$this->template->ok(sprintf(_('Registration token "%s" was deleted.'), $deleted_registration_token));
			} else {
				$this->template->warning(sprintf(_('No token "%s" to delete.'), $this->security->get['drg']));
			}
		}

		$RESULTS = $this->db->invokeQuery('PHPDS_readTokenListQuery');

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('RESULTS', $RESULTS['list']);

		// Output Template.
		$view->show();
	}
}

return 'RegTokenList';
