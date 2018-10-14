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
	$items = $model_projects->getData("`id` = $proj_id AND `creator_id` = $user_id");
	if (empty($items)) {
		throw new Exception('Project not found', 500);
	}
	else {
		$project = $items[0];
	}

	$model_groups = getModel('groups');
	$groups = $model_groups->getData("`project_id` = $proj_id");
	$users_of_groups = $model_groups->getUsersAndGroupsByProjectId($proj_id);
?>
<div class="container">
	<center><h2><?= $project->name; ?></h2></center>
	<h3>Группы</h3>
	<table class="table table-hover">
		<tbody>
		<?php foreach ($groups as $group) { ?>
			<tr class="tr_group" data-group_id="<?= $group->id ?>"><th colspan="2"><?= $group->name ?> <i class="fas fa-angle-right"></i></th></tr>
			<?php foreach ($users_of_groups as $item) {
					if ($item->group_id == $group->id) { ?>
						<tr class="tr_user_of_group" data-group_id="<?= $group->id ?>" style="display: none;"><td><?= $item->name ?></td><td><?= $item->classname ?></td></tr>
				<?php }
				}
			}
		?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.tr_group').click(function() {
			var group_id = this.getAttribute('data-group_id');
			var elems = jQuery('.tr_user_of_group[data-group_id="'+group_id+'"]');
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