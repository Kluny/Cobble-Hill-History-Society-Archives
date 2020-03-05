<?php
	$previous_page = false;
	$offset        = empty( $_GET['o'] ) ? 0 : intval( $_GET['o'] ) ;
	$limit         = empty( $_GET['l'] ) ? 90 : intval( $_GET['l'] );

	$search = '';
	if ( ! empty( $_GET['archive_search'] ) ) {
		$search = "&archive_search=" . urlencode( esc_html( $_GET['archive_search'] ) );
	}



	$next_page = $offset + $limit;
	if ( $offset > 0 ) {
		$previous_page = $offset - $limit;
	}

?>
<div class="col-md-3">
    <nav aria-label="Navigation">
        <ul class="pagination pagination-lg">
			<?php if ( false !== $previous_page ): ?>
                <li class="page-item">
                    <a class="page-link align-bottom"
                       href="<?php echo site_url() . '?o=' . $previous_page . '&l=' . $limit . $search; ?>"
                       aria-label="Previous">
                        <span aria-hidden="true">&laquo; Previous</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
			<?php endif; ?>
            <li class="page-item">
                <a class="page-link align-bottom" href="<?php echo site_url() . '?o=' . $next_page . '&l=' . $limit . $search; ?>"
                   aria-label="Next">
                    <span aria-hidden="true">Next &raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
</div>