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
<style type="text/css">
	.table>tbody>tr>td, .table>tbody>tr>th {
		vertical-align: middle;
	}
</style>
<div class="container">
	<div class="modal_container">
		<div class="modal_window">
			<label for="project_name">Название:</label><input class="form-control" id="project_name" type="text"><br>
			<label for="project_description">Описание:</label><textarea class="form-control" id="project_description" style="width: 380px; height: 260px; resize: none;"></textarea><br>
			<center><button class="btn btn-success" id="btn_save_project">Сохранить <i class="fas fa-save"></i></button>
				<button class="btn btn-danger" id="btn_cancel_project">Отменить <i class="fas fa-undo"></i></button></center>
		</div>
	</div>
	<center><h2>Проекты</h2></center>
	<button class="btn btn-success" id="btn_add_project" style="float: right;">Создать проект <i class="fas fa-plus"></i></button>
	<?php if (!empty($items)) { ?>
		<table class="table table-hover">
		<thead>
			<th>Название</th>
			<th>Описание</th>
		</thead>
		<tbody>
		<?php foreach ($items as $item) { ?>
			<tr class="tr_href" data-href="index.php?view=projectcard_teacher&id=<?= $item->id ?>"><td><?= $item->name; ?></td><td><textarea class="form-control area_openable" style="resize: none; cursor: pointer; background: white;" readonly><?= $item->description; ?></textarea></td></tr>
		<?php } ?>
		</tbody>
		</table>
	<?php } else {
		echo '<p>Проекты не созданы</p>';
	} ?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		const ADD_PROJECT_CONT = jQuery('.modal_window')[0].innerHTML;

		var elem_add_project = document.getElementById('btn_add_project');
		if (elem_add_project) {
			elem_add_project.onclick = function() {

				jQuery('.modal_window')[0].innerHTML = ADD_PROJECT_CONT;

				var elem_save_project = document.getElementById('btn_save_project');
				if (elem_save_project) {
					elem_save_project.onclick = saveProject;
				}

				var elem_cancel_project = document.getElementById('btn_cancel_project');
				if (elem_cancel_project) {
					elem_cancel_project.onclick = function() {
						jQuery('.modal_container').hide();
					};
				}

				jQuery('.modal_container').show();
			};
		}

		function saveProject() {
			if (document.getElementById('project_name').value.trim() === '') {
				noty({
	                timeout: 2000,
	                theme: 'relax',
	                layout: 'topCenter',
	                maxVisible: 5,
	                type: 'warning',
	                text: 'Пустое название'
	            });
	            document.getElementById('project_name').focus();
				return;
			}

			jQuery.ajax({
		        type: 'POST',
		        url: 'index.php?task=projects.addNewProject',
		        data: {
		            name: document.getElementById('project_name').value,
		            description: document.getElementById('project_description').value
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
			                text: 'Проект не создан'
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

	});
</script>