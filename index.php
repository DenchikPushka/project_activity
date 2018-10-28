<?php
try {
	require_once('params.php');
	require_once('helper.php');
	session_start();

	if (!empty($_GET['task'])) {
		$task = $_GET['task'];
		if (preg_match('/^[0-9a-zA-Z\_]*[\.]{0,1}[0-9a-zA-Z\_]+$/', $task)) {
			if (strpos($task, '.')) {
				$classfunc = explode('.', $task);
				$class = $classfunc[0];
				$func = $classfunc[1];
				if (file_exists(DIR."/controllers/$class.php")) {
					require_once(DIR."/controllers/$class.php");
					$controller_name = 'Controller'.$class;
					$instance = new $controller_name();
					$instance->$func();
				} else {
					throw new Exception('Controller not found', 500);
				}
			} else {
				require_once(DIR.'/controller.php');
				$instance = new mainController();
				if (method_exists($instance, $task)) {
					$instance->$task();
				} else {
					throw new Exception('Function not found', 500);
				}
			}
		} else {
			throw new Exception('Invalid task', 500);
		}
	}
}
catch(Exception $e) {
	exit($e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Проектная деятельность</title>
	<link href="styles/images/favicon.ico" rel="shortcut icon">
	<link rel="stylesheet" type="text/css" href="styles/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="styles/web-fonts-with-css/css/fontawesome-all.min.css">
	<link rel="stylesheet" type="text/css" href="styles/styles.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.noty.packaged.min.js"></script>
</head>
<body>
	<header>
	<?php
		$user = getUser();
		if (!empty($user)) {
			echo '<div style="float: left;"><button class="btn btn-primary" id="btn_back"><i class="fas fa-arrow-left"></i> Назад</button></div>';
			echo '<div style="float: right;">'.$user->username.' <a class="btn btn-primary" href="index.php?task=users.exitUser">Выйти <i class="fas fa-sign-out-alt"></i></a></div>';
		}
	?>
	</header>
	<div id="main_container">
		<?php
		try {
			if (!empty($_GET['view'])) {
				$file = $_GET['view'];
				if (preg_match('/^[0-9a-zA-Z\_]+$/', $file)) {
					$filename = "/views/$file.php";
					if (file_exists(DIR.$filename)) {
					    require_once(DIR.$filename);
					} else {
					    throw new Exception('Страница не найдена', 404);
					}
				} else {
					throw new Exception('Недопустимое имя страницы', 500);
				}
			} else {
				require_once(DIR.'/views/main_page.php');
			}
		}
		catch(Exception $e) {
			echo '<h1>'.$e->getCode().': '.$e->getMessage().'</h1>';
		}
		?>
	</div>
	<footer>
		<div class="row">
		  	<div class="col-xs-6 col-md-4"></div>
		  	<div class="col-xs-6 col-md-4"></div>
		  	<div class="col-xs-6 col-md-4"></div>
		</div>
	</footer>
</body>
</html>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.tr_href').click(function() {
			location.href = this.getAttribute('data-href');
		});

		jQuery('.modal_container').mouseup(function() {
			jQuery('.modal_container').hide();
		});

		jQuery('.modal_window').mouseup(function() {
			return false;
		});

		jQuery('.area_openable').click(function() {
			jQuery('.modal_window')[0].innerHTML = '<textarea class="form-control" style="width: 400px; height: 400px; resize: none; background: white;" readonly>'+this.value+'</textarea>';
			jQuery('.modal_container').show();
			return false;
		});

		const reg_url1 = /view=projects_teacher/, reg_url2 = /view=projects_kid/;
		if (reg_url1.test(location.href) || reg_url2.test(location.href)) {
			jQuery('#btn_back').hide();
		}

		jQuery('#btn_back').click(function() {
			history.back();
		});
	});
</script>