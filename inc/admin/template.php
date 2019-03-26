<div class="wrap">
	<h1>River of Ebooks API credentials</h1>

	<p>
		Register an application here: <a href="http://ec2-18-219-223-27.us-east-2.compute.amazonaws.com/keys"><?php echo ROE_BASE_URL; ?>/keys</a>.
		<br />
		Once it has been whitelisted, your users will be able to use the AppID and secret to publish ebooks to the River of Ebooks.
	</p>

	<form method="post" action="edit.php?action=roepressbooksaction">
		<?php wp_nonce_field( 'roe-validate' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="some_field">AppID</label></th>
				<td>
					<input name="roe_pressbooks_key" class="regular-text" type="text" id="roe-key" value="<?php echo get_site_option('roe_pressbooks_key') ?>" />
					<p class="description">The AppID of the application you registered.</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="some_field">Application secret</label></th>
				<td>
					<input name="roe_pressbooks_secret" class="regular-text" type="password" id="roe-secret" value="<?php echo get_site_option('roe_pressbooks_secret') ?>" />
					<p class="description">The secret of the application you registered.</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
