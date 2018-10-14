<?php
	$user = getUser();
	if (empty($user) || $user->user_type != 2) {
		header('location: index.php?view=main_page');
		exit();
	}
	$user_id = $user->id;
	$model_projects = getModel('projects');
	$items = $model_projects->getData("`creator_id` = $user_id");
?>
<div class="container">
	<center><h2>Проекты</h2></center>
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
			<tr class="tr_href" data-href="index.php?view=projectcard_teacher&id=<?= $item->id ?>"><td><?= $item->name; ?></td><td><?= $proj_description; ?></td></tr>
		<?php } ?>
		</tbody>
	</table>
</div>