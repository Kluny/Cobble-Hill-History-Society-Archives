<?php

	require_once 'inc/functions.php';
	$id = ! empty( $_GET['id'] ) ? intval( $_GET['id'] ) : false;

	$search = '';
	if ( ! empty( $_GET['archive_search'] ) ) {
		$search = "&archive_search=" . esc_html( $_GET['archive_search'] );
	}

?>

<div class="container-fluid">
	<?php if ( $id ) { ?>
		<a href="<?php echo return_url( 'thumbnail_listing' ); ?>">« Thumbnails</a>&nbsp;&nbsp;&nbsp;
	<?php } ?>
	<a href="<?php echo home_url(); ?>">Recently Added</a>
	<?php
		save_url( 'thumbnail_listing' );
		require_once 'inc/recent_listing.php';
		require_once 'inc/pagination_index.php';
	?>

</div>
