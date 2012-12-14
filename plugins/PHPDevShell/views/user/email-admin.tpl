<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="span4">
			<fieldset>
				<legend>Contact</legend>
				<p>
					{_e('Priority')}<br>
					{$priority}
				</p>
				{if $id == 0}
				<p>
					<label>{_e('Identified By')}
						<input type="text" size="50" id="name" name="name" value="{$name}" required="required" title="{_e('Please provide your name so we can identify you.')}">
					</label>
				</p>
				{else}
				<input type="hidden" name="name" value="{$name}">
				{/if}
				{if $user_email == false}
				<p>
					<label>{_e('Your Email')}
						<input type="text" size="50" id="email_from" name="email_from" value="{$email_from}" required="required" title="{_e('This is the source email address of the user who sent the email.')}">
					</label>
				</p>
				{else}
				<input type="hidden" name="email_from" value="{$user_email}">
				{/if}
				<p>
					<label>{_e('Subject')}
						<input type="text" size="50" name="subject" value="{$subject}" required="required" title="{_e('Please provide an appropriate subject to your message. The subject should clearly state what your query is about.')}">
					</label>
				</p>
				<p>
					<label>{_e('Message')}
						<textarea name="message" rows="10" cols="60" title="{_e('In as much detail as possible please state your query.')}">{$message}</textarea>
					</label>
				</p>
			</fieldset>
		</div>
		<div class="span4">
			<fieldset>
				<legend>{_e('Type')}</legend>
				<p>
					{_e('Query Type')}<br>
					{$query_type}
				</p>
			</fieldset>
			{$botBlockFields}
		</div>
		<div class="span4">
			<fieldset>
				<legend>{_e('Submit')}</legend>
				<p>
					<button type="submit" name="send_mail" value="send_mail"><span class="submit"></span><span>{_e('Send Email')}</span></button>
					<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
				</p>
			</fieldset>
		</div>
    </div>
</form>
