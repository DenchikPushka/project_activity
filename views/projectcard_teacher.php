<?php
	$user = getUser();
	if (empty($user) || $user->user_type != 2) {
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
	}
	else {
		$project = $items[0];
	}
?>
<div class="container">
	<center>
		<p><a href="index.php?view=groups&id=<?= $project->id ?>" class="btn btn-lg btn-success" style="width: 200px;">Группы</a></p>
		<p><a href="index.php?view=tasks&id=<?= $project->id ?>" class="btn btn-lg btn-success" style="width: 200px;">Задания</a></p>
		<p><a href="index.php?view=database&id=<?= $project->id ?>" class="btn btn-lg btn-success" style="width: 200px;">База данных</a></p>
		<p><a href="index.php?view=history&id=<?= $project->id ?>" class="btn btn-lg btn-success" style="width: 200px;">История</a></p>
	</center>
</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
		document.getElementById('page_title').innerHTML = '<?= $project->name; ?>';
	});
</script>