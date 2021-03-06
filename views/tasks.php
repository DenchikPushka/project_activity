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

	$model_tasks = getModel('tasks');
	$tasks = $model_tasks->getData("`project_id` = $proj_id");
	$groups_of_tasks = $model_tasks->getGroupsAndTasksByProjectId($proj_id);

	$model_groups = getModel('groups');
	$groups = $model_groups->getData("`project_id` = $proj_id");
?>
<style type="text/css">
	.table>tbody>tr>td, .table>tbody>tr>th {
		vertical-align: middle;
	}
</style>
<div class="container">
	<div class="modal_container">
		<div class="modal_window">
			<label for="task_name">Название:</label><input class="form-control" id="task_name" type="text"><br>
			<label for="task_description">Описание:</label><textarea class="form-control" id="task_description" style="resize: none;"></textarea><br>
			<div style="min-width: 280px; height: 260px; overflow-y: auto; padding: 10px 10px; border: 1px solid black; border-radius: 4px;">
			<?php foreach ($groups as $group) { ?>
				<input type="checkbox" class="form-check-input groups_check" data-group_id="<?= $group->id; ?>"> <label>Группа "<?= $group->name; ?>"</label><br>
			<?php } ?>
			</div><br>
			<center><button class="btn btn-success" id="btn_save_task">Сохранить <i class="fas fa-save"></i></button>
				<button class="btn btn-danger" id="btn_cancel_task">Отменить <i class="fas fa-undo"></i></button></center>
		</div>
	</div>
	<h3>Задания <button class="btn btn-success" id="btn_add_task">Создать задание <i class="fas fa-plus"></i></button></h3>
	<?php if (!empty($tasks)) { ?>
		<table class="table table-hover">
		<tbody>
		<?php foreach ($tasks as $task) {
			$tr_style = '';
			if ($task->closed) {
				$tr_style = 'style="background: rgba(149, 195, 97, 0.6);"';
			}
		?>
			<tr class="tr_task" data-task_id="<?= $task->id; ?>" <?= $tr_style; ?>><th><?= $task->name; ?> <i class="fas fa-angle-right"></i></th><th><textarea class="form-control area_openable" style="resize: none; cursor: pointer; background: white;" readonly><?= $task->description; ?></textarea></th><th style="text-align: right;">
				<?php if (!$task->closed) { ?>
				<button class="btn btn-success btn_task_ready" data-task_id="<?= $task->id; ?>">Закрыть задачу <i class="fas fa-check"></i></button>
				<?php } else { ?>
				<button class="btn btn-primary btn_task_open" data-task_id="<?= $task->id; ?>">Открыть задачу <i class="fas fa-undo"></i></button>
				<?php } ?>
				<button class="btn btn-danger btn_delete_task" data-task_id="<?= $task->id; ?>" data-task_name="<?= trim(mb_ereg_replace('[^A-Za-zА-ЯЁа-яё\d\_\s]', '', $task->name)); ?>"><i class="fas fa-trash-alt"></i></button>
			</th></tr>
			<?php foreach ($groups_of_tasks as $item) {
					if ($item->task_id == $task->id) { ?>
						<tr class="tr_groups_of_tasks" data-task_id="<?= $task->id ?>" style="display: none;"><td colspan="3">Группа "<?= $item->group_name; ?>"</td></tr>
				<?php }
				}
			}
		?>
		</tbody>
		</table>
	<?php } else {
		echo '<p>Задания не добавлены</p>';
	} ?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {

		document.getElementById('page_title').innerHTML = '<?= $project->name; ?>';

		const ADD_TASK_CONT = jQuery('.modal_window')[0].innerHTML;

		var elem_add_task = document.getElementById('btn_add_task');
		if (elem_add_task) {
			elem_add_task.onclick = function() {
				jQuery('.modal_window')[0].innerHTML = ADD_TASK_CONT;

				if (jQuery('.groups_check').length === 0) {
					noty({
		                timeout: 2000,
		                theme: 'relax',
		                layout: 'topCenter',
		                maxVisible: 5,
		                type: 'warning',
		                text: 'Для создания заданий, необходимо создать группы'
		            });
		            jQuery('.modal_container').hide();
		            return;
				}

				var elem_save_task = document.getElementById('btn_save_task');
				if (elem_save_task) {
					elem_save_task.onclick = saveTask;
				}

				var elem_cancel_task = document.getElementById('btn_cancel_task');
				if (elem_cancel_task) {
					elem_cancel_task.onclick = function() {
						jQuery('.modal_container').hide();
					};
				}

				jQuery('.modal_container').show();
			};
		}

		jQuery('.btn_delete_task').click(function() {
			task_id = this.getAttribute('data-task_id');
			task_name = this.getAttribute('data-task_name');

			noty({
                theme: 'relax',
                layout: 'topCenter',
                type: 'default',
                modal: true,
                text: 'Вы действительно хотите удалить задание "'+task_name+'"?',
                killer: true,
                buttons: [
                    {
                        addClass: 'btn btn-warning', text: 'Удалить', onClick: function($noty) {
                            jQuery.ajax({
						        type: 'POST',
						        url: 'index.php?task=tasks.deleteTask',
						        data: {
						        	task_id: task_id
						        },
						        success: function(data) {
						        	location.reload();
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
                    },
                    {
                        addClass: 'btn btn-primary', text: 'Отмена', onClick: function($noty) {
                            $noty.close();
                        }
                    }
                ]
            });
			return false;
		});

		jQuery('.btn_task_ready').click(function() {
			task_id = this.getAttribute('data-task_id')-0;
			jQuery.ajax({
		        type: 'POST',
		        url: 'index.php?task=tasks.closeTask',
		        data: {
		            id: task_id
		        },
		        success: function(data) {
		        	location.reload();
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
		    return false;
		});

		jQuery('.btn_task_open').click(function() {
			task_id = this.getAttribute('data-task_id')-0;
			jQuery.ajax({
		        type: 'POST',
		        url: 'index.php?task=tasks.openTask',
		        data: {
		            id: task_id
		        },
		        success: function(data) {
		        	location.reload();
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
		    return false;
		});

		function saveTask() {
			if (document.getElementById('task_name').value.trim() === '') {
				noty({
	                timeout: 2000,
	                theme: 'relax',
	                layout: 'topCenter',
	                maxVisible: 5,
	                type: 'warning',
	                text: 'Пустое название'
	            });
	            document.getElementById('task_name').focus();
				return;
			}

			var groups_check = jQuery('.groups_check'),
				data = [];
			for (var i = 0; i < groups_check.length; i++) {
				if (groups_check[i].checked) {
					data.push(groups_check[i].getAttribute('data-group_id')-0);
				}
			}
			//console.log(data);
			jQuery.ajax({
		        type: 'POST',
		        url: 'index.php?task=tasks.addNewTask',
		        data: {
		            data: JSON.stringify(data),
		            name: document.getElementById('task_name').value,
		            description: document.getElementById('task_description').value,
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
			                text: 'Задание не создано, убедитесь что группы выбранны'
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

		jQuery('.tr_task').click(function() {
			var task_id = this.getAttribute('data-task_id');
			var elems = jQuery('.tr_groups_of_tasks[data-task_id="'+task_id+'"]');
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