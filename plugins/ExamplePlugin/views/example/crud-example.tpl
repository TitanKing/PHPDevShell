<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="column grid_6">
			{if $id != ''}
            <p>
                <label>ID
                    <input type="text" size="5" name="id" value="{$id}" readonly>
                </label>
            </p>
			{/if}
            <p>
                <label>Name (Can only contain alpha)
                    <input type="text" size="20" name="example_name" value="{$example_name}" required="required">
                </label>
				<label>Email (Must be an email)
                    <input type="email" size="20" name="example_email" value="{$example_email}" required="required">
                </label>
				<label>Your Website (Must be a URL)
                    <input type="url" size="20" name="example_url" value="{$example_url}" required="required">
                </label>
            </p>
			<p>
				<label>Select Example
					<select name="example_select">
						{$example_select}
					</select>
				</label>
			</p>
			<p>
				<label>Multi Select Example
					<select name="example_multi_select[]" multiple="multiple" size="3">
						{$example_multi_select}
					</select>
				</label>
				<label>Checkboxes<br></label>
				{$example_checkbox1}
				{$example_checkbox2}
				<label>Radio Buttons<br></label>
				{$example_radio}
			</p>
            <p>
                <button type="submit" name="submit_example" value="submit_example"><span class="save"></span><span>Save Example</span></button>
                <button type="submit" name="new" value="new"><span class="new"></span><span>New Example</span></button>
                <button type="reset"><span class="reset"></span><span>Reset Example</span></button>
            </p>
        </div>
		<div class="column grid_6">
            <p>
                <label>Notes (Minimum 20 Characters)
                    <textarea rows="5" cols="40" name="example_note">{$example_note}</textarea>
                </label>
            </p>
            <p>
                <label>Alias (Must all be lower case)
                    <input type="text" size="20" name="example_alias" value="{$example_alias}">
                </label>
            </p>
			<table>
				<thead>
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>URL</th>
					</tr>
				</thead>
				<tbody>
					{foreach item=r from=$results}
					<tr>
						<td>
							<strong>{$r.example_name}</strong>
						</td>
						<td>
							<strong>{$r.example_email}</strong>
						</td>
						<td>
							<strong><a href="{$r.example_url}">{$r.example_url}</a></strong>
						</td>
						<td>
							<a href="?edit={$r.id}" class="button">Edit</a>
						</td>
						<td>
							<a href="?delete={$r.id}" class="button">Delete</a>
						</td>
					</tr>
					{foreachelse}
					<h3>Nothing posted yet...</h3>
					{/foreach}
				</tbody>		
			</table>	
        </div>
    </div>
</form>
