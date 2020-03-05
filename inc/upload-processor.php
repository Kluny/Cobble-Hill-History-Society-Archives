<?php

	class Upload_Processor {

		public $form;

		public $file_upload_field = FILE_UPLOAD_FIELD_ID;

		public $thumb_height = 900;

		public $thumb_width = 900;

		public $thumbnail_directory = THUMBNAIL_DIRECTORY;

		public $database_table = PICTURES_TABLE;

		// This will point to the uploaded file in the gravity forms directory and will contain the original filename.
		public $uploaded_file_path;

		// This will point to the resized file in the thumbnail directory and will contain the new filename.
		public $new_file_path;

		// This should be the name of the file that is uploaded to dreamhost, with the original extension, but that's not possible as the dreamhost upload has already occurred at this point.
		public $cloud_file_name;

		public $columns = array(
			'Description' => '',
			'Donor'       => '',
			'CloudFile'   => '',
			'Size'        => '',
			'Pixels'      => '',
			'DPI'         => '',
			'FileDate'    => '',
			'MiniPhoto'   => '',
			'File'        => '',
			'FileType'    => ''
		);

		public function __construct( $form ) {
			$this->form = $form;
		}

		/**
		 * Do all the things necessary to get a form submission into the database.
		 *
		 * @param $entry
		 *
		 * @return bool|int
		 */
		public function process( $entry ) {

			if ( empty( $this->form ) ) {
				return 0;
			}

			$processed_file = false;
			$row_inserted   = false;

			$full_path = $this->get_filepath( $entry );
			$catno = $this->get_incremented_catno();


			// Get values ready for database.
			foreach ( $this->form['fields'] as $k => $field ) {
				if ( ! empty( $entry[ $field->id ] ) ) {
					$this->columns[ $field->label ] = $entry[ $field->id ];
				}
			}

			// upload a PDF and a .doc

			$DPI = $this->get_exif_item( 'XResolution', $full_path );
			if ( false !== $DPI ) {
				$this->columns['DPI'] = $DPI;
			}

			$this->columns['Size']      = $this->file_size( $full_path ); // mb
			$this->columns['Pixels']    = $this->pixels( $full_path ); // pixels high and wide
			$this->columns['FileType']  = $this->filetype( $full_path ); // mimetype
			$this->columns['FileDate']  = date( 'Y-m-d H:i:s' );

			// This is where we want a mutex lock
			//LOCK TABLES PICTURES_TABLE WRITE;


			$this->columns['CatNo']     = $catno;
			$this->columns['CloudFile'] = $catno . '.' . $this->get_extension( $full_path );

			// This will change once miniphoto is resized and moved to its destination.
			$this->columns['MiniPhoto'] = $full_path;

			// TODO: add original filename to the description.

			// Check if a file was uploaded, and process it if so.
			if ( ! empty( $entry[ $this->file_upload_field ] ) ) {
				$uploader = new Dreamhost_Client();
				$uploader->process( $entry, $this->columns['CloudFile'] );
				$processed_file = $this->process_file_upload( $entry, $catno );

			}

			// Discard the uploaded file once it's all dealt with.
			// Only write to the database if the image was saved.
			if ( $processed_file ) {
				$this->columns['MiniPhoto'] = $processed_file;
				$this->remove_document( $full_path );
				$row_inserted = $this->insert_row();
			}

			// This is where we unlock the mutex

			// you are here: showing a notification if everything went fine. 
			return $row_inserted;
		}

		public function get_exif_item( $key, $full_path ) {
			@$exif = exif_read_data( $full_path );

			if ( is_array( $exif ) ) {
				if ( ! empty( $exif[ $key ] ) ) {
					return $exif[ $key ];
				}
			}

			return false;
		}

		/**
		 * Return filetype from getimagesize.
		 *
		 *
		 * @param $full_path
		 *
		 * @return string
		 */
		public function filetype( $filepath ) {
			$finfo = finfo_open( FILEINFO_MIME_TYPE );
			$mime  = finfo_file( $finfo, $filepath );
			finfo_close( $finfo );

			if ( false !== $mime ) {
				return $mime;
			}

			return 0;
		}

		/**
		 * Return image size in pixels in ###x### format.
		 *
		 * @param $filepath
		 * @param $dimension = false|string
		 *
		 * @return int|string
		 */
		public function pixels( $filepath, $dimension = false ) {
			$dimensions = array(
				'width'     => 0,
				'height'    => 1,
				'imagetype' => 2,
				'html'      => 3,
			);

			$image_size = getimagesize( $filepath );
			if ( $image_size ) {
				if ( $dimension ) {
					return $image_size[ $dimensions[ $dimension ] ];
				}

				return $image_size[0] . "x" . $image_size[1];
			}

			return 0;
		}

		/**
		 * Return file size in bytes, formatted for readability.
		 *
		 * @param $filepath
		 *
		 * @return int|string
		 */
		public function file_size( $filepath ) {
			$filesize = filesize( $filepath );
			if ( $filesize ) {
				$base     = log( $filesize, 1024 );
				$suffixes = array( '', 'K', 'M', 'G', 'T' );

				return round( pow( 1024, $base - floor( $base ) ), 1 ) . ' ' . $suffixes[ floor( $base ) ];
			}

			return 0;
		}

		/**
		 * Gets the last catalog number in the database and increments it by one.
		 *
		 * @return bool|string
		 */
		public function get_incremented_catno() {
			global $wpdb;

			$catno = false;

			$statement       = "select * from " . PICTURES_TABLE . " ORDER BY `id` DESC LIMIT 1";
			$wpdb->query( $statement );

			if ( 1 === $wpdb->num_rows ) {
				$row = $wpdb->last_result;
				$catno = $row[0]->CatNo;

			}

			if( false === $catno ) {
				return $catno;
			}

			$catno = intval( $catno ) + 1;

			$catno = str_pad ( (string) $catno, 6, 0, STR_PAD_LEFT );

			return $catno;

		}

		/**
		 * Prepare database statement and execute it.
		 */
		public function insert_row() {
			$cols = '';
			$args = '';
			global $wpdb;


			foreach ( $this->columns as $column => $value ) {
				$cols .= '`' . $column . '`, ';
				$args .= "%s, ";
			}

			$cols = rtrim( $cols, ', ' );
			$args = rtrim( $args, ', ' );

			$sql = 'INSERT INTO ' . esc_html( $this->database_table ) . ' ( ' . $cols . ' ) VALUES ( ' . $args . ' );';


			$result = $wpdb->query( $wpdb->prepare( $sql, array_values( $this->columns ) ) );

			if ( $wpdb->last_error !== '' ) {
				echo "<pre>" . print_r( $wpdb->last_error, true ) . "</pre>";
			}

			return $result;

		}

		/**
		 * Path to the uploaded file on server.
		 *
		 * @param $entry
		 *
		 * @return string
		 */
		public function get_filepath( $entry ) {
			$file_url  = $entry[ $this->file_upload_field ];
			$url_parts = parse_url( $file_url );
			$full_path = $_SERVER['DOCUMENT_ROOT'] . $url_parts['path'];

			return $full_path;
		}


		/**
		 * Save a thumbnail version of the uploaded file and discard the full size.
		 * If it's not possible to generate a thumbnail, assign an icon.
		 *
		 * Returns the new filename, or false.
		 *
		 * @param $filepath
		 * @return bool|string
		 */
		public function process_file_upload( $entry, $new_file_name ) {

			$full_path      = $this->get_filepath( $entry );
			$filetype       = $this->filetype( $full_path );
			$file_processed = false;

			$processable_types = array(
				"image/tiff",
				"image/png",
				"image/jpeg",
			);


			if ( in_array( $filetype, $processable_types ) ) {
				$filetype = explode( '/', $filetype );
				$filetype = $filetype[1];

				// check whether file is larger than 900x900 here, and just pass if it's not.
				if ( 900 < $this->pixels( $full_path, 'height' ) && 900 < $this->pixels( $full_path, 'width' ) ) {
					$this->save_document( $full_path, $new_file_name );
				} else {
					$file_processed = call_user_func_array( array( $this, 'resize_' . $filetype ), array(
						$full_path,
						900,
						900,
						false,
						$new_file_name
					) );
				}

			} else {
				$ext                        = $this->get_extension( $full_path );
				$file_processed             = $ext . '.png';

				// remove document since we'll be using a generic thumbnail.
				$this->remove_document( $full_path );

			}

			return $file_processed;
		}

		/**
		 * Remove original doc after upload to save space.
		 *
		 * @param $full_path
		 */
		public function remove_document( $full_path ) {
			unlink( $full_path );
		}

		/**
		 * // Move the file to the archive directory.
		 *
		 * @param $filepath
		 */
		public function save_document( $filepath, $new_file_name ) {
			$ext = $this->get_extension( $filepath);
			move_uploaded_file( $filepath, $this->thumbnail_directory . '/' . $new_file_name . '.' . $ext );
		}

		public function get_extension( $filepath  ) {
			return pathinfo( $filepath, PATHINFO_EXTENSION );
		}

		/**
		 * @param $file
		 * @param $w
		 * @param $h
		 * @param bool $crop
		 *
		 * @return bool
		 */
		function resize_png( $file, $w, $h, $crop = false, $new_file_name ) {

			list( $width, $height ) = getimagesize( $file );
			$r = $width / $height;
			if ( $crop ) {
				if ( $width > $height ) {
					$width = ceil( $width - ( $width * abs( $r - $w / $h ) ) );
				} else {
					$height = ceil( $height - ( $height * abs( $r - $w / $h ) ) );
				}
				$newwidth  = $w;
				$newheight = $h;
			} else {
				if ( $w / $h > $r ) {
					$newwidth  = $h * $r;
					$newheight = $h;
				} else {
					$newheight = $w / $r;
					$newwidth  = $w;
				}
			}
			$src = imagecreatefrompng( $file );
			$dst = imagecreatetruecolor( $newwidth, $newheight );
			imagecopyresampled( $dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );

			$ext = $this->get_extension( $file );
			$success = imagepng( $dst, $this->thumbnail_directory . '/' . $new_file_name . '.' . $ext );

			if( $success ) {
				return $new_file_name . '.' . $ext;
			} return $success;

		}

		/**
		 * @param $file
		 * @param $w
		 * @param $h
		 * @param bool $crop
		 *
		 * @return bool
		 */
		function resize_jpeg( $file, $w, $h, $crop = false, $new_file_name ) {
			list( $width, $height ) = getimagesize( $file );
			$r = $width / $height;
			if ( $crop ) {
				if ( $width > $height ) {
					$width = ceil( $width - ( $width * abs( $r - $w / $h ) ) );
				} else {
					$height = ceil( $height - ( $height * abs( $r - $w / $h ) ) );
				}
				$newwidth  = $w;
				$newheight = $h;
			} else {
				if ( $w / $h > $r ) {
					$newwidth  = $h * $r;
					$newheight = $h;
				} else {
					$newheight = $w / $r;
					$newwidth  = $w;
				}
			}
			$src = imagecreatefromjpeg( $file );
			$dst = imagecreatetruecolor( $newwidth, $newheight );
			imagecopyresampled( $dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );

			$ext = $this->get_extension( $file );
			$success = imagejpeg( $dst, $this->thumbnail_directory . '/' . $new_file_name . '.' . $ext);

			if( $success ) {
				return $new_file_name . '.' . $ext;
			} return $success;
		}

		/**
		 * @param $file
		 * @param $w
		 * @param $h
		 * @param bool $crop
		 *
		 * @return bool
		 */
		function resize_tiff( $file, $w, $h, $crop = false, $new_file_name ) {

			$file_processed = false;
			//$file = fopen( $file, 'r+' );

			try {
				$images = new Imagick( $file );

				// We can't deal with multipage tiffs. Probably gonna need an error report.
				$count = 0;
				foreach ( $images as $i => $image ) {
					$count ++;
					// Providing 0 forces thumbnail Image to maintain aspect ratio
					// Using height instead of width here because most of the tiffs are tall
					$image->thumbnailImage( 0, $h );
					// Get the filename without extension

					// Save to disk
					$success = $image->writeImage( $this->thumbnail_directory . '/' . $new_file_name . '.' . 'jpg');


					if( $success ) {
						return $new_file_name . '.' . 'jpg';
					}

					// Change the MiniPhoto name for the database
				}

				$images->clear();

			} catch ( Exception $e ) {
				echo $e->getMessage();
			};

			return $file_processed;

		}

	}

	// Things to note:
	// - if two files with the same filename are uploaded, the second one will arrive in the thumbnail directory with a number appended to it. Is that number also appended to the cloudfile name? better check.

