<?php

class CronjobAdminList extends PHPDS_controller
{

	/**
	 * Simply optimizes Database.
	 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 24 June 2010
	 */
	public function execute()
	{
		$this->template->heading(_('Cronjobs Admin'));

		// Delete broken cron item.
		if (!empty($this->security->get['dc']) && $this->user->isRoot()) {
			if ($this->db->deleteQuick('_db_core_cron', 'menu_id', $this->security->get['dc'])) {
				$this->template->ok(sprintf(_('Orphan item %s was deleted.'), $this->security->get['dc']));
			} else {
				$this->template->warning(sprintf(_('No orphan cron "%s" to delete.'), $this->security->get['dc']));
			}
		}

		$so = $this->navigation->buildURL(false, 'so=');
		if (!empty($this->security->get['so']) && $this->security->get['so'] == 'true') {
			$orphan_sql = false;
			$show_orphans = "<a href=\"{$so}false\">" . _('(Show Crons)') . "</a>";
		} else {
			$orphan_sql = true;
			$show_orphans = "<a href=\"{$so}true\">" . _('(Find Orphans)') . "</a>";
		}

		$RESULTS = $this->db->invokeQuery('PHPDS_listCronjobAdminQuery', $orphan_sql);

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('RESULTS', $RESULTS);

		// Set Values.
		$view->set('show_orphans', $show_orphans);

		// Output Template.
		$view->show();
	}
}

return 'CronjobAdminList';