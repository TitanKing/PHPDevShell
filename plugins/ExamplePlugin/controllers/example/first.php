<?php
class myFirstApp extends PHPDS_controller
{
	// execute is the default controller method.
	public function execute()
	{
		$this->template->styleForms();
		$this->template->validateForms();
		$this->template->styleButtons();

		$this->template->heading('My First App');
		$this->template->info('This is just my first sandbox application in PHPDevShell, I like what I see so far!');

		$this->handleForm();
		$this->myFirstForm();
	}

	private function handleForm()
	{
		if (! empty($_POST)) {
			if (! empty($_POST['name'])) {
				$this->template->ok(sprintf("Thanks girlfriend, the name {$_POST['name']} was submitted."));
			} else {
				$this->template->warning('Are you sick in your head, no name was given!?');
			}
		}
	}

	private function myFirstForm()
	{
		// Lets create some form.
		$FORM_HTML = <<<HTML
			<form action="{$this->navigation->buildURL()}" method="post" class="validate">
				<p>
					<label>Enter your name
						<input type="text" size="20" name="name" value="" title="Just enter your name!">
					</label>
				</p>
				<p>
					<button type="submit" name="save" value="save"><span class="save"></span><span>Submit Name</span></button>
				</p>
			</form>
HTML;
		echo $FORM_HTML;
	}
}
return 'myFirstApp';
?>
