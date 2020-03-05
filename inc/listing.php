<div class="controls row">
	<?php require 'search.php'; ?>
</div>
<?php
	/** select all items with some limit and offset, display them.  */
	/** MySql Link */
	//require_once 'link.php';
	global $wpdb;

	$offset = empty( $_GET['o'] ) ? 0 : intval( $_GET['o'] );

	// If offset is 90, the first item on the page will be 91. Therefore subtract 1 from the offset to have item #90 at the top of the page.    
	$offset = ( $offset > 0 ) ? $offset - 1 : $offset;

	$limit = empty( $_GET['l'] ) ? 90 : intval( $_GET['l'] );

	$sql       = "select * from " . PICTURES_TABLE . " LIMIT %d OFFSET %d";
	$statement = $wpdb->prepare( $sql, array( $limit, $offset ) );
	$wpdb->query( $statement );

	if ( $wpdb->num_rows > 0 ): ?>
        <div class="row">
            <div class="col-md-12">
                <h4>Database Listing <?php echo isset( $search_string ) ? $search_string : ''; ?></h4>
            </div>
        </div>
		<?php
		$rows = $wpdb->get_results( $statement );
		require_once 'partials/thumbnail_listing.php' ?>
	<?php endif; ?>