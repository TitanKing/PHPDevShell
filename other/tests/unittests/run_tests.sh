#!bash

cd framework
phpunit --verbose --colors \
	--coverage-html ../diag/coverage  \
	--testdox-html ../diag/dox.html \
	--bootstrap bootstrap.php \
	./includes/
