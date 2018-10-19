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

	$model_users = getModel('users');
	$kids = $model_users->getKids();
?>
<div class="container">
	<div class="modal_container">
		<div class="modal_window">
			<label for="group_name">Имя группы:</label><input class="form-control" id="group_name" type="text">
			<div style="min-width: 200px; height: 360px; overflow-y: auto; padding: 10px 10px; border: 1px solid black; border-radius: 4px;">
			<?php
				$class_name; 
				foreach ($kids as $kid) {
					if (!empty($class_name) && $class_name != $kid->class_name) {
						echo '<hr style="margin: 5px;">';
					}
			?>
				<input type="checkbox" class="form-check-input kids_check" data-kid_id="<?= $kid->user_id; ?>"> <label> <?= $kid->class_name.' '.$kid->user_name; ?></label><br>
			<?php
					$class_name = $kid->class_name;
				}
			?>
			</div><br>
			<center><button class="btn btn-success" id="btn_save_group">Сохранить <i class="fas fa-save"></i></button>
				<button class="btn btn-danger" id="btn_cancel_group">Отменить <i class="fas fa-undo"></i></button></center>
		</div>
	</div>
	<center><h2><?= $project->name; ?></h2></center>
	<h3>Группы <button class="btn btn-success" id="btn_add_group">Создать группу <i class="fas fa-plus"></i></button></h3>
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
		var elem_add_group = document.getElementById('btn_add_group');
		if (elem_add_group) {
			elem_add_group.onclick = function() {
				jQuery('.modal_container').show();
			};
		}

		var elem_save_group = document.getElementById('btn_save_group');
		if (elem_save_group) {
			elem_save_group.onclick = saveGroup;
		}

		var elem_cancel_group = document.getElementById('btn_cancel_group');
		if (elem_cancel_group) {
			elem_cancel_group.onclick = function() {
				jQuery('.modal_container').hide();
			};
		}

		function saveGroup() {
			if (document.getElementById('group_name').value.trim() === '') {
				noty({
	                timeout: 2000,
	                theme: 'relax',
	                layout: 'topCenter',
	                maxVisible: 5,
	                type: 'warning',
	                text: 'Пустое имя группы'
	            });
	            document.getElementById('group_name').focus();
				return;
			}

			var kids_check = jQuery('.kids_check'),
				data = [];
			for (var i = 0; i < kids_check.length; i++) {
				if (kids_check[i].checked) {
					data.push(kids_check[i].getAttribute('data-kid_id')-0);
				}
			}
			console.log(data);
			jQuery.ajax({
		        type: 'POST',
		        url: 'index.php?task=groups.addNewGroup',
		        data: {
		            data: JSON.stringify(data),
		            name: document.getElementById('group_name').value,
		            project_id: <?= $proj_id; ?>
		        },
		        success: function(data) {
		        	//console.log(data);
		        	if (data !== 0) {
		        		location.reload();
		        	} else {
		        		noty({
			                timeout: 2000,
			                theme: 'relax',
			                layout: 'topCenter',
			                maxVisible: 5,
			                type: 'warning',
			                text: 'Группа не создана, убедитесь что ученики выбранны'
			            });
		        	}
		        },
		        dataType: 'json',
		        async: true,
		        timeout: 10000,
		        error: function(data) {
		        	console.log(data);
		            noty({
		                timeout: 2000,
		                theme: 'relax',
		                layout: 'topCenter',
		                maxVisible: 5,
		                type: 'error',
		                text: 'Ошибка!'
		            });
		        }
		    });
		}

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