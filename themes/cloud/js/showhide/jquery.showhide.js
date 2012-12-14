$(document).ready(function() {
	/* Show and Hide multiple elements */
	// Andy Langton's show/hide/mini-accordion - updated 23/11/2009
	// Latest version @ http://andylangton.co.uk/jquery-show-hide

	// this tells jquery to run the function below once the DOM is ready

	// choose text for the show/hide link - can contain HTML (e.g. an image)
	var showText='<span class="ui-icon ui-icon-triangle-1-s"></span>';
	var hideText='<span class="ui-icon ui-icon-triangle-1-n"></span>';

	// initialise the visibility check
	var is_visible = false;

	// append show/hide links to the element directly preceding the element with a class of "toggle"
	$('.toggle').prev().append('<a href="#" class="toggleLink">'+showText+'</a>');

	// hide all of the elements with a class of 'toggle'
	$('.toggle').hide();

	// capture clicks on the toggle links
	$('a.toggleLink').click(function() {

        //Check the status of the icon (showText or hideText?) and toggle the icon
        //Modified by Jeff Sherk - December 16, 2010
        if (showText == $(this).html() ) {
            $(this).html(hideText);
        } else {
            $(this).html(showText);
        }

		// toggle the display - uncomment the next line for a basic "accordion" style
		//$('.toggle').hide();$('a.toggleLink').html(showText);
		$(this).parent().next('.toggle').toggle('slow');

		// return false so any link destination is not followed
		return false;

	});
});


