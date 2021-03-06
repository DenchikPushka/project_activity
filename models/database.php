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

		$mysqli->real_query("
			INSERT INTO `db_tables` (`name`, `project_id`)
			VALUES ('$name', $project_id)
		");
		$table_id = $mysqli->insert_id;

		$values = '';
		foreach ($data as $item) {
			$name = trim($mysqli->real_escape_string($item->name));
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
			$mysqli->real_query("
				INSERT INTO `db_attributes` (`table_id`, `name`, `type_id`, `not_null`)
				VALUES $values
			");
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

		$mysqli->real_query("
			SELECT 	* 
			  FROM 	`db_attributes`
			  $filter
			  ORDER BY `id`
		");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function addEntity($table_id, $data, $user_id) {
		$mysqli = db_connect();

		$mysqli->real_query("
			INSERT INTO `db_entities` (`table_id`, `creator_id`, `create_time`)
			VALUES ($table_id, $user_id, NOW())
		");
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
			$mysqli->real_query("
				INSERT INTO `db_values` (`attribute_id`, `entity_id`, `value`)
				VALUES $values
			");
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
			$filter = 'AND ('.$filter.')';
		}
		$mysqli = db_connect();

		$mysqli->real_query("
			SELECT 	`v`.*,
					`a`.`type_id`,
					`e`.`creator_id`
			  FROM 	`db_values` AS `v`
					INNER JOIN `db_attributes` AS `a`
						ON `a`.`id` = `v`.`attribute_id`
					INNER JOIN `db_tables` AS `t`
						ON `t`.`id` = `a`.`table_id`
					INNER JOIN `db_entities` AS `e`
						ON `e`.`id` = `v`.`entity_id`
			  WHERE `t`.`id` = $table_id
			  		$filter
			  ORDER BY `v`.`entity_id`
		");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		$result_array = array();
		foreach ($result as $item) {
			if (!empty($result_array[$item->entity_id])) {
				$result_array[$item->entity_id]->attributes[$item->attribute_id] = $item->value;
			} else {
				$result_array[$item->entity_id] = (object) array('attributes' => array($item->attribute_id => $item->value), 'creator_id' => $item->creator_id);
			}
		}

		return $result_array;
	}

	public function deleteEntity($entity_id) {
		$mysqli = db_connect();

		$mysqli->real_query("DELETE FROM `db_entities` WHERE `id` = $entity_id");

		$mysqli->close();

		return true;
	}

	public function deleteTable($table_id) {
		$mysqli = db_connect();

		$mysqli->real_query("DELETE FROM `db_tables` WHERE `id` = $table_id");

		$mysqli->close();

		return true;
	}

	public function getEntitiesHistory($project_id) {
		$mysqli = db_connect();

		$mysqli->real_query("
			SELECT 	`u`.`name` AS `creator_name`,
					`e`.`create_time`,
					`t`.`name` AS `table_name`
			  FROM 	`db_entities` AS `e`
					INNER JOIN `db_tables` AS `t`
						ON `t`.`id` = `e`.`table_id`
					INNER JOIN `users` AS `u`
						ON `u`.`id` = `e`.`creator_id`
			  WHERE `t`.`project_id` = $project_id
			  ORDER BY `e`.`id` DESC");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

}
?>