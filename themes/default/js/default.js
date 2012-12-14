



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
		if (deferred.status !== 200) {
			/*deferred.reject();
			alert('Error ' + deferred.status);*/
		}
	}).fail(function(deferred, status) {
		if (deferred.status !== 200) {
			//deferred.reject();
			alert('Error! ' + deferred.statusText);
		}
	}
	);
}

/**
 * Apply default formating to the objects inside the given root element (root element is optional, defaults to BODY)
 * @param root DOM object to assign.
 */
function PHPDS_documentReady(root)
{
	if (!root) {
		root = $('BODY');
	}
}