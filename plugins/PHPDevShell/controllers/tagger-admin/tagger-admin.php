<?php

/**
 * Tags Manager: Allows you to manage data by tagging objects together.
 * @author Jason Schoeman
 * @return string
 */
class taggerAdmin extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$this->template->heading(__('Tags Manager'));

		$security = $this->security;

		// Action url.
		$self_url_ = $this->navigation->buildURL(false);
		// Should we delete a setting?
		if (!empty($security->get['dt'])) {
			// Lets delete the entry.
			$this->db->deleteQuick('_db_core_tags', 'tagID', $security->get['dt']);

			// Show ok deleted.
			$this->template->ok(sprintf(__('Deleted setting entry %s.'), $security->get['dt']));
		}
		// We have a save action, lets handle it.
		if (! empty($security->post['save'])) {
			$this->db->invokeQuery('PHPDS_updateTagsQuery');
		}

		// Lets create the list of all the entries.
		$RESULTS = $this->db->invokeQuery('PHPDS_readTagsQuery');

		// Load views.
		$view = $this->factory('views');

		// Set Values to Template.
		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('tagField', tag_field_object());
		$view->set('RESULTS', $RESULTS['list']);
		$view->set('self_url', $self_url_);

		// Call template.
		$view->show();
	}
}

return 'taggerAdmin';