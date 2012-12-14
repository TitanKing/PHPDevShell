<?php

class viaAjaxView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();

		// Get ajax search ajax menu.
		$livesearch = $this->navigation->buildURL(null, "callback=");

		$template->addJsFileToHead("themes/cloud/jquery/js/jquery.ui.autocomplete.min.js");

		// For live ajax search we need to throw some javascript to the template head.
		$JS = <<<JS

			$(document).ready(function() {
				//attach autocomplete
				$("#livesearch").autocomplete({
					//define callback to format results
					source: function(req, add){
						//pass request to server
						$.getJSON("{$livesearch}?", req, function(data) {
							//create array for response objects
							var suggestions = [];
							//process response
							$.each(data, function(i, val){
								suggestions.push(val.name);
							});
							//pass array to callback
							add(suggestions);
						});
					}
				});
			});

JS;

		$this->template->addJsToHead($JS);
	}
}

return 'viaAjaxView';
