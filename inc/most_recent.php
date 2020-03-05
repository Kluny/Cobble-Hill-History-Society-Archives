<?php
	/** select all items with some limit and offset, display them.  */
	/** MySql Link */
	//require_once 'link.php';
	global $wpdb;

	$statement       = "select * from " . PICTURES_TABLE . " ORDER BY id DESC LIMIT 1";
	$wpdb->query( $statement );

	if ( $wpdb->num_rows > 0 ):
		$rows = $wpdb->get_results( $statement );
		foreach ( $rows as $row ): ?>

            <a href="<?php echo site_url() . "?id=" . intval( $row->id ); ?>">Catalog #<?php echo intval( $row->CatNo ); ?></a>

		<?php endforeach;
	endif;