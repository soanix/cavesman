<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" rel="stylesheet">
		<link href="{$css}/login.css" type="text/css" rel="stylesheet">
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="modal-dialog">
					<div class="loginmodal-container">
						<h1>TPV CM</h1><br>
						<form action="/ajax/user/login" method="POST">
							<input type="text" name="user" placeholder="Usuario">
							<input type="password" name="password" placeholder="Contraseña">
							<input type="submit" name="login" class="login loginmodal-submit" value="ENTRAR">
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
