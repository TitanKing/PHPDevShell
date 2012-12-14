<div class="row">
	<div class="column grid_6">
		<article>
			<h1>
				Looking at ORM and storing data the easy way
			</h1>
			<h3>Lets create a few links, grab the data and store it into our database which will be created automatically. You might also want to take note on how clean GET url is achieved.</h3>
			<h2>Books recently purchased</h2>
		</article>
		<tbody>
			<table>
			{foreach item=book from=$books}
				<tr style="color: green;">
					<td>{$book.id}</td>
					<td>{urldecode($book.title)}</td>
					<td>{$book.category}</td>
					<td>{$book.type}</td>
					<td>{urldecode($book.author)}</td>
					<td>${$book.price}</td>
					<td><a href="{$delete}{$book.id}">[Delete Record]</a></td>
				</tr>
			{foreachelse}
				<tr style="color: red;">
					<td>No books sold yet :(</td>
				</tr>	
			{/foreach}
			</table>
		</tbody>	
	</div>
	<div class="column grid_6">
		<article>
			<h2>Book Specials</h2>
			<h3>The Robot Rapist</h3>
			<p><a href="{$special1}">[Purchase Book for $3.99]</a></p>
			<h3>Getting hard again</h3>
			<p><a href="{$special2}">[Purchase Book for $2.99]</a></p>
			<h3>Cinderella goes rogue</h3>
			<p><a href="{$special3}">[Purchase Book for $8.99]</a></p>
		</article>
	</div>
</div>			