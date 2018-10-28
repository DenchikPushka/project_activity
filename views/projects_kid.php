<?php
	$user = getUser();
	if (empty($user) || $user->user_type != 1) {
		header('location: index.php?view=main_page');
		exit();
	}
	$user_id = $user->id;
	$model_projects = getModel('projects');
	$items = $model_projects->getProjectsByKid($user_id);
?>
<div class="container">
	<div class="modal_container">
		<div class="modal_window">
		</div>
	</div>
	<center><h2>Проекты</h2></center>
	<?php if (!empty($items)) { ?>
		<table class="table table-hover">
		<thead>
			<th>Название</th>
			<th>Описание</th>
		</thead>
		<tbody>
		<?php foreach ($items as $item) {
			if (empty($item->description)) {
				$proj_description = '-';
			}
			else {
				$proj_description = $item->description;
			}
		?>
			<tr class="tr_href" data-href="index.php?view=projectcard_kid&id=<?= $item->id ?>"><td><?= $item->name; ?></td><td><textarea class="form-control area_openable" style="resize: none; cursor: pointer; background: white;" readonly><?= $item->description; ?></textarea></td></tr>
		<?php } ?>
		</tbody>
		</table>
	<?php } else {
		echo '<p>Нет доступных проектов</p>';
	} ?>
</div>