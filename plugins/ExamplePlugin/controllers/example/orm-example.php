<?php

// In ORM example we will show you how to use the ORM system.

class ormExample extends PHPDS_controller
{
	public function execute()
	{
		$this->template->heading('ORM Example');

		// Call ORM plugin.
		$this->factory('orm');
		
		// Will create a table called 'book';
		$book = R::dispense('example_book');
		
		// Deleting a record
		if (! empty($_GET['delete'])) {
			$sold = R::load("example_book", $_GET['delete']);
			R::trash($sold);
			$this->template->note(sprintf('Record %s was deleted', $_GET['delete']));
		}	
		
		// Sell a book
		if (! empty($_GET['sell'])) {
			$title = urldecode($_GET['title']);
			$this->template->ok("Great, thanks for buying \"{$title}\". You are a little weird reading this, but who are we to judge.", false, false);
		}
		
		// To update a field, you need to provide the id (always id) like so;
		// $book->id = 1;
		
		// Will execute saving to database.
		if (! empty($_GET['sell'])) {
			$sold = $book->import($_GET, 'category,type,author,price,title');
			if ($sold)
				$this->template->ok("That went well, we just sold another book.", false, false);
			R::store($book);
		}
		
		// Reading from ORM is just as simple...
		$books = R::find('example_book');
		
		$book1 = $this->navigation->sefURL(null, 'sell=true&category=books&type=Sci-Fiction&author=Peter Viljoen&price=3.99&title=The Robot Rapist');
		$book2 = $this->navigation->sefURL(null, 'sell=true&category=books&type=Health&author=Samie Caster&price=2.99&title=Getting hard again');
		$book3 = $this->navigation->sefURL(null, 'sell=true&category=books&type=Horror&author=Lion Wessels&price=8.99&title=Cinderella goes rogue');
		
		// Please see http://redbeanphp.com/ for complete manual.
		// For all R methods see : http://www.redbeanphp.com/api/class_r.html
		
		$view = $this->factory('views');
		$view->set('delete', $this->navigation->sefURL(null, 'delete='));
		$view->set('special1', $book1);
		$view->set('special2', $book2);
		$view->set('special3', $book3);
		$view->set('books', $books);
		$view->show();
	}
}

return 'ormExample';

