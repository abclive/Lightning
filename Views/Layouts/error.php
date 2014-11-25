<html>

	<head>
		<title>Lightning - <?= $error->getMessage() ?></title>
	</head>

	<body>
		<h1><?= $error->getCode() ?></h1>
		<p><?= $error->getMessage() ?></p>
	</body>

</html>