<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body><?php 
//$a = new WP_Query( array(
	chdir('.');
	include('wp-config.php');
	//stawowa 26
	$host = DB_HOST;
	$db   = DB_NAME;
	$user = DB_USER;
	$pass = DB_PASSWORD;
	$charset = 'utf8';

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$opt = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	$pdo = new PDO($dsn, $user, $pass, $opt);
	$order_meta = $pdo->query('SELECT * FROM `'.DB_NAME.'`.`wp_postmeta` WHERE (CONVERT(`meta_id` USING utf8) LIKE \'%' . $_POST[ 'p24_session_id' ] . '%\' OR CONVERT(`post_id` USING utf8) LIKE \'%' . $_POST[ 'p24_session_id' ] . '%\' OR CONVERT(`meta_key` USING utf8) LIKE \'%' . $_POST[ 'p24_session_id' ] . '%\' OR CONVERT(`meta_value` USING utf8) LIKE \'%' . $_POST[ 'p24_session_id' ] . '%\')')->fetchAll( PDO::FETCH_ASSOC );
	
	if( isset($order_meta[0]['post_id']) ){
	
		/*$order_payment_meta = $pdo->query($q = 'SELECT `id` FROM `'.DB_NAME.'`.`wp_gaad_p24` WHERE `post_id` = '.$order_meta[0]['post_id'])->fetchAll( PDO::FETCH_ASSOC );
		$pm_id = $order_payment_meta[0]['id'];
		if( isset($pm_id) ){
			
			
			$sql = "UPDATE `'.DB_NAME.'`.`wp_gaad_p24` SET `p24_amount` = \'". $_POST['p24_amount'] ."\', `p24_currency` = \'". $_POST['p24_currency'] ."\', `p24_merchant_id` = \'". $_POST['p24_merchant_id'] ."\', `p24_method` = \'". $_POST['p24_method'] ."\', `p24_order_id` = \'". $_POST['p24_order_id'] ."\', `p24_pos_id` = \'". $_POST['p24_pos_id'] ."\', `p24_session_id` = \'". $_POST['p24_session_id'] ."\', `p24_sign` = \'". $_POST['p24_sign'] ."\', `p24_statement` = \'". $_POST['p24_statement'] ."\' WHERE `wp_gaad_p24`.`id` = ".$pm_id."';";
			$pdo->query( $sql )->execute();
			
			
		} else {
			
		
		}*/
		try {
				$pdo->prepare($insert = "INSERT INTO `23341640_0000002`.`wp_gaad_p24` (`id`, `post_id`, `p24_amount`, `p24_currency`, `p24_merchant_id`, `p24_method`, `p24_order_id`, `p24_pos_id`, `p24_session_id`, `p24_sign`, `p24_statement`) VALUES (NULL, '".$order_meta[0]['post_id']."', '" . $_POST[ 'p24_amount' ] . "', '" . $_POST[ 'p24_currency' ] . "', '" . $_POST[ 'p24_merchant_id' ] . "', '" . $_POST[ 'p24_method' ] . "', '" . $_POST[ 'p24_order_id' ] . "', '" . $_POST[ 'p24_pos_id' ] . "', '" . $_POST[ 'p24_session_id' ] . "', '" . $_POST[ 'p24_sign' ] . "', '" . $_POST[ 'p24_statement' ] . "')")->execute();

			} catch (PDOException $e) { throw $e; }
	
	}
	
	//file_put_contents('test16.html', json_encode($_POST));
	file_put_contents('test16.html', json_encode($order_payment_meta) );
	echo '<pre>'; echo var_dump($fff); echo '</pre>';

?>
</body>
</html>
