<?php

	if ( isset( $_GET['id'] ) ) {
		$id = intval( $_GET['id'] );

		global $wpdb;

		/**
		 * Select the current record and the one before and after it.
		 */
		$sql = "select * from " . PICTURES_TABLE . " where id = %d
            union all  
	          (select * from " . PICTURES_TABLE . " where id < %d order by id desc limit 1) 
            union all  
	          (select * from " . PICTURES_TABLE . " where id > %d order by id asc limit 1)";

		$rows = $wpdb->get_results( $wpdb->prepare( $sql, array( $id, $id, $id ) ) );

		$previous_item = false;
		$next_item     = false;
		foreach ($rows as $row ) {
			if ( $row->id > $id ) {
				$next_item = $row->id;
			} elseif ( $row->id < $id ) {
				$previous_item = $row->id;
			}
		}

		?>
        <div class="col-md-3">
            <nav aria-label="Navigation">
                <ul class="pagination pagination-lg">
					<?php if ( $previous_item ): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo site_url() . '?id=' . $previous_item; ?>"
                               aria-label="Previous">
                                <span aria-hidden="true">&laquo; Previous</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
					<?php endif; ?>
					<?php if ( $next_item ): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo site_url() . '?id=' . $next_item; ?>" aria-label="Next">
                                <span aria-hidden="true">Next &raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
					<?php endif; ?>

                </ul>
            </nav>
        </div>
		<?php

	}
?>