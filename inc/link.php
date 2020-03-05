<?php
	use Aws\S3\S3Client;


	// Dreamhost credentials
	define( 'AWS_KEY', 'DHDYH9N66WCVJH7QJFE4' );
	define( 'AWS_SECRET_KEY', 'UaCq2c3VYCeMHyBETus_QJOORvmfPDEc_dm9mTwm' );
	define( 'HOST', 'https://objects-us-east-1.dream.io' );
	define( 'REGION', 'us-east-1' );
	define( 'BUCKET', 'mochi' );

	function aws_client() {
		// Establish connection with DreamObjects with an S3 client.
		$client = new S3Client( [
			'version'     => '2006-03-01',
			'region'      => REGION,
			'endpoint'    => HOST,
			'credentials' => [
				'key'    => AWS_KEY,
				'secret' => AWS_SECRET_KEY,
			]
		] );

		return $client;
	}


	/** MySQL database link */

	/**
	if ( "localhost:8888" === $_SERVER['HTTP_HOST'] ) {
		$host    = 'localhost';
		$db      = 'chhs';
		$user    = 'root';
		$pass    = 'root';
		$charset = 'utf8mb4';
	} else {
		$host    = 'localhost';
		$db      = 'cobbyorg_mochi';
		$user    = 'cobbyorg_mochi';
		$pass    = '(i[y7Xz=e04L';
		$charset = 'utf8mb4';
	}


	$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
	$options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	try {
		$link = new PDO( $dsn, $user, $pass, $options );
	} catch ( \PDOException $e ) {
		throw new \PDOException( $e->getMessage(), (int) $e->getCode() );
	}
**/