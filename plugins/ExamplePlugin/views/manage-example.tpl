<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="column grid_6">
			{if $id != ''}
            <p>
                <label>{_e('Example ID')}
                    <input type="text" size="5" name="id" value="{$id}" readonly title="{_e('This is some example id.')}">
                </label>
            </p>
			{/if}
            <p>
                <label>{_e('Example Name')}
                    <input type="text" size="20" name="example_name" value="{$example_name}" title="{_e('Provide some example name.')}">
                </label>
            </p>
			<p>
				<label>Multi Select Example
					<select name="example_multi_select_crud[]" multiple="multiple" size="3">
						{$example_multi_select_crud}
					</select>
				</label>
			</p>	
            <p>
                <button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Example')}</span></button>
                <button type="reset"><span class="reset"></span><span>{_e('Reset Example')}</span></button>
				<button type="submit" name="new" value="new"><span class="new"></span><span>{_e('New Example')}</span></button>
            </p>
        </div>
		<div class="column grid_6">
            <p>
                <label>{_e('Example Notes')}
                    <textarea rows="5" cols="40" name="example_note" title="{_e('Provide some notes for this example.')}">{$example_note}</textarea>
                </label>
            </p>
            <p>
                <label>{_e('Alias')}
                    <input type="text" size="20" name="alias" value="{$alias}" title="{_e('Some alias for this example.')}">
                </label>
            </p>
        </div>
    </div>
</form>
