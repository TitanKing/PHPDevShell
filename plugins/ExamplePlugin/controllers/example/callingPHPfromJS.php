<?php


class PHP_controller extends PHPDS_controller
{
	function execute()
	{
		
		?>
<h1>This page will demonstrate how easy it is to call a PHP function from your Javascript</h1>
<p>Click <button id="ExampleButton">here</button> and the JS code will send the version of jQuery to the server and display the result in the following line:</p>
<p id="ExampleSentence" style="font-style: italic">This sentence will be replace by the one from the server.</p>
<p>The JS code:</p>
<pre>
	var versionOfjQuery = jQuery.fn.jquery;
	var browserLanguage = navigator.language;
	$.when(PHPDS_remoteCall('ExampleFunction', {language: browserLanguage, version: versionOfjQuery}))
	.then(function(result){
		$('#ExampleSentence').html('&lt;b&gt;' + result + '&lt;/b&gt;');
	});
</pre>
<p>The PHP code</p>
<pre>
	function ajaxExampleFunction($version, $dummy, $language = 'unknown')
	{
		$date = date('H:i:s');
		return "Now at $date, you are using jQuery version $version and your language is $language";
		
	}
</pre>
<p>Nothing more has to be done on the controller side.</p>
<p style="font-weight: bold">NOTE: starting with PHP5 you can give the arguments to the JS call in any order, they are passed by name (prior to PHP5 they are given in order).</p>
<script>
	$(function(){
		$('#ExampleButton').click(function(){
			var versionOfjQuery = jQuery.fn.jquery;
			var browserLanguage = navigator.language;
			$.when(PHPDS_remoteCall('ExampleFunction', {language: browserLanguage, version: versionOfjQuery}))
			.then(function(result){
				$('#ExampleSentence').html('<b>' + result + '</b>');
			});
		});
	});
	
	
	
	
	
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
				url += ((url.indexOf('?') >= 0) ? '&' : '?') + $.param(extParams);
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
				alert('Error ' + deferred.status);
			}
		}
		);
	}
</script>
		<?php
	}
	
	function ajaxExampleFunction($version, $dummy, $language = 'unknown')
	{
		$date = date('H:i:s');
		return "Now at $date, you are using jQuery version $version and your language is $language";
		
	}
	
	
}

return 'PHP_controller';