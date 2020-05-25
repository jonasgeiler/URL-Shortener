<?php $this->layout('layouts/default') ?>

<?php $this->block('content') ?>
	<div class="uk-text-center uk-margin">
		<h1 class="uk-heading-primary">URL-Shortener</h1>
	</div>

	<form method="post" class="uk-form-horizontal">
		<?= $this->csrf()->getTokenField() ?>

		<fieldset class="uk-fieldset">
			<div class="uk-margin-small">
				<div class="uk-inline uk-width-1-1">
					<span class="uk-form-icon uk-form-icon-flip" uk-icon="world"></span>
					<input class="uk-input uk-form-large uk-border-pill" placeholder="Shorten your link" type="url" id="link-input"
					       name="link" required value="<?= isset($link) ? $link : '' ?>">
				</div>
			</div>

			<div class="uk-margin-small">
				<label class="uk-form-label" for="custom-id-input">Custom ID (Optional):</label>
				<div class="uk-form-controls">
					<div class="uk-inline uk-width-1-1">
						<span class="uk-form-icon uk-form-icon-flip" uk-icon="hashtag"></span>
						<input class="uk-input uk-border-pill" placeholder="ID" type="text" maxlength="250" id="custom-id-input"
						       name="custom_id" pattern="[A-Za-z0-9-]+" value="<?= isset($customID) ? $customID : '' ?>">
					</div>
				</div>
			</div>

			<div class="uk-margin-small">
				<label class="uk-form-label" for="expires-select">Expires:</label>
				<div class="uk-form-controls">
					<select class="uk-select uk-border-pill" name="expires" id="expires-select">
						<option value="0" <?= isset($expires) && intval($expires) <= 0 ? 'selected' : '' ?>>Never</option>
						<option value="1" <?= isset($expires) && intval($expires) === 1 ? 'selected' : '' ?>>In 1 hour</option>
						<option value="3" <?= isset($expires) && intval($expires) === 3 ? 'selected' : '' ?>>In 3 hours</option>
						<option value="6" <?= isset($expires) && intval($expires) === 6 ? 'selected' : '' ?>>In 6 hours</option>
						<option value="12" <?= isset($expires) && intval($expires) === 12 ? 'selected' : '' ?>>In 12 hours</option>
						<option value="24" <?= isset($expires) && intval($expires) === 24 ? 'selected' : '' ?>>In 24 hours</option>
						<option value="48" <?= isset($expires) && intval($expires) >= 48 ? 'selected' : '' ?>>In 48 hours</option>
					</select>
				</div>
			</div>

			<?php if (isset($error)): ?>
				<div class="uk-text-center">
					<p class="uk-text-danger"><?= $error ?></p>
				</div>
			<?php endif; ?>

			<div class="uk-margin">
				<button type="submit" class="uk-button uk-button-primary uk-border-pill uk-width-1-1">SHORTEN</button>
			</div>
		</fieldset>
	</form>

	<div>
		<ul class="uk-subnav uk-subnav-divider uk-flex-center" uk-margin>
			<li><a href="/delete" class="uk-link-reset uk-text-small">Delete Link</a></li>
			<li><a href="/report" class="uk-link-reset uk-text-small">Report Link</a></li>
			<?php if (Flight::auth()->authorized && Flight::auth()->isAdmin): ?>
				<li><a href="/mod" class="uk-link-reset uk-text-small">Moderation</a></li>
			<?php endif; ?>
		</ul>
	</div>
<?php $this->endblock() ?>