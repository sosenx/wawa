<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body><?php 

	file_put_contents('test.html', json_encode($_POST));
	echo '<pre>'; echo var_dump($_POST); echo '</pre>';

?>
</body>
</html>
