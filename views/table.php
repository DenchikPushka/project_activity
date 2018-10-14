<?php
	$user = getUser();
	if (empty($user)) {
		header('location: index.php?view=main_page');
		exit();
	}
	if (empty($_GET['id'])) {
		throw new Exception('Empty id', 500);
	}
	$table_id = $_GET['id'];
	if (preg_match('/[^\d]/', $table_id)) {
		throw new Exception('Invalid id', 500);
	}
	$user_id = $user->id;

	$model_database = getModel('database');
	$tables = $model_database->getTables("`id` = $table_id");
	if (!empty($tables)) {
		$table = $tables[0];
	} else {
		throw new Exception('Table not found', 500);
	}

	$model_projects = getModel('projects');
	$items = $model_projects->getData("`id` = $table->project_id AND `creator_id` = $user_id");
	if (empty($items)) {
		throw new Exception('Project not found', 500);
	}
	else {
		$project = $items[0];
	}

	$attributes = $model_database->getAttributes("`table_id` = $table_id");
	//$db_table = $model_database->getDataFromTable();
?>
<div class="container">
	<center><h2><?= $project->name; ?></h2></center>
	<h3><?= $table->name; ?></h3>
	<table class="table">
		<tr>
			<?php foreach ($attributes as $attr) { ?>
				<td><?= $attr->name; ?></td>
			<?php } ?>
		</tr>
		<tr>
			<?php foreach ($attributes as $attr) { ?>
				<td><input class="form-control" data-db_type="<?= $attr->type_id; ?>" type="text"></td>
			<?php } ?>
		</tr>
	</table>
	<button class="btn btn-success" id="btn_add_entity">Добавить строку <i class="fas fa-plus"></i></button>
</div>