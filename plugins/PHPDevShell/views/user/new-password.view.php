<?php

class newPasswordView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();

		$this->template->addJsFileToHead('themes/cloud/js/password/jquery.password.js');
		$shortPass = _('Too Short');
		$badPass = _('Weak');
		$goodPass = _('Good');
		$strongPass = _('Strong');
		$samePass = _('Username and Password identical!');
		$passwordMeter = <<<JS
           $(document).ready(function() {
                    $.fn.shortPass = '{$shortPass}';
                    $.fn.badPass = '{$badPass}';
                    $.fn.goodPass = '{$goodPass}';
                    $.fn.strongPass = '{$strongPass}';
                    $.fn.samePassword = '{$samePass}';
                    $.fn.resultStyle = "";
                    $(".password_test").passStrength({
                        shortPass: 		"critical",
                        badPass:		"warning",
                        goodPass:		"notice",
                        strongPass:		"ok",
                        baseStyle:		"passwordMeter",
                        userid:         "#user_name_test"
                    });
            });
JS;
		$this->template->addJsToHead($passwordMeter);
	}
}

return 'newPasswordView';
