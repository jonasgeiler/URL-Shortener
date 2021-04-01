<?php $this->layout('layouts/default', ['largeWidth' => true]) ?>

<?php $this->block('content') ?>
<div class="uk-text-center uk-margin">
	<p class="uk-margin-remove uk-text-uppercase">Created on <?= $createdTime ?></p>
	<h1 class="uk-heading-primary uk-margin-remove-top uk-margin-small-bottom uk-text-truncate"><?= $linkTitle ?></h1>
	<a class="uk-link-reset uk-text-break" target="_blank" href="<?= $link ?>"><span uk-icon="link"></span> <?= $link ?></a>
</div>

<div class="uk-width-large uk-margin-auto">
	<div class="uk-grid uk-grid-divider uk-grid-medium uk-child-width-1-2 uk-margin" uk-grid>
		<div class="uk-flex-right">
			<span class="uk-text-small"><span uk-icon="forward" class="uk-margin-small-right"></span>Clicks</span>
			<h1 class="uk-heading-primary uk-margin-remove uk-text-primary"><?= number_format($totalClicks, 0, ',', '.') ?></h1>
		</div>
		<div title="<?= $shares['tooltip'] ?>" uk-tooltip>
			<span class="uk-text-small"><span uk-icon="social" class="uk-margin-small-right"></span>Shares</span>
			<h1 class="uk-heading-primary uk-margin-remove uk-text-primary"><?= $shares['count'] ?></h1>
		</div>
	</div>
</div>

<div>
	<ul class="uk-subnav uk-flex-center" uk-margin>
		<li><a href="/" class="uk-link-reset uk-text-small"><span uk-icon="arrow-left"></span> Back to Home</a></li>
	</ul>
</div>
<?php $this->endblock() ?>