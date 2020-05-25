<?php $this->layout('layouts/default') ?>

<?php $this->block('content') ?>
<div class="uk-text-center uk-margin">
	<h1 class="uk-heading-primary">Your Shortened Link:</h1>
</div>

<div class="uk-form-horizontal">
	<fieldset class="uk-fieldset">
		<div class="uk-margin-small" title="<?= $link ?>">
			<div class="uk-inline uk-width-1-1">
				<a onclick="copy('shortened-link')" class="uk-form-icon uk-form-icon-flip" uk-icon="copy"></a>
				<input id="shortened-link" readonly="readonly" class="uk-input uk-form-large uk-border-pill" type="url"
				       onclick="select('shortened-link')" value="https://<?= Flight::get('shortener.domain') ?>/<?= $id ?>">
			</div>
		</div>

		<div class="uk-margin-small" title="Use this key to delete the shortened link.">
			<label class="uk-form-label" for="custom-id-input">Delete Key:</label>
			<div class="uk-form-controls">
				<div class="uk-inline uk-width-1-1">
					<a onclick="copy('delete-key')" class="uk-form-icon uk-form-icon-flip" uk-icon="copy"></a>
					<input id="delete-key" readonly="readonly" class="uk-input uk-border-pill" type="text"
					       onclick="select('delete-key')" value="<?= $deleteKey ?>">
				</div>
			</div>
		</div>

		<div class="uk-margin">
			<a href="/<?= $id ?>/stats" class="uk-button uk-button-primary uk-border-pill uk-width-1-1">STATISTICS PAGE</a>
		</div>
	</fieldset>
</div>

<div>
	<ul class="uk-subnav uk-flex-center" uk-margin>
		<li><a href="/" class="uk-link-reset uk-text-small"><span uk-icon="arrow-left"></span> Back to Home</a></li>
	</ul>
</div>

<textarea id="clipboard-text" readonly="readonly" style="position: absolute; left: -9999px;"></textarea>
<?php $this->endblock() ?>

<?php $this->block('scripts') ?>
<script type="application/javascript">
	function copy(id) {
		var input = document.getElementById(id);
		setClipboard(input.value);
		input.onclick();
	}

	function select(id) {
		var input = document.getElementById(id);
		input.select();
		input.setSelectionRange(0, input.value.length);
	}

	function setClipboard(text) {
		// Use the Async Clipboard API when available
		if (navigator.clipboard) return navigator.clipboard.writeText(text);

		var textArea = document.getElementById('clipboard-text');
		textArea.value = text;

		var selected = document.getSelection().rangeCount > 0 ? document.getSelection().getRangeAt(0) : false;

		textArea.select();

		var success = false;
		try {
			success = document.execCommand('copy');
		} catch (e) {
		}

		if (selected) {
			document.getSelection().removeAllRanges();
			document.getSelection().addRange(selected);
		}

		return success
			? Promise.resolve()
			: Promise.reject();
	}
</script>
<?php $this->endblock() ?>
