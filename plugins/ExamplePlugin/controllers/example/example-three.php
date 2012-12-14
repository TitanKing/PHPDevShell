<?php

// The power of $this->parent allows you add any class to $this-> of the phpdevshell root.

$test = $this->singleton('Station');
echo $test->methodTwo() . '<br>'; // Print 0
echo $test->methodTwo() . '<br>'; // Print 1
echo $test->methodTwo() . '<br>'; // Print 2
$extend = $this->singleton('Sharing');
print $extend->someOtherMethod() . '<br>'; // Print 3


class Station extends PHPDS_dependant
{
	protected $count = 0;

	public function methodTwo()
	{
		return $this->count++;
	}
}

class Sharing extends PHPDS_dependant
{
	public function someOtherMethod()
	{
		$instance = $this->singleton('Station');
		print $instance->methodTwo();
	}

	public function sharedResources()
	{
		// Everything available.
		$this->core->formatDateTime();
	}
}

$a = $this->factory('Station');
$b = $this->factory('Station');
$c = $this->singleton('Station');
$d = $this->singleton('Station');

// Note & before the class name, this tells the system it should be 
$e = $this->factory('&Station');

print '$a = $b ? '.($a === $b ? 'yes' : 'no').' (unique)<br/>';
print '$a = $c ? '.($a === $c ? 'yes' : 'no').' (unique)<br/>';
print '$c = $d ? '.($c === $d ? 'yes' : 'no').' (instance)<br/>';
print '$c = $e ? '.($c === $d ? 'yes' : 'no').' (shortcut to instance)<br/>';