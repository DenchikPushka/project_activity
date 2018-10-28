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
	$tables = $model_database->getTables("`project_id` = $proj_id");

	
	$db_types = $model_database->getDataTypes();

	$select_options = '';
	foreach ($db_types as $type) {
		$select_options .= "<option value=\"$type->id\">$type->name</option>";
	}
	
?>
<style type="text/css">
	#table_newtable td {
		padding: 4px;
		border: 1px solid black;
	}
	.tdcenter {
		text-align: center;
	}
</style>
<div class="container">
	<div class="modal_container">
		<div class="modal_window">
		</div>
	</div>
	<center><h2><?= $project->name; ?></h2></center>
	<h3>Таблицы
	<?php if ($user->user_type == 2) { ?>
		<button class="btn btn-success" id="btn_add_table">Создать таблицу <i class="fas fa-plus"></i></button>
	<?php
		}
	?>
	</h3>
	<?php if (!empty($tables)) { ?>
		<table class="table table-hover">
		<tbody>
		<?php
			foreach ($tables as $table) {
		?>
			<tr class="tr_href" data-href="index.php?view=table&id=<?= $table->id; ?>"><th><?= $table->name; ?></th>
				<?php if ($user->user_type == 2) { ?>
				<th style="text-align: right;"><button class="btn btn-danger btn_delete_table" data-table_id="<?= $table->id; ?>" data-table_name="<?= trim(mb_ereg_replace('[^A-Za-zА-ЯЁа-яё\d\_\s]', '', $table->name)); ?>"><i class="fas fa-trash-alt"></i></button></th>
				<?php } ?>
			</tr>
		<?php 
			}
		?>
		</tbody>
		</table>
	<?php } else {
		echo '<p>Таблицы не созданы</p>';
	} ?>
</div>
<script type="text/javascript">
	const ADD_TABLE_CONT = '<div class="row"><div class="col-md-6"><label for="table_name">Имя таблицы:</label><input class="form-control" id="table_name" type="text"></div><div class="col-md-6"></div></div><br><div style="height: 280px; overflow-y: scroll;"><table id="table_newtable"><tbody><tr><td>Имя колонки</td><td>Тип данных</td><td>Обязательно для заполнения?</td></tr><tr><td><input class="form-control col_name" type="text"></td><td><select class="form-control col_type"><?= $select_options; ?></select></td><td class="tdcenter"><input class="form-check-input col_notnull" type="checkbox"></td></tr></tbody></table></div><br><center><button class="btn btn-success" id="btn_add_str_attrib">Добавить атрибут <i class="fas fa-plus"></i></button></center><br><center><button class="btn btn-success" id="btn_save_table">Сохранить <i class="fas fa-save"></i></button> <button class="btn btn-danger" id="btn_cancel_table">Отменить <i class="fas fa-undo"></i></button></center>';

	jQuery(document).ready(function() {

		var elem_add_table = document.getElementById('btn_add_table');
		if (elem_add_table) {
			elem_add_table.onclick = function() {
				jQuery('.modal_window')[0].innerHTML = ADD_TABLE_CONT;

				var elem_save_table = document.getElementById('btn_save_table');
				if (elem_save_table) {
					elem_save_table.onclick = saveTable;
				}

				var elem_cancel_table = document.getElementById('btn_cancel_table');
				if (elem_cancel_table) {
					elem_cancel_table.onclick = function() {
						jQuery('.modal_container').hide();
					};
				}

				var elem_add_str_attrib = document.getElementById('btn_add_str_attrib');
				if (elem_add_str_attrib) {
					var attrib_count = 0;
					elem_add_str_attrib.onclick = function() {
						if (attrib_count < 9) {
							jQuery('#table_newtable tr:last').after('<tr><td><input class="form-control col_name" type="text"></td><td><select class="form-control col_type"><?= $select_options; ?></select></td><td class="tdcenter"><input class="form-check-input col_notnull" type="checkbox"></td></tr>');
							attrib_count++;
						} else {
							elem_add_str_attrib.disabled = true;
						}
					};
				}

				jQuery('.modal_container').show();
			};
		}

		jQuery('.btn_delete_table').click(function() {
			table_id = this.getAttribute('data-table_id');
			table_name = this.getAttribute('data-table_name');

			noty({
                theme: 'relax',
                layout: 'topCenter',
                type: 'default',
                modal: true,
                text: 'Вы действительно хотите удалить таблицу "'+table_name+'"?',
                killer: true,
                buttons: [
                    {
                        addClass: 'btn btn-warning', text: 'Удалить', onClick: function($noty) {
                            jQuery.ajax({
						        type: 'POST',
						        url: 'index.php?task=database.deleteTable',
						        data: {
						        	table_id: table_id
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

		function saveTable() {
			if (document.getElementById('table_name').value.trim() === '') {
				noty({
	                timeout: 2000,
	                theme: 'relax',
	                layout: 'topCenter',
	                maxVisible: 5,
	                type: 'warning',
	                text: 'Пустое имя таблицы'
	            });
	            document.getElementById('table_name').focus();
				return;
			}

			var col_names = jQuery('.col_name'),
				col_types = jQuery('.col_type'),
				col_notnulls = jQuery('.col_notnull'),
				data = [];
			for (var i = 0; i < col_names.length; i++) {
				data.push({name: col_names[i].value, type: col_types[i].value, notnull: col_notnulls[i].checked});
			}
			
			jQuery.ajax({
		        type: 'POST',
		        url: 'index.php?task=database.addNewTable',
		        data: {
		            data: JSON.stringify(data),
		            name: document.getElementById('table_name').value,
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
			                text: 'Таблица не создана, проверьте данные атрибутов'
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