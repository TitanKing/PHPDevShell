<?php

class menuItemAdminView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;
		// Require JS.
		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();
		$template->styleSelect();
		$template->addJsFileToHead("themes/cloud/js/dircheck/dircheck.jquery.js");
		$js = <<<JS

			$(document).ready(function() {
				$('#menu_link_check').instantcheck({ controls: [ 'INPUT[name=menu_id]', 'INPUT[name=menu_link]', 'INPUT[name=menu_type]:checked', 'INPUT[name=plugin]' ], success: function (event, data) {
						if (data.title) $('#locationLabel').html(data.title);
					}
				});
				$('#plugin_check').instantcheck({ controls: [ 'INPUT[name=plugin]' ] });
				$('#alias_check').instantcheck({ controls: [ 'INPUT[name=menu_id]', 'INPUT[name=alias]' ] });
			});

JS;
		$template->addJsToHead($js);
	}
}

return 'menuItemAdminView';
