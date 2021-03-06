<?php
class ControllerProjects
{
	function addNewProject() {
		$user = getUser();
		if (empty($user) || $user->user_type != 2) {
			throw new Exception("Forbidden", 403);
		}
		$creator_id = $user->id;
		if (!empty($_POST['name'])) {
			$name = trim(mb_ereg_replace('[\\\\\'\#\<\>\*]', '', $_POST['name']));
			$description = null;
			if (!empty($_POST['description'])) {
				$description = trim($_POST['description']);
			}
			$model_projects = getModel('projects');
			$project_id = $model_projects->addNewProject($name, $description, $creator_id);

			die(json_encode($project_id));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}

	function deleteProject() {
		$user = getUser();
		if (empty($user) || $user->user_type != 2) {
			throw new Exception("Forbidden", 403);
		}
		if (!empty($_POST['project_id'])) {
			$project_id = mb_ereg_replace('[^\d]', '', $_POST['project_id']);
			
			$model_projects = getModel('projects');
			$model_projects->deleteProject($project_id);

			die(json_encode(true));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}

}
?>