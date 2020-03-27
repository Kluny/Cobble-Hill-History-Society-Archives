<?php
	/**
	 * Plugin Name:     CHHS Archive Manager
	 * Plugin URI:      https://geeksonthebeach.ca
	 * Description:     Upload and manage archival assets for the Cobble Hill Historical Society
	 * Author:          Shannon Graham (kluny)
	 * Author URI:      http://rocketships.ca
	 * Text Domain:     gotb
	 * Domain Path:     /languages
	 * Version:         0.1.0
	 *
	 * @package         /CHHS_Archive_Manager
	 */


	define( 'MOCHI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'MOCHI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	define( 'MOCHI', dirname( __FILE__, 2 ) );


	function mochi_init() {
		define( 'TEST_MODE', true );

		if ( true === TEST_MODE ) {
			// database table
			define( 'PICTURES_TABLE', '`test_pictures`' );
			// uploads directory
			define( 'THUMBNAIL_DIRECTORY', WP_CONTENT_DIR . "/" . "CHHSArchiveDB_test" );
			// thumbnail url
			define( 'IMG_PATH', content_url( "CHHSArchiveDB_test/" ) );
			// dreamhost bucket
			define( 'DH_UPLOAD_BUCKET', 'secret-bucket' );
			// dreamhost download bucket
			define( 'DH_DOWNLOAD_BUCKET', 'secret-bucket' );

		} else {
			// database table
			define( 'PICTURES_TABLE', '`pictures`' );
			// uploads directory
			define( 'THUMBNAIL_DIRECTORY', WP_CONTENT_DIR . "/" . "CHHSArchiveDB" );
			// thumbnail url
			define( 'IMG_PATH', content_url( "CHHSArchiveDB/" ) );
			// dreamhost upload bucket
			define( 'DH_UPLOAD_BUCKET', 'mochi' );
			// dreamhost download bucket
			define( 'DH_DOWNLOAD_BUCKET', 'mochi' );
		}


		require_once MOCHI_PLUGIN_DIR . '/inc/dreamhost-client.php';
		require_once MOCHI_PLUGIN_DIR . '/inc/upload-processor.php';
		require_once MOCHI_PLUGIN_DIR . '/inc/single-record.php';
	}

	add_action( 'init', 'mochi_init' );


	function test_mode_css() {
		if ( true === TEST_MODE ) {
			?>
            <style>
                .test_mode_active {
                    display: inline-block;
                    border: solid red 1px;
                    border-radius: 3px;
                    margin-left: 15px;
                }

                .navbar-nav .test_mode_active a.nav-link.disabled {
                    color: red;
                }

            </style>
			<?php
		} else {
			?>
            <style>
                .test_mode_active {
                    display: none;
                }
            </style>
			<?php
		}
	}

	add_action( 'wp_head', 'test_mode_css' );

	function mochi_enqueue_scripts() {
		wp_enqueue_script( 'popper', "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js", array( 'jquery' ) );
		wp_enqueue_script( 'bootstrap_js', "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js", array( 'jquery' ) );
		wp_enqueue_script( 'local_scripts', MOCHI_PLUGIN_URL . 'js/functions.js', array(
			'jquery',
			'popper',
			'bootstrap_js'
		), '6.0.1' );

		wp_enqueue_style( 'bootstrap_css', "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css", array(), '0.1.0', 'all' );
		wp_enqueue_style( 'local_styles', MOCHI_PLUGIN_URL . 'css/style.css' );


	}

	add_action( 'wp_enqueue_scripts', 'mochi_enqueue_scripts' );


	function show_mochi_index() {
		ob_start();
		require 'start.php';
		$output = ob_get_clean();

		return $output;
	}

	add_shortcode( 'mochi_index', 'show_mochi_index' );

	function show_recent_items() {
		ob_start();
		require 'recent.php';
		$output = ob_get_clean();

		return $output;
	}

	add_shortcode( 'mochi_recent', 'show_recent_items' );

	function show_most_recent_item() {
		ob_start();
		require 'inc/most_recent.php';
		$output = ob_get_clean();

		return $output;
	}

	add_shortcode( 'mochi_most_recent', 'show_most_recent_item' );


	define( 'FORM_ID', 1 );
	define( 'FILE_UPLOAD_FIELD_ID', 1 );

	/** Depends on Gravity Forms. */
	function mochi_after_submission( $entry, $form ) {
		$processor = new Upload_Processor( $form );
		$processor->process( $entry );
	}

	add_action( 'gform_after_submission_' . FORM_ID, 'mochi_after_submission', 10, 2 );

	function start_session() {
		if ( ! session_id() ) {
			session_start();
		}
	}

	add_action( 'init', 'start_session', 1 );

	function save_url( $key ) {
		$base_url         = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'];
		$url              = $base_url . $_SERVER["REQUEST_URI"];
		$_SESSION[ $key ] = $url;
	}

	function return_url( $key ) {
		if ( ! empty( $_SESSION[ $key ] ) ) {
			return $_SESSION[ $key ];
		}

		return home_url();
	}

