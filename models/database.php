<?php
class ModelDatabase
{
	public function getDataTypes() {
		$mysqli = db_connect();

		$mysqli->real_query("SELECT * FROM `db_types`");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function getTables($filter = null) {
		if (empty($filter)) {
			$filter = '';
		} else {
			$filter = 'WHERE '.$filter;
		}
		$mysqli = db_connect();

		$mysqli->real_query("SELECT * FROM `db_tables` $filter");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function addNewTable($project_id, $name, $data) {
		$mysqli = db_connect();

		$mysqli->real_query("INSERT INTO `db_tables` (`name`, `project_id`) VALUES ('$name', $project_id)");
		$table_id = $mysqli->insert_id;

		$values = '';
		foreach ($data as $item) {
			$name = trim($item->name);
			$type = $item->type;
			$notnull = (int)$item->notnull;
			if (!empty($name) && !empty($type)) {
				if (!empty($values)) {
					$values .= ", ($table_id, '$name', $type, $notnull)";
				} else {
					$values .= "($table_id, '$name', $type, $notnull)";
				}
			}
		}
		if (!empty($values)) {
			$mysqli->real_query("INSERT INTO `db_attributes` (`table_id`, `name`, `type_id`, `not_null`) VALUES $values");
		} else {
			$mysqli->real_query("DELETE FROM `db_tables` WHERE `id` = $table_id");
			$table_id = 0;
		}

		$mysqli->close();

		return $table_id;
	}

	public function getAttributes($filter = null) {
		if (empty($filter)) {
			$filter = '';
		} else {
			$filter = 'WHERE '.$filter;
		}
		$mysqli = db_connect();

		$mysqli->real_query("SELECT * FROM `db_attributes` $filter ORDER BY `id`");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function addEntity($table_id, $data) {
		$mysqli = db_connect();

		$mysqli->real_query("INSERT INTO `db_entities` (`table_id`) VALUES ($table_id)");
		$entity_id = $mysqli->insert_id;

		$values = '';
		foreach ($data as $item) {
			$attr_id = $item->id;
			$value = $mysqli->real_escape_string(trim($item->value));
			if (!empty($attr_id) && $value !== '') {
				if (!empty($values)) {
					$values .= ", ($attr_id, $entity_id, '$value')";
				} else {
					$values .= "($attr_id, $entity_id, '$value')";
				}
			}
		}
		if (!empty($values)) {
			$mysqli->real_query("INSERT INTO `db_values` (`attribute_id`, `entity_id`, `value`) VALUES $values");
		} else {
			$mysqli->real_query("DELETE FROM `db_entities` WHERE `id` = $entity_id");
			$entity_id = 0;
		}

		$mysqli->close();

		return $entity_id;
	}

	public function getDataFromTable($table_id, $filter = null) {
		if (empty($filter)) {
			$filter = '';
		} else {
			$filter = 'WHERE '.$filter;
		}
		$mysqli = db_connect();

		$mysqli->real_query("SELECT `v`.* FROM `db_values` AS `v`
			INNER JOIN `db_attributes` AS `a` ON `a`.`id` = `v`.`attribute_id`
			INNER JOIN `db_tables` AS `t` ON `t`.`id` = `a`.`table_id`
			WHERE `t`.`id` = $table_id $filter");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		$result_array = array();
		foreach ($result as $item) {
			$result_array[$item->entity_id][$item->attribute_id] = $item->value;
		}

		return $result_array;
	}

}
?>