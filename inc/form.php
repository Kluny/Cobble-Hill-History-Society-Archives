<div class="controls row">
	<?php require 'search.php';
		global $wp;
		$record = new Single_Record();
		$id     = intval( $_GET['id'] );

		if ( $_POST ) {
			$record->update( $_POST );
		}

		$record->get( $id );

	?>
    <div class="col-md-6 text-center"><h1><?php echo $record->image_data['CatNo']; ?></h1></div>
	<?php require 'pagination_arrows.php'; ?>
</div>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?id=" . $id; ?>">
    <div class="row">
        <div class="col-md-12 top-row">
			<?php
				$tabindex = 1;

				foreach ( $record->input_fields as $key => $field ) {
					if ( ! empty( $field['type'] ) && 'textarea' === $field['type'] ) {
						$field['tabindex'] = $tabindex;
						$tabindex ++;
						text_area_input( $field );
					}
				}

			?>
            <button tabindex="<?php echo $tabindex; ?>" id="submit-top" type="submit" class="btn save btn-primary">
                Save
            </button>
            <div class="clear"></div>
        </div>
        <div class="image-col col-md-6">
            <img title="Click to toggle size" tabindex="-1" class="subject-image    "
                 src="<?php echo IMG_PATH . strtolower( $record->image ); ?>">
        </div>
        <div class="col-md col-sm-12">
			<?php
				// request.php :: $input_fields
				foreach ( $record->input_fields as $key => $field ) {
                    if ( 'textarea' !== $field['type'] ) {
						$tabindex ++;
						$field['tabindex'] = $tabindex;
						form_input( $field );
					}
				}
				$tabindex ++;
			?>
            <input type="hidden" id="id" name="id" value="<?php echo $id; ?>">
            <button tabindex="<?php echo $tabindex; ?>" id="submit" type="submit" class="btn save btn-primary">Save
            </button>

			<?php
				$dreamhost_client = new Dreamhost_Client();

				$signed_url       = $dreamhost_client->signed_url( $record->image_data['CloudFile'] );

				if ( false !== ( $signed_url ) ): ?>
                    <a id="download" target="_blank" tabindex="-1" class="btn download btn-primary"
                       download="<?php echo $record->image_data['CloudFile']; ?>" href="<?php echo $signed_url; ?>">Download full size <?php echo $record->image_data['CatNo']; ?></a>
				<?php else: ?>
                    <a id="download" tabindex="-1" class="btn download btn-primary disabled">Download not available</a>
				<?php endif; ?>
        </div>
    </div>
</form>

<div class="controls row">
	<?php require 'pagination_arrows.php'; ?>
</div>