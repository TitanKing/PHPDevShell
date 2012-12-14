<p>And one of them was Linus Torvalds.</p>
<h4>Lets discuss the ajax node type... {$name}</h4>
<p>Data passed: {$data}</p>
<p>
	The ajax node type is also a kind of a widget without the restrictions of a graphical frame making it appear like a widget. Remember the ajax node techniques on this page is to call smaller html pages.
	For advanced ajax you will most probably used the <em>viaAjax</em> method.
</p>
<p>
	The default <em>requestAjax</em> method is really meant for simple ajax calls exactly like widgets without the "widget" styling.
	Once an ajax node is called you can continue to do more advanced ajax queries on it.
	The ajax node can be more advanced by sending your own jquery/javascript instructions to it instead of using <em>requestAjax.</em>
	For very advanced ajax, you should use the controller method <em>viaAjax.</em>
</p>
<p>
	<button type="submit" value="{_e('Some Button')}" name="subscribe"><span class="save"></span><span>{_e('Some Button')}</span></button>
</p>
