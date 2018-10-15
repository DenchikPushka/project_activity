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
	$db_table = $model_database->getDataFromTable($table_id);
?>
<div class="container">
	<center><h2><?= $project->name; ?></h2></center>
	<h3><?= $table->name; ?></h3>
	<table class="table">
		<tr>
			<?php foreach ($attributes as $attr) {
				echo "<th>$attr->name</th>";
			} ?>
		</tr>
		<?php foreach ($db_table as $entity) {
			echo '<tr>';
			foreach ($attributes as $attr) {
				$attr_key = $attr->id;
				if (array_key_exists($attr_key, $entity)) {
					echo "<td>$entity[$attr_key]</td>";
				} else {
					echo '<td></td>';
				}
				
			}
			echo '</tr>';
		} ?>
		<tr>
			<?php foreach ($attributes as $attr) { ?>
				<td><input class="form-control db_attr_values" data-db_attr_id="<?= $attr->id; ?>" data-db_type="<?= $attr->type_id; ?>" type="text"></td>
			<?php } ?>
		</tr>
	</table>
	<button class="btn btn-success" id="btn_add_entity">Добавить строку <i class="fas fa-plus"></i></button>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {

		var elem_add_entity = document.getElementById('btn_add_entity');
		if (elem_add_entity) {
			elem_add_entity.onclick = function() {
				var attr_values = jQuery('.db_attr_values'), data = [];
				for (var i = 0; i < attr_values.length; i++) {
					data.push({value: attr_values[i].value, id: attr_values[i].getAttribute('data-db_attr_id')});
				}
				jQuery.ajax({
			        type: 'POST',
			        url: 'index.php?task=database.addEntity',
			        data: {
			        	table_id: <?= $table_id; ?>,
			            data: JSON.stringify(data)
			        },
			        success: function(data) {
			        	//console.log(data);
			        	if (data !== 0) {
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