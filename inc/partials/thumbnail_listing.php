<div class="row image-row">

	<?php
		$search = '';
		if ( ! empty( $_GET['archive_search'] ) ) {
			$search = "&archive_search=" . esc_html( $_GET['archive_search'] );
		}

		$count = 0;
		foreach ( $rows as $row ):
			$image = isset( $row->MiniPhoto ) ? $row->MiniPhoto : '0000000.JPG';
			?>
            <div class="col-md-2 col-sm-4 col-xs-1">
                <div class="img-container">
                    <a href="<?php echo site_url() . "?id=" . intval( $row->id ); ?>">
                        <img title="<?php echo esc_html( $row->Description ); ?>" class="thumbnail image-fluid"
                             src="<?php echo IMG_PATH . strtolower( $image ); ?>">
                    </a>
                </div>
                <div>
                    <p>
                        <a href="<?php echo site_url() . "?id=" . intval( $row->id ) . $search;; ?>">
							<?php echo esc_html( $row->CatNo ); ?></a>
                        <br>
						<?php echo wp_trim_words( esc_html( $row->Description ), 20 ); ?>
                    </p>
                </div>
            </div>
		<?php endforeach; ?>
</div>