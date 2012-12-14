<!-- PHPDevShell uses a 12 Grid system called 1KB Grid. -->
<!-- This awesome grid gives you 12 colums without tables -->
<div class="row">
	<div class="column grid_6">
		<article>
			<h2>
				Hi {$developers_name}, welcome to the Example Plugin
			</h2>
			<p>
				Source Code Located : <code>plugins/ExamplePlugin/controllers/readme-example.php</code>
			</p>
			<p>
				Following the source code of this plugin you should be able to quickly learn how plugins look and work. You will also be able to see how we see MVC and how you can utilize it without restriction. We obviously only provide you with a neat structure to follow. However, you don’t need to follow our MVC structure, you can follow your own. You can even use your own framework inside the controller file.
			</p>
			<p>
				You can find the source of this plugin inside plugins/ExamplePlugin, its then divided into folders models, controllers and views. To be honest, we believe pure MVC is a Java orientated which is not really meant for a scripting language.  We simplified it allot, made it manageable and it’s up to you to use it or not.
			</p>
			<p>
				If you decide MVC is not needed, you only need one file per menu item, you can use this file to code in directly using pure PHP.
			</p>
			<p>
				{$mvc_exmplained}
			</p>
			<h2>
				It's so pure and clean...
			</h2>
			<p>
				PHPDevShell is always in the process of cleaning and offering neat coding and possibilities. Take for instance the HTML, if you would like your pages to look like the theme you are using, simply use pure HTML, no extra classes are needed in the tags. Just plain HTML and it will style it alike. This will give your administrator interface a consistent look and feel.
			</p>
			<p>
				There are 7 main objects you can use in PHPDevShell to assist you with development, they are;
				<code>$this->db, $this->navigation, $this->security, $this->template, $this->user, $this->tagger, $this->core</code> you also have a range of PU_utility functions.
				Most of the methods in PHPDevShell tells a story like,
				<code>$this->user->isRoot(),  $this->user->belongToGroup() or $this->navigation->createMenuId()</code>. Hopefully our easy naming convention will make you remember them easy.
			</p>
			<h2>
				Lets look at a few handy methods and available session data.
			</h2>
			<p>
				PHPDevShell provides developer with the most important methods to manage users, security, roles, groups, tags and a whole set of data about current logged in users. Its clean lightweight modular engine allows you to extend your applications through plugins, making it so much more maintainable and reusable. 
			</p>
			<p>
				We intend to expand these methods to cover most possible scenarios. However the ones available will certainly get most requests done.
			</p>
			<p>
				<span class="big">Your user id is </span><span class="bigger">{$user_id}. </span><span class="big">You belong to primary role</span><span class="bigger"> {$role} </span><span class="big">and primary group</span><span class="bigger"> {$group}. </span>
				<span class="big">The username you picked to log in is</span><span class="bigger"> {$username} </span><span class="big">or you could log in with</span><span class="bigger"> {$email} </span>
				<span class="big">Look at </span><a href="{$info_url}" class="bigger">System Info</a><span class="big"> for more available session data.</span>
			</p>
			<h2>Security and Navigation</h2>
			<p>The most important part of any application is navigation and access control. PHPDevShell takes care of this in an elegant way. If a user does not have permission to access a certain item, PHPDevShell does not even know of that item. For PHPDevShell, if you have no access to an item, that item does not exist.</p>
			<p>In a neat navigation array, you have access to every element the user can browse or access. This can be used for a wide variety of applications.</p>
			<p>You can find anything navigation related in regards to current logged in user under array <code>$this->navigation->navigation;</code></p>
			<p>For instance, let’s look if we can find the current pages alias. Ah, there we go, the alias for the current page is <span class="bigger">{$alias}.</span></p>
			<h3>Roles, Groups and Tagging</h3>
			<p>There are two types of access to help you provide data to a certain user. Roles are meant to provide or prevent access to a user, where Groups on the other hand is meant to group data together, so that for instance user Peter, can only see reports for Peters school.</p>
			<p>Tags on the other hand, is an extremely powerful way to tag anything you want together.</p>
			<p>Lets quickly look at a few methods in the "$this->user" object;</p>
			<p><span class="big">You have access to the following groups, </span><span class="bigger">{foreach $access_to_groups as $group_id}{$group_id},{/foreach}</span><span class="big">see how easy it is {$smile}</span></p>
			<h2>Database</h2>
			<p>The $this->db object is what you will use to work with data extensively; we will look at this object later. We are planning to support multiple databases in the near future, in fact, within the next few releases. PHPDevShell will continue to install on the default MySQL database until Oracle does what it is does something we don’t like, we will probably jump to Postgre then.</p>
			<p>Let's get some sample settings from database:</p>
			<p><span class="bigger">{$setting.sampleSetting1}</span></p>
			<p><span class="bigger">{$setting.sampleSetting2}</span></p>
			<p><strong>Remember, models can be reused by simply including one controllers model in another controller using standard php include.</strong></p>
			<h2>Reusing codes using helper classes.</h2>
			<p>The cool thing about supporting/helper classes is the ability to reuse code over and over again, PHPDevShell offers and extemely easy way to do this.</p>
			<p>{$reused_htmlcode}</p>
			<p>Taking it further, helper classes can have their own models extending reusing code even more.</p>
			<p>Let's call some support method who in turn uses his own model, the model called <span class="vlarge">{$reused_codewithmodel}</span></p>
			<h2>Whats next...</h2>
			<p>With the next example we will look at the database model more extensively. Remember you can do things with whatever you feel comfortable with. We don’t want to restrict you in any way. So let’s continue on to the next example. We will save some data and do a listing to the database with the tables we created during the install process.</p>
			<h1><a href="{$example2}">Continue to next example...</a></h1>
		</article>
	</div>
	<div class="column grid_6">
		<article>
            <h1>Some Features...</h1>
			<h2>Themes</h2>
			<p>
				Using the most stable and best HTML5 and CSS3 features the theme is what wraps your application. Standard elements are used, this means there is no need to learn endless classes to make your application look consistent on every page. Just type HTML in your view as you know it.
			</p>
			<p>
				It’s extremely easy to create your own theme, and it is 100% customizable to have a theme for your next application or website exactly how you would like it.
			</p>
			<p>
				Using <a href="http://www.1kbgrid.com/">1KB Grid</a>, we offer the simplest solution to do complicated layouts without using tables using the standard cloud theme.
				You are free to choose whatever CSS frameworks (if any) you like when designing your own theme.
			</p>
			<h2>JQuery Skins</h2>
			<p>On top of themes you can give color to your theme and widgets with skins.</p>
            <p>Make your theme seamlessly integrate and feel the same as Jquery widgets. The default theme comes with over 25 preset skins for those developers who prefer to develop rather then design. You can even create your own skins within minutes using the JQuery Theme Roller.</p>
			<h2>Widget Preview</h2>
			{$error}
			{$warning}
			{$critical}
			{$ok}
			{$notice}
			{$busy}
			{$message}
			{$note}
			{$scripthead}
			<h2>Some Form Elements</h2>
			<p>
				<label>{_e('Text Field')}
					<input type="text" size="20" name="text" value="" title="{_e('Sample Text Title.')}">
				</label>
			</p>
			<p>
				<label>{_e('Text Field Required')}
					<input type="text" size="20" name="textreq" value="" required="required" title="{_e('Sample Required Text Title.')}">
				</label>
			</p>
			<p>
				<button type="submit" name="sample" value="sample"><span class="save"></span><span>{_e('Sample Button')}</span></button>
				<button type="submit" name="sample_" value="sample_"><span class="ui-icon ui-icon-transferthick-e-w left"></span><span>{_e('Another Sample Button')}</span></button>
			</p>
			<h2>Other Elements Styling</h2>
			<h1>Heading 1</h1>
			<h2>Heading 2</h2>
			<h3>Heading 3</h3>
			<h4>Heading 4</h4>
			<h5>Heading 5</h5>
			<h6>Heading 6</h6>
            <p>
				This is <abbr title="title">abbreviation</abbr><br>
				This is <strong>strong</strong><br>

				This is <em>emphasis</em><br>
				This is <b>bold text</b><br>
				This is <i>italic text</i><br>
				This is <cite>cite</cite><br>
				This is <small>small text</small><br>

				This is <big>big text</big><br>
				This is <del>deleted text</del><br>
				This is <ins>inserted text</ins><br>
				This is <dfn>defining instance</dfn><br>
				This is <kbd>user input</kbd><br>

				This is <samp>sample output</samp><br>
				This is <q>inline quotation</q> <br>
				These are <sub>subscript</sub> and <sup>superscript</sup><br>
				This is <var>a variable</var>
            </p>
			<h2>Tables</h2>
			<table summary="This is the summary text for this table.">
				<caption><em>A test table with a thead, tfoot, and tbody elements</em></caption>
				<thead>
					<tr>
						<th>Table Header One</th>
						<th>Table Header Two</th>
						<th>Table Header Image</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>TD One</td>
						<td>TD Two</td>
						<td>{$urlbutton}</td>
					</tr>
					<tr>
						<td>TD One</td>
						<td>TD Two</td>
						<td>{$urlbutton}</td>
					</tr>
					<tr>
						<td>TD One</td>
						<td>TD Two</td>
						<td>{$urlbutton}</td>
					</tr>
					<tr>
						<td>TD One</td>
						<td>TD Two</td>
						<td>{$urlbutton}</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3">tfoot footer - some text for a footer or pagination</td>
					</tr>
				</tfoot>
			</table>
			<h2>User Interface Images</h2>
			<p>
				Pick from over three thousand images for your next applications ui functionality, this done with a simple function.
			</p>
			<p>
				{$img1} {$img2} {$img3} {$img4}
			</p>
		</article>
	</div>
</div>
