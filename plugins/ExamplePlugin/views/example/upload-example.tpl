<!-- PHPDevShell uses a 12 Grid system called 1KB Grid. -->
<!-- This awesome grid gives you 12 colums without tables -->
<div class="row">
	<div class="column grid_6">
		<article>
			<h1>
				Hi {$developers_name}, welcome to the ExamplePlugins upload example.
			</h1>
            <h3>
                This example is not as much on how to upload files as it is to show you how plugins work in PHPDevShell.
            </h3>
            <p>
                When installing a plugin it shares its resources within the classes registry. What happens for instance, if you call:
                <code>
                    $filemanager = $this->factory('fileManager');
                </code>
                This tells PHPDevShell to look at autoload location and the plugin class registry. If its located it loads and initiates the object.
                The cool thing about this is, that if you develop your own plugin with a class alias of 'fileManager' it will override the default one and use yours if it precedes it.
            </p>
            <h2>
                Pick some files to upload,
            </h2>
            <h3>
                You can upload the following extention types, 
            </h3>
			<form action="{$self_url}" method="post" enctype="multipart/form-data">
				<p>
					<input name="file1" size="45" type="file">
					<input name="file2" size="45" type="file">
					<input name="file3" size="45" type="file">
				</p>
				<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Upload Files')}</span></button>
			</form>
		</article>
	</div>
	<div class="column grid_6">
		<article>
            <h2>
                This is the files you have uploaded. It stores them neatly in the file upload registry for easy location.
            </h2>
		</article>
		<table class="floatHeader">
			<thead>
				<tr>
					<th>Extention</th>
					<th>Original Filename</th>
					<th>File Download</th>
					<th>File Size</th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody>
				{section name=files loop=$u_arr}
				{strip}
				<tr>
					<td>
						{$u_arr[files].extention_img}
					</td>
					<td>
						<h3>{$u_arr[files].original_filename}</h3>
					</td>
					{if $u_arr[files].thumbnail == false}
					<td>
						<h3><a href="{$absolute_url}{$u_arr[files].download_file}">{$u_arr[files].original_filename}</a></h3>
					</td>
					{else}
					<td>
						<a href="{$absolute_url}{$u_arr[files].download_file}"><img src="{$u_arr[files].thumbnail}" alt="{$u_arr[files].original_filename}" title="{$u_arr[files].original_filename}" /></a>
					</td>
					{/if}
					<td>
						<h3>{$u_arr[files].format_file_size}</h3>
					</td>
					<td>
						{$u_arr[files].delete_file}
					</td>
				</tr>
				{/strip}
				{sectionelse}
				<tr>
					<td colspan="2">
						<h2>No files uploaded yet...</h2>
					</td>
				</tr>
				{/section}
			</tbody>
		</table>
	</div>
</div>