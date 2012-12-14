<?php

class WidgetExampleView extends PHPDS_controller
{

	public function execute()
	{
		$template = $this->template;

		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();

		$template->addJsFileToHead("themes/cloud/jquery/js/jquery.ui.autocomplete.min.js");

		// Get ajax search ajax menu.
		$livesearch = $this->navigation->buildURL("2932001018", "callback=");

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

return 'WidgetExampleView';