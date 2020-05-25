<?php $this->layout('layouts/default') ?>

<?php $this->block('content') ?>
	<div class="uk-text-center uk-margin-small">
		<h1 class="uk-heading-primary uk-margin-remove">Delete Link</h1>
	</div>

	<div class="uk-text-center">
		<p>
			Did a mistake? Don't need the shortened link anymore?
			Just enter the <b>delete key</b> you've received when creating the shortened link below!
		</p>
	</div>

	<form method="post">
		<?= $this->csrf()->getTokenField() ?>

		<fieldset class="uk-fieldset">
			<div class="uk-margin-small">
				<div class="uk-inline uk-width-1-1">
					<span class="uk-form-icon uk-form-icon-flip" uk-icon="trash"></span>
					<input class="uk-input uk-form-large uk-border-pill" placeholder="Delete Key" type="text"
					       name="delete_key" required maxlength="<?= Flight::get('shortener.delete_key_length') ?>" value="<?= isset($deleteKey) ? $deleteKey : '' ?>">
				</div>
			</div>

			<?php if (isset($error)): ?>
				<div class="uk-text-center">
					<p class="uk-text-danger"><?= $error ?></p>
				</div>
			<?php endif; ?>

			<div class="uk-margin">
				<button type="submit" class="uk-button uk-button-danger uk-border-pill uk-width-1-1">DELETE</button>
			</div>
		</fieldset>
	</form>

	<div>
		<ul class="uk-subnav uk-flex-center" uk-margin>
			<li><a href="/" class="uk-link-reset uk-text-small"><span uk-icon="arrow-left"></span> Back to Home</a></li>
		</ul>
	</div>
<?php $this->endblock() ?>