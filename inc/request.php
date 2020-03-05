<?php
	/**
	 * Selects a record from the database and prepares it as form data.
	 */
	require 'functions.php';

	if ( ! isset( $_GET ) ) {
		die( 'invalid access' );
	}

	if ( empty( $_GET['id'] ) ) {
		die( 'invalid access' );
	}

	// MySql link

	global $wpdb;

	$id = intval( $_GET['id'] );
	// select item from database

	// link.php :: $link
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . PICTURES_TABLE . " WHERE id=%d", array( $id ) ) );
	if ( $wpdb->num_rows > 0 ) {
		// prepare form field options
		foreach ( $input_fields as $col => $args ) {
			$input_fields[ $col ]['value'] = $row->{$col};
		}

		$image_data = (array) $row;
		$image      = isset( $image_data['MiniPhoto'] ) ? $image_data['MiniPhoto'] : '0000000.JPG';

	}
