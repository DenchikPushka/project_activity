<?php
class ControllerDatabase
{
	function addNewTable() {
		if (!empty($_POST['project_id']) && !empty($_POST['name']) && !empty($_POST['data'])) {
			$data = json_decode($_POST['data']);
			$project_id = mb_ereg_replace('[^\d]', '', $_POST['project_id']);
			$name = trim(mb_ereg_replace('[^A-Za-zА-ЯЁа-яё\d\_\s]', '', $_POST['name']));
			
			$model_database = getModel('database');
			$table_id = $model_database->addNewTable($project_id, $name, $data);

			die(json_encode($table_id));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}

	function addEntity() {
		if (!empty($_POST['table_id']) && !empty($_POST['data'])) {
			$table_id = mb_ereg_replace('[^\d]', '', $_POST['table_id']);
			$data = json_decode($_POST['data']);
			
			$model_database = getModel('database');
			$entity_id = $model_database->addEntity($table_id, $data);

			die(json_encode($entity_id));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}
}
?>