<?php
	$user = getUser();
	if (!empty($user)) {
		switch ($user->user_type) {
			case 1:
				header('location: index.php?view=projects_kid');
				break;
			case 2:
				header('location: index.php?view=projects_teacher');
				break;
			case 3:
				header('location: index.php?view=classes');
				break;
			default:
				header('location: index.php?view=main_page');
				break;
		}
		exit();
	}
?>
<div class="container">
	<center><h2>Авторизация</h2></center>
	<div style="margin: 10px auto; width: 200px;">
		<p>Логин*<br><input class="form-control" type="text" name="username" id="username" style="width: 100%;"></p>
		<p>Пароль*<br><input class="form-control" type="password" name="password" id="password" style="width: 100%;"></p>
		<center><button class="btn btn-success" id="enter">Войти</button></center>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		document.onkeydown = function(e) {
			if (e.keyCode === 13) {
				send_authorization();
			}
		};

		document.getElementById('enter').onclick = send_authorization;

		function send_authorization() {
			var username = document.getElementById('username').value,
				password = document.getElementById('password').value;
			
			if (!(/^[a-zA-Z0-9]+$/g).test(username)) {
				noty({
	                timeout: 4000,
	                theme: 'relax',
	                layout: 'topCenter',
	                maxVisible: 5,
	                type: 'warning',
	                text: 'Проверьте логин!'
	            });
	            document.getElementById('username').focus();
			}
			else if (!(/^[a-zA-Z0-9]+$/g).test(password)) {
				noty({
	                timeout: 4000,
	                theme: 'relax',
	                layout: 'topCenter',
	                maxVisible: 5,
	                type: 'warning',
	                text: 'Проверьте пароль!'
	            });
	            document.getElementById('password').focus();
			}
			else {
				jQuery.ajax({
			        type: 'POST',
			        url: 'index.php?task=users.authorization',
			        data: {
			            username: username,
			            password: password,
			        },
			        success: function(data) {
			        	//console.log(data);
			            if (!data) {
			                noty({
			                    timeout: 4000,
			                    theme: 'relax',
			                    layout: 'topCenter',
			                    maxVisible: 5,
			                    type: 'warning',
			                    text: 'Неверный логин или пароль!'
			                });
			            }
			            else {
			            	if (data.user_type == 1) {
				                location.href = 'index.php?view=projects_kid';
				            }
				            else if (data.user_type == 2) {
				            	location.href = 'index.php?view=projects_teacher';
				            }
				            else if (data.user_type == 3) {
				            	location.href = 'index.php?view=main_page_3';
				            }
				            else {
				            	location.href = 'index.php?view=main_page';
				            }
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
		}
	});
</script>