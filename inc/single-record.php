<?php

	class Single_Record {

		public $id = false;
		public $input_fields = array(
			'Description' => array(
				'id'          => 'Description',
				'label'       => 'Description',
				'description' => 'Detailed description of the photograph. Lots of keywords.',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'textarea',
				'disabled'    => false,
			),
			'Donor'       => array(
				'id'          => 'Donor',
				'label'       => 'Donor',
				'description' => 'Person or organization who provided the document.',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'text',
				'disabled'    => false,
			),
			'CloudFile'   => array(
				'id'          => 'CloudFile',
				'label'       => 'CloudFile',
				'description' => '',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'text',
				'disabled'    => true,
			),
			'Size'        => array(
				'id'          => 'Size',
				'label'       => 'Size',
				'description' => 'Size of photograph in KB or MB.',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'text',
				'disabled'    => true,
			),
			'Pixels'      => array(
				'id'          => 'Pixels',
				'label'       => 'Pixels',
				'description' => 'Height by width in pixels.',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'text',
				'disabled'    => true,
			),
			'DPI'         => array(
				'id'          => 'DPI',
				'label'       => 'DPI',
				'description' => 'Pixel density of image.',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'text',
				'disabled'    => true,
			),
			'FileDate'    => array(
				'id'          => 'FileDate',
				'label'       => 'FileDate',
				'description' => 'Date that photograph was added to this archive.',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'date',
				'disabled'    => true,
			),
			'MiniPhoto'   => array(
				'id'          => 'MiniPhoto',
				'label'       => 'MiniPhoto',
				'description' => 'Name of photo thumbnail.',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'text',
				'disabled'    => false,
			),
			'SpecialInstructions'   => array(
				'id'          => 'SpecialInstructions',
				'label'       => 'Special Instructions',
				'description' => 'Details about how this file must be handled..',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'hidden',
				'disabled'    => true,
			),
			'InformationSource'   => array(
				'id'          => 'InformationSource',
				'label'       => 'Information Source',
				'description' => '',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'hidden',
				'disabled'    => true,
			),
			'UploaderIdentity'   => array(
				'id'          => 'UploaderIdentity',
				'label'       => 'Uploader Identity',
				'description' => 'The person who added this file to the database.',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'hidden',
				'disabled'    => true,
			),
			'Public'   => array(
				'id'          => 'Public',
				'label'       => 'Public',
				'description' => '',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'hidden',
				'disabled'    => true,
			),
			'Copyright'   => array(
				'id'          => 'Copyright',
				'label'       => 'Copyright',
				'description' => '',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'hidden',
				'disabled'    => true,
			),
			'Administration'   => array(
				'id'          => 'Administration',
				'label'       => 'Administration',
				'description' => '',
				'placeholder' => '',
				'value'       => '',
				'type'        => 'hidden',
				'disabled'    => true,
			),
		);
		public $image_data = array();
		public $image = '';

		public function __construct() {

		}


		public function get( $id ) {
			global $wpdb;

			// if $id isn't set, none of the other values are either.
			$this->id = $id;

			// select item from database
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ". PICTURES_TABLE ." WHERE id=%d", array( $this->id ) ) );
			if ( $wpdb->num_rows > 0 ) {
				// prepare form field options
				foreach ( $this->input_fields as $col => $args ) {
					$this->input_fields[ $col ]['value'] = $row->{$col};
				}

				$this->image_data = (array) $row;
				$this->image      = isset( $this->image_data['MiniPhoto'] ) ? $this->image_data['MiniPhoto'] : '0000000.JPG';

			}
		}

		public function update( $formdata ) {

			global $wpdb;

			$id   = intval( $formdata['id'] );
			$args = array();
			$cols = array();

			foreach ( $formdata as $key => $value ) {
				if ( ! empty ( $this->input_fields[ $key ] ) && ( false === $this->input_fields[ $key ]['disabled'] ) ) {
					// If intval nulls the input value, don't add it to the array. Null values make the query fail
					if ( 'number' === $this->input_fields[ $key ]['type'] ) {
						if ( ! empty( $value = intval( $value ) ) ) {
							$cols[] = $key;
							$args[] = $value;
						}
					} else {
						if ( ! empty( $value = sanitize_text_field( $value ) ) ) {
							$cols[] = $key;
							$args[] = $value;
						}
					}
				}
			}

			// id is the last argument because it's at the end of the mysql statement in the where clause.
			$args[] = $id;

			// Add arguments list of columns to prepared statement.
			$columns = '';
			foreach ( $cols as $key ) {
				$columns .= ' ' . $key . '=%s, ';
			}
			$columns = rtrim( $columns, ', ' );

			$sql = "UPDATE " . PICTURES_TABLE . " SET" . $columns . " WHERE id=%s";

			try {
				$result = $wpdb->get_results( $wpdb->prepare( $sql, $args ) );
				if ( null === $result ) {
					echo "<pre>MySQL Error: " . print_r( $wpdb->last_error, true ) . "</pre>";
					die;
				}

			} catch ( PDOException $e ) {
				echo "<pre>PDO Exception: " . print_r( $e, true ) . "</pre>";
				die;
			}


		}

	}