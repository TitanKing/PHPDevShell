$(document).ready(function() {
	//filter results based on query  
	function filter(selector, query) {
	  query = $.trim(query); //trim white space
	  query = query.replace(/ /gi, '|'); //add OR for regex query

	  $(selector).each(function() {
		($(this).text().search(new RegExp(query, "i")) < 0) ? $(this).hide().removeClass('visible') : $(this).show().addClass('visible');
	  });
	}
	
	$('tbody tr').addClass('visible');

	$('#search_field').keyup(function(event) {
		//if esc is pressed or nothing is entered
		if (event.keyCode == 27 || $(this).val() == '') {
		//if esc is pressed we want to clear the value of search box
			$(this).val('');
  
			//we want each row to be visible because if nothing
			//is entered then all rows are matched.
			$('tbody tr').removeClass('visible').show().addClass('visible');
		}
  
		//if there is text, lets filter  
		else {
			filter('tbody tr', $(this).val());
		}
	});
});