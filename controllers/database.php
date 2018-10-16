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

			$user = getUser();
			$files = array();
			foreach ($_FILES as $file) {
				$md5 = md5($user->id.microtime().$file['name'].rand(1, 10000));
		        if (move_uploaded_file($file['tmp_name'], DIR.'/uploads/'.$md5)) {
		            $files[] = (object)array('name' => $file['name'], 'md5' => $md5);
		        } else {
		            throw new Exception('File not upload', 500);
		        }
		    }

		    $model_database = getModel('database');

			foreach ($data as $key => $item) {
				$attr_id = $item->id;
				$value = trim($item->value);
				if (!empty($attr_id) && $value !== '') {
					$attr = $model_database->getAttributes("`id` = $attr_id")[0];
					if ($attr->type_id == 5) {
						foreach ($files as $file) {
							if ($file->name == $value) {
								$data[$key]->value .= ' | '.$file->md5;
								break;
							}
						}
					}
				}
			}
			
			$entity_id = $model_database->addEntity($table_id, $data);

			die(json_encode($entity_id));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}
}
?>