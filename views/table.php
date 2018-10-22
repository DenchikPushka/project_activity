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
	$items = $model_projects->getData("`id` = $table->project_id");
	if (empty($items)) {
		throw new Exception('Project not found', 500);
	}
	else {
		$project = $items[0];
	}

	$attributes = $model_database->getAttributes("`table_id` = $table_id");
	$db_table = $model_database->getDataFromTable($table_id);
?>
<style type="text/css">
	table {
        border-collapse: collapse;
        border: 1px solid black;
    }
    tbody, thead, tfoot, tr {
        display: block;
    }
    td, th {
    	padding: 5px 10px;
        display: inline-block;
        width: calc(<?= (int)(100/(count($attributes))); ?>% - <?= (int)(60/(count($attributes))); ?>px);
        vertical-align: top;
    }
    td {
        overflow: auto;
    }
    thead {
        border-bottom: 1px solid black;
    }
    tbody {
        max-height: 400px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    tbody tr {
        border-bottom: 1px dashed darkgray;
    }
    tfoot {
        border-top: 1px solid black;
    }
</style>
<div class="container">
	<div class="modal_container">
		<div class="modal_window">
		</div>
	</div>
	<center><h2><?= $project->name; ?></h2></center>
	<h3><?= $table->name; ?></h3>
	<table class="" style="width: 100%;">
		<thead>
			<tr>
				<?php foreach ($attributes as $attr) {
					echo "<th>$attr->name</th>";
				} ?>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($db_table as $key => $entity) {
				echo "<tr data-entity_id=\"$key\">";
				foreach ($attributes as $attr) {
					$attr_key = $attr->id;
					if (array_key_exists($attr_key, $entity)) {
						if ($attr->type_id == 5) {
							$file = explode('|', $entity[$attr_key]);
							echo "<td><a href=\"uploads/$file[0]\" download=\"$file[1]\">$file[1]</a></td>";
						} elseif ($attr->type_id == 3) {
							echo "<td><textarea class=\"form-control\" style=\"cursor: pointer; background: #ffffff; resize: none;\" readonly>$entity[$attr_key]</textarea></td>";
						} else {
							echo "<td>$entity[$attr_key]</td>";
						}
					} else {
						echo '<td></td>';
					}
				}
				echo "<td style=\"width: 60px; text-align: right;\"><button class=\"btn btn-danger btn_delete_entity\" data-entity_id=\"$key\"><i class=\"fas fa-trash-alt\"></i></button></td>";
				echo '</tr>';
			} ?>
		</tbody>
		<tfoot>
			<tr>
				<?php foreach ($attributes as $attr) {
					$important = '';
					if ($attr->not_null == 1) {
						$important = '<label>(обязательное поле)</label>';
					}
					switch ($attr->type_id) {
						case 1:
					 		echo "<th><input class=\"form-control db_attr_values\" data-db_attr_id=\"$attr->id\" type=\"number\">$important</th>";
					 		break;
					 	case 2:
					 		echo "<th><input class=\"form-control db_attr_values\" data-db_attr_id=\"$attr->id\" type=\"text\">$important</th>";
					 		break;
					 	case 3:
					 		echo "<th><textarea class=\"form-control db_attr_values\" data-db_attr_id=\"$attr->id\" style=\"resize: none;\"></textarea>$important</th>";
					 		break;
					 	case 4:
					 		echo "<th><input class=\"form-control db_attr_values\" data-db_attr_id=\"$attr->id\" type=\"number\">$important</th>";
					 		break;
					 	case 5:
					 		echo "<th><input style=\"display: none;\" data-db_attr_id=\"$attr->id\" type=\"file\">
					 			<button class=\"btn btn-success db_file_button\" data-db_attr_id=\"$attr->id\"><i class=\"fas fa-upload\"></i></button>
					 			<label class=\"db_file_label\" data-db_attr_id=\"$attr->id\"></label>
					 			<input class=\"db_attr_values db_file\" data-db_attr_id=\"$attr->id\" type=\"hidden\">$important</th>";
					 		break;
					}
				} ?>
				<th></th>
			</tr>
			<tr><th><button class="btn btn-success" id="btn_add_entity">Добавить строку <i class="fas fa-plus"></i></button></th></tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		///////////////////////////////////////////////////////////
		jQuery(window).resize(function() {
			var $table = jQuery('table'),
			    $bodyCells = $table.find('tbody tr:first').children(),
			    colWidth;
			// Get the tbody columns width array
			colWidth = $bodyCells.map(function() {
			    return jQuery(this).width();
			}).get();
			// Set the width of thead columns
			$table.find('thead tr').children().each(function(i, v) {
			    jQuery(v).width(colWidth[i]);
			});
			$table.find('tfoot tr').children().each(function(i, v) {
			    jQuery(v).width(colWidth[i]);
			});
		}).resize();
		///////////////////////////////////////////////////////////

		jQuery('.btn_delete_entity').click(function() {
			var entity_id = this.getAttribute('data-entity_id');
			console.log(entity_id);
		});

		jQuery('.db_file_button').click(function() {
			var attr_id = this.getAttribute('data-db_attr_id');
		    jQuery('input[type=file][data-db_attr_id='+attr_id+']')[0].click();
		});

		jQuery('input[type=file]').change(function() {
			var attr_id = this.getAttribute('data-db_attr_id');
		    if (this.files.length === 1) {
		    	jQuery('.db_file[data-db_attr_id='+attr_id+']')[0].value = this.files[0].name;
		    	jQuery('.db_file_label[data-db_attr_id='+attr_id+']')[0].innerHTML = this.files[0].name;
		    }
		});

		var elem_add_entity = document.getElementById('btn_add_entity');
		if (elem_add_entity) {
			elem_add_entity.onclick = function() {
				var attr_values = jQuery('.db_attr_values'), data = [];
				for (var i = 0; i < attr_values.length; i++) {
					data.push({value: attr_values[i].value, id: attr_values[i].getAttribute('data-db_attr_id')});
				}

				var files = [],
					inputs_files = jQuery('input[type=file]');
				for (var i = inputs_files.length; i--;) {
					if (inputs_files[i].files.length === 1) {
						files.push(inputs_files[i].files[0]);
					}
				}

				var formdata = new FormData();
			    jQuery.each(files, function(key, value) {
			        formdata.append(key, value);
			    });
			    formdata.append('table_id', <?= $table_id; ?>);
			    formdata.append('data', JSON.stringify(data));
			    /*for (var key of formdata.entries()) {
			    	console.log(key[0]+', '+key[1]);
			    }*/
				jQuery.ajax({
			        type: 'POST',
			        url: 'index.php?task=database.addEntity',
			        cache: false,
			        processData: false, // Не обрабатываем файлы (Don't process the files)
        			contentType: false, // Так jQuery скажет серверу что это строковой запрос
			        data: formdata,
			        success: function(data) {
			        	//console.log(data);
			        	if (data == 'Empty value') {
			        		noty({
				                timeout: 4000,
				                theme: 'relax',
				                layout: 'topCenter',
				                maxVisible: 5,
				                type: 'warning',
				                text: 'Не заполнено поле, обязательное для заполнения'
				            });
			        	} else if (data !== 0) {
			        		location.reload();
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
			};
		}

	});
</script>