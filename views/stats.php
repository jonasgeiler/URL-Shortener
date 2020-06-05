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

<?php if (count($clicksOverTime) !== 0): ?>
	<div class="uk-card uk-card-small uk-card-default uk-margin">
		<div class="uk-card-header">
			<div class="uk-grid-small uk-flex-middle" uk-grid>
				<div class="uk-width-auto uk-text-right">
					<span uk-icon="history"></span>
				</div>
				<div class="uk-width-expand"><h4 class="uk-card-title">Clicks Over Time</h4></div>
			</div>
		</div>
		<div class="uk-card-body">
			<canvas id="click-chart"></canvas>
		</div>
	</div>
<?php endif; ?>

<div>
	<ul class="uk-subnav uk-flex-center" uk-margin>
		<li><a href="/" class="uk-link-reset uk-text-small"><span uk-icon="arrow-left"></span> Back to Home</a></li>
	</ul>
</div>
<?php $this->endblock() ?>

<?php $this->block('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
<script type="text/javascript">
	new Chart('click-chart', {
		type:    'bar',
		data:    {
			labels:   [
				<?php
				foreach ($clicksOverTime as $click) {
					echo "'" . $click['month'] . "',";
				}
				?>
			],
			datasets: [
				{
					label:           'Clicks',
					data:            [
						<?php
						foreach ($clicksOverTime as $click) {
							echo $click['clicks'] . ',';
						}
						?>
					],
					backgroundColor: '#1e87f0'
				}
			]
		},
		options: {
			responsive:                  true,
			maintainAspectRatio:         false,
			responsiveAnimationDuration: 500,
			legend:                      {
				display: false
			},
			animation:                   {
				duration: 2000
			}
		}
	});

</script>
<?php $this->endblock() ?>
