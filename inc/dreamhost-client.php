<?php
	require_once MOCHI_PLUGIN_DIR . '/' . 'aws' . '/' . 'aws-autoloader.php';
	require_once MOCHI_PLUGIN_DIR . '/' . 'inc' . '/' . 'functions.php';
	use Aws\S3\S3Client;

	class Dreamhost_Client {

		public $client;
		public $acl = "authenticated-read";

		public function __construct() {
			// Dreamhost credentials
			define( 'AWS_KEY', 'DHDYH9N66WCVJH7QJFE4' );
			define( 'AWS_SECRET_KEY', 'UaCq2c3VYCeMHyBETus_QJOORvmfPDEc_dm9mTwm' );
			define( 'HOST', 'https://objects-us-east-1.dream.io' );
			define( 'REGION', 'us-east-1' );
			// bucket and test bucket are now set in mochi.php.

			$this->client = new S3Client( [
				'version'     => '2006-03-01',
				'region'      => REGION,
				'endpoint'    => HOST,
				'credentials' => [
					'key'    => AWS_KEY,
					'secret' => AWS_SECRET_KEY,
				]
			] );

		}

		/**
		 * Get the file data out of form entry.
		 */
		public function process( $entry, $new_file_name = '' ) {
// demo: https://www.engagewp.com/send-gravity-forms-file-uploads-to-amazon-s3/

// Bail if there is no file uploaded to the form
			if ( empty( $entry[ FILE_UPLOAD_FIELD_ID ] ) ) {
				return;
			}

			$file_url  = $entry[ FILE_UPLOAD_FIELD_ID ];
			$file_name = ! empty( $new_file_name) ? $new_file_name : sanitize_file_name( $_FILES[ 'input_' . FILE_UPLOAD_FIELD_ID ]['name'] );

			$this->upload( $file_url, $file_name );

		}


		/**
		 * Upload to DreamHost bucket.
		 *
		 * @param $file_url
		 * @param $file_name
		 */
		private function upload( $file_url, $file_name ) {
// demo: https://docs.aws.amazon.com/code-samples/latest/catalog/php-s3-PutObject.php.html

			$url_parts = parse_url( $file_url );
			$full_path = $_SERVER['DOCUMENT_ROOT'] . $url_parts['path'];

			try {
				$result = $this->client->putObject( [
					'Bucket'     => DH_UPLOAD_BUCKET,
					'Key'        => $file_name,
					'SourceFile' => $full_path,
				] );

				return $result;

			} catch ( \Aws\S3\Exception\S3Exception $e ) {
				error_log( $e->getMessage() );
			}

			return false;
		}


		/**
		 * @param $file
		 *
		 * @return bool|string
		 */
		function signed_url( $file ) {

			//$extensions = [ 'tif', 'TIF', 'tiff', 'TIFF', 'jpg', 'JPG', 'jpeg', 'JPEG', 'txt', 'TXT', 'pdf' ];

			//foreach ( $extensions as $ext ) {
				try {

					$cmd = $this->client->getCommand('GetObject', [
						'Bucket' => DH_DOWNLOAD_BUCKET,
						'Key'    => $file
					]);

					$request = $this->client->createPresignedRequest($cmd, '+1 hour');

					return $request->getUri();

				} catch ( \Aws\S3\Exception\S3Exception $e ) {
					error_log( $e->getMessage() );
				}
			//}

			return false;
		}


	}