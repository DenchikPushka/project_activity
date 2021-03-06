<?php
	$user = getUser();
	if (empty($user) || $user->user_type != 1) {
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

	$model_tasks = getModel('tasks');
	$tasks = $model_tasks->getTasksByKid($proj_id, $user_id);
?>
<div class="container">
	<h3>Задания <a href="index.php?view=database&id=<?= $project->id ?>" class="btn btn-success">Перейти к таблицам <i class="fas fa-database"></i></a></h3>
	<?php if (!empty($tasks)) { ?>
		<table class="table table-hover">
		<tbody>
		<?php foreach ($tasks as $task) {
			if ($task->closed) {
				$tr_style = 'style="background: rgba(149, 195, 97, 0.6);"';
				$status = '<i class="fas fa-check"></i> Выполнено';
			} else {
				$tr_style = 'style="background: rgba(240, 173, 78, 0.8);"';
				$status = 'В работе';
			}
			if (!empty($task->description)) {
				$description = $task->description;
			} else {
				$description = 'Описание отсутствует';
			}
		?>
			<tr class="tr_task" data-task_id="<?= $task->id ?>" <?= $tr_style; ?>><th><?= $task->name; ?> <i class="fas fa-angle-right"></i></th><th style="text-align: right;"><?= $status; ?></th></tr>
			<tr class="tr_descriptions_of_tasks" data-task_id="<?= $task->id ?>" style="display: none;"><td colspan="2"><pre style="max-width: 1124px; word-wrap: break-word; white-space: pre-wrap;"><?= $description; ?></pre></td></tr>
		<?php
			}
		?>
		</tbody>
		</table>
	<?php } else {
		echo '<p>Задания не добавлены учителем</p>';
	} ?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {

		document.getElementById('page_title').innerHTML = '<?= $project->name; ?>';

		jQuery('.tr_task').click(function() {
			var task_id = this.getAttribute('data-task_id');
			var elems = jQuery('.tr_descriptions_of_tasks[data-task_id="'+task_id+'"]');
			var fas = this.getElementsByClassName('fas')[0];
			if (elems.length > 0) {
				if (elems[0].style.display === 'none') {
					elems.css('display', 'table-row');
					fas.classList.remove('fa-angle-right');
					fas.classList.add('fa-angle-down');
				}
				else {
					elems.css('display', 'none');
					fas.classList.remove('fa-angle-down');
					fas.classList.add('fa-angle-right');
				}
			}
		});

	});
</script>