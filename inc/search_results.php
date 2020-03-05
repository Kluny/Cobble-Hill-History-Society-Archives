<div class="controls row">
	<?php require 'search.php'; ?>
</div>
<?php
	/**
	 * Phrase search on the description field.
	 */
	//require_once 'link.php';
	global $wpdb;

	require_once 'functions.php';


	if ( ! empty( $_GET['archive_search'] ) ):
		$search_term = $_GET['archive_search'];
		$search_string = prepare_search_string( $search_term );
		$offset = empty( $_GET['o'] ) ? 0 : intval( $_GET['o'] );
		$limit = empty( $_GET['l'] ) ? 90 : intval( $_GET['l'] );

		$sql = "select * from " . PICTURES_TABLE . " where ( lower( Description ) like %s ) or ( lower( CatNo ) like %s ) LIMIT %d OFFSET %d; ";

		$statement = $wpdb->prepare( $sql, array( $search_string, $search_string, $limit, $offset ) );
		$result    = $wpdb->query( $statement );

		if ( $wpdb->num_rows > 0 ): ?>
            <div class="row">
                <div class="col-md-12">
                    <h4>Results: <?php echo $search_term; ?></h4>
                    <small><?php echo $wpdb->num_rows; ?> records found</small>
                </div>
            </div>
			<?php

			$rows = $wpdb->get_results( $statement );
			require_once 'partials/thumbnail_listing.php' ?>
			<?php
        elseif ( $result === false ):
            echo "No results.";
            if( is_admin() ) {
	            echo "<pre>" . print_r( $wpdb->last_error, true ) . "</pre>";
            }
		endif;
	else:
		echo "No results.";
	endif;