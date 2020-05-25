<?php $this->layout('layouts/default') ?>

<?php $this->block('content') ?>
	<div class="uk-text-center uk-margin-small">
		<h1 class="uk-heading-primary uk-margin-remove">Report Link</h1>
	</div>

	<div class="uk-text-center">
		<p>
			Found a shortened link with <b>illegal</b>, <b>disturbing</b> or <b>pornographic</b> content?
			Just <b>report</b> it here and I'll make sure to delete it as soon as possible!
			Please enter the ID of the link (the part after <i>https://<?= Flight::get('shortener.domain') ?>/</b></i>) below.
		</p>
	</div>

	<form method="post">
		<?= $this->csrf()->getTokenField() ?>

		<fieldset class="uk-fieldset">
			<div class="uk-margin-small">
				<div class="uk-inline uk-width-1-1">
					<span class="uk-form-icon uk-form-icon-flip" uk-icon="hashtag"></span>
					<input class="uk-input uk-form-large uk-border-pill" placeholder="ID" type="text"
					       name="id" required maxlength="250" pattern="[A-Za-z0-9-]+" value="<?= isset($id) ? $id : '' ?>">
				</div>
			</div>

			<?php if (isset($error)): ?>
				<div class="uk-text-center">
					<p class="uk-text-danger"><?= $error ?></p>
				</div>
			<?php endif; ?>

			<div class="uk-margin">
				<button type="submit" class="uk-button uk-button-secondary uk-border-pill uk-width-1-1">REPORT</button>
			</div>
		</fieldset>
	</form>

	<div>
		<ul class="uk-subnav uk-flex-center" uk-margin>
			<li><a href="/" class="uk-link-reset uk-text-small"><span uk-icon="arrow-left"></span> Back to Home</a></li>
		</ul>
	</div>
<?php $this->endblock() ?>