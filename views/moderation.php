<?php $this->layout('layouts/default', ['largeWidth' => true]) ?>

<?php $this->block('content') ?>
<div class="uk-text-center uk-margin">
	<h1 class="uk-heading-primary uk-margin-remove">Moderation</h1>
	<p class="uk-margin-remove">I have already created <?= $linksCreated ?> links!</p>
</div>

<?php if (empty($reportedLinks)): ?>
	<div class="uk-text-center">
		<p class="uk-text-large">
			No reported links!
		</p>
	</div>
<?php else: ?>
	<form method="post">
		<?= $this->csrf()->getTokenField() ?>

		<div class="uk-overflow-auto uk-margin-bottom">
			<table class="uk-table uk-table-middle uk-table-divider uk-table-responsive">
				<thead>
					<tr>
						<th class="uk-table-shrink"></th>
						<th class="uk-width-small">ID</th>
						<th class="uk-width-xlarge">Link</th>
						<th class="uk-table-shrink">Expires</th>
						<th class="uk-table-shrink">Created</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($reportedLinks as $link): ?>
						<tr>
							<td><input class="uk-checkbox" type="checkbox" name="selected_ids[<?= $link['id'] ?>]"></td>
							<td class="uk-text-truncate"><b><?= $link['id'] ?></b></td>
							<td class="uk-table-link uk-text-truncate uk-text-nowrap">
								<a class="uk-link-reset" href="<?= $link['link'] ?>" title="<?= $link['link'] ?>"><?= $link['link'] ?></a>
							</td>
							<td class="uk-text-nowrap"><?= $link['expires'] ?></td>
							<td class="uk-text-nowrap"><?= $link['created'] ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<?php if (isset($error) && $error): ?>
			<div class="uk-text-center">
				<p class="uk-text-danger"><?= $error ?></p>
			</div>
		<?php endif; ?>

		<div class="uk-margin uk-grid-small uk-flex-center" uk-grid>
			<div>
				<input type="submit" name="action" class="uk-button uk-button-primary uk-border-pill uk-width-1-1" value="APPROVE SELECTED">
			</div>
			<div>
				<input type="submit" name="action" class="uk-button uk-button-danger uk-border-pill uk-width-1-1" value="DELETE SELECTED">
			</div>
		</div>
	</form>
<?php endif; ?>

<div>
	<ul class="uk-subnav uk-flex-center" uk-margin>
		<li><a href="/" class="uk-link-reset uk-text-small"><span uk-icon="arrow-left"></span> Back to Home</a></li>
	</ul>
</div>
<?php $this->endblock() ?>
