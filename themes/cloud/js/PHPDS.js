



/**
 * Call a PHP function
 *
 * The function must be handled by the current controller (method name is "ajax" + functionName)
 *
 * Parameters are preferably passed through POST for two reasons:
 * - GET data maybe polluted for other reasons (sessions handling, ...) where POST are always under control
 * - GET data appear in URL therefore are limited in size and charset
 * @see http://www.cs.tut.fi/~jkorpela/forms/methods.html
 *
 * Note: only application parameters are sent through GET/POST, handling data such as function name sent though headers
 *
 * Caution: prior to PHP 5 the parameters fed to the PHP function are given IN ORDER, NOT BY NAME
 *
 * @param functionName string, the name of the function to call (ie. method "ajax"+functionName of the controller)
 * @param params array, data to be serialized and sent via POST
 * @param extParams array (optional), data to be serialized and sent via GET
 *
 * TODO: possibility of calling a method from another controller
 * TODO: handle errors gracefully
 *
 */
function PHPDS_remoteCall(functionName, params, extParams)
{
	var url = document.URL;
	if (extParams) {
			//url += ((url.indexOf('?') >= 0) ? '&' : '?') + $.param(extParams);
		url = URI(url).addQuery(extParams).href();
	}
	return $.when($.ajax({
		url:  url,
		dataType: 'json',
		data: params,
		type: 'POST',
		headers:  {'X-Requested-Type': 'json', 'X-Remote-Call': functionName},
		beforeSend : function(xhr) {
				xhr.setRequestHeader('X-Requested-Type', 'json');
				xhr.setRequestHeader('X-Remote-Call', functionName);
		}
	})).done(function(data_received, status ,deferred) {
		if (deferred.status != 200) {
			/*deferred.reject();
			alert('Error ' + deferred.status);*/
		}
	}).fail(function(deferred, status) {
		if (deferred.status != 200) {
			//deferred.reject();
			alert('Error! ' + deferred.statusText);
		}
	}
	);
}



/**
 * Apply default formating to the objects inside the given root element (root element is optional, defaults to BODY)
 */
function PHPDS_documentReady(root)
{
	if (!root) {
		root = $('BODY');
	}
	/* Navigation
	 *************************************************/
	/* Hover over selectors */
	$("#nav li a, #bread li a, .cp-selector, .hover, .loginlink", root).hover(
		function () {
			$(this).addClass("ui-state-hover");
		},
		function () {
			$(this).removeClass("ui-state-hover");
		});
	/* General theming. */
	$(".active", root).addClass("ui-state-active");
	$(".cp-selector, .hover, .loginlink", root).addClass("ui-state-default ui-corner-all");

	/* Navigation. */
	$("#nav > li > a, ul#bread > li > a", root).addClass("ui-state-default ui-corner-all");
	$("#nav li a, #bread li a", root).hover().addClass("ui-corner-all");
	$("#nav ul, #bread ul, fieldset", root).addClass("ui-widget-content ui-corner-all");
	$("#nav > .current a, #bread > .current a", root).addClass("ui-state-active ui-corner-all");
	$("#nav .grandparent .nav-grand, #bread .grandparent .nav-grand", root).addClass("ui-icon ui-icon-triangle-1-s left");
	$("#nav ul .parent .nav-parent, #bread ul .parent .nav-parent", root).addClass("ui-icon ui-icon-triangle-1-e right");
	$("#bread .jump span", root).addClass("ui-icon ui-icon-calculator left");
	$("#bread .home span", root).addClass("ui-icon ui-icon-home left");
	$("#bread .up span", root).addClass("ui-icon ui-icon-arrowreturnthick-1-w left");

	/* Login */
	$("#logged-in span", root).addClass("ui-icon ui-icon-power left");
	$("#logged-out span", root).addClass("ui-icon ui-icon-key left");
}

