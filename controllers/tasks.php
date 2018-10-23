<?php
class ControllerTasks
{
	function addNewTask() {
		if (!empty($_POST['project_id']) && !empty($_POST['name']) && !empty($_POST['data'])) {
			$data = json_decode($_POST['data']);
			$project_id = mb_ereg_replace('[^\d]', '', $_POST['project_id']);
			$name = trim(mb_ereg_replace('[^A-Za-zА-ЯЁа-яё\d\_\s]', '', $_POST['name']));
			$description = null;
			if (!empty($_POST['description'])) {
				$description = trim($_POST['description']);
			}
			
			$model_tasks = getModel('tasks');
			$task_id = $model_tasks->addNewTask($project_id, $name, $description, $data);

			die(json_encode($task_id));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}

	function closeTask() {
		$user = getUser();
		if (empty($user) || $user->user_type != 2) {
			throw new Exception("Forbidden", 403);
		}
		if (!empty($_POST['id'])) {
			$id = mb_ereg_replace('[^\d]', '', $_POST['id']);
			$model_tasks = getModel('tasks');
			$model_tasks->closeTask($id);
			die(json_encode(true));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}

	function openTask() {
		$user = getUser();
		if (empty($user) || $user->user_type != 2) {
			throw new Exception("Forbidden", 403);
		}
		if (!empty($_POST['id'])) {
			$id = mb_ereg_replace('[^\d]', '', $_POST['id']);
			$model_tasks = getModel('tasks');
			$model_tasks->openTask($id);
			die(json_encode(true));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}
}
?>