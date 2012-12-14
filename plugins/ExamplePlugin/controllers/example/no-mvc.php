<?php

// Call this line to protect your no-mvc script from unwanted access.
(is_object($this->security)) ? $this->security->securityIni() : exit('Access Denied!');
// If you dont like mvc, you dont have to use it.

// And magically you still have all the PHPDevShell object available.
$this->template->heading(__('No MVC, you dont like MVC hey?'));
$this->template->info(__('PHPDevShell gives you the freedom to decide how you want to code.'));

// Except you write everything outside a class using plain PHP.
?>
<h1>Wow I love PHPDevShell</h1>
<p>It is starting to become clear that PHPDevShell is very flexible</p>
<p>Your name is <?php echo $this->configuration['user_display_name'] ?> by the way, we are watching you.</p>

