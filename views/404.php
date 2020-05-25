<?php $this->layout('layouts/default') ?>

<?php $this->block('content') ?>
	<div class="uk-text-center uk-margin-small">
		<h1 class="uk-heading-primary uk-margin-remove">404</h1>
	</div>

	<div class="uk-text-center">
		<p class="uk-text-large">NOT FOUND</p>
	</div>

	<div>
		<ul class="uk-subnav uk-flex-center" uk-margin>
			<li><a href="/" class="uk-link-reset uk-text-small"><span uk-icon="arrow-left"></span> Go to Home</a></li>
		</ul>
	</div>
<?php $this->endblock() ?>