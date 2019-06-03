<?php
	$user = getUser();
	if (empty($user)) {
		header('location: index.php?view=main_page');
		exit();
	}
	if (empty($_GET['id'])) {
		throw new Exception('Empty id', 500);
	}
	$proj_id = $_GET['id'];
	if (preg_match('/[^\d]/', $proj_id)) {
		throw new Exception('Invalid id', 500);
	}
	$user_id = $user->id;
	$model_projects = getModel('projects');
	$items = $model_projects->getData("`id` = $proj_id");
	if (empty($items)) {
		throw new Exception('Project not found', 500);
	} else {
		$project = $items[0];
	}

	$model_database = getModel('database');
	$entities = $model_database->getEntitiesHistory($proj_id);
?>
<div class="container">
	<h3>История</h3>
	<p>(при удалении строки из таблицы, удаляется соответствующая запись из истории)</p>
	<?php if (!empty($entities)) { ?>
		<table class="table table-hover">
		<tbody>
		<?php foreach ($entities as $entity) { ?>
			<tr><td><?= $entity->create_time; ?></td><td><b><?= $entity->creator_name; ?></b> добавил(а) запись в таблицу <b><?= $entity->table_name; ?></b></td></tr>
		<?php } ?>
		</tbody>
		</table>
	<?php } else {
		echo '<p>Пусто</p>';
	} ?>
</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
		document.getElementById('page_title').innerHTML = '<?= $project->name; ?>';
	});
</script>
