<?php
	/**
	 * Action for saving submitted form data.
	 */

	if ( ! isset( $_POST['id'] ) ) {
		die( 'nothing' );
	}

	$id = intval( $_POST['id'] );

	// MySql link
	//require_once 'link.php';
	global $wpdb;

	require_once 'functions.php';

	// Ensure that only allowed fields get into MySql. Escape text and intval ints.
	$allowed_fields = $input_fields;

	$args = array();
	foreach ( $_POST as $key => $value ) {

		if ( ! empty ( $allowed_fields[ $key ] ) && ( false === $allowed_fields[ $key ]['disabled'] ) ) {
			// If intval nulls the input value, don't add it to the array. Null values make the query fail
			if ( 'number' === $allowed_fields[ $key ]['type'] ) {
				if ( ! empty( $value = intval( $value ) ) ) {
					$args[ $key ] = $value;
				}
			} else {
				if ( ! empty( $value = esc_html( $value ) ) ) {
					$args[ $key ] = $value;
				}
			}
		}
	}

	// Add arguments list of columns to prepared statement.
	$columns = '';
	foreach ( $args as $key => $value ) {
		$columns .= ' ' . $key . '=?, ';
	}
	$columns = rtrim( $columns, ', ' );

	$sql = "UPDATE " . PICTURES_TABLE . " SET" . $columns . " WHERE id=?";
	// Add ID to the end of args array, since it won't be an updated column.
	$args['id'] = $id;
	
	
	try {
		$update = $wpdb->prepare( $sql );
		// Sending key-value pairs to execute() doesn't work.
		$result = $update->execute( array_values( $args ) );

		if ( ! $result ) {
			echo "<pre>MySQL Error: " . print_r( $update->errorCode(), true ) . "</pre>";
			die;
		}

	} catch ( PDOException $e ) {
		echo "<pre>PDO Exception: " . print_r( $e, true ) . "</pre>";
		die;
	}

	header( "Location: " . ABSPATH . "?id=$id" );