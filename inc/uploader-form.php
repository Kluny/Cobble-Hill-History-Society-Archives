<div class="controls row">
	<?php require 'search.php';
          //require_once 'request.php';

	?>
    <div class="col-md-6 text-center"><h1><?php echo $image_data['CatNo']; ?></h1></div>
	<?php require 'inc/pagination_arrows.php'; ?>
</div>
<form method="post" enctype='multipart/form-data' action="../GravityFormsS3/inc/upload-action.php">
    <div class="row">
        <div class="col-md-12 top-row">
			<?php
				$tabindex = 1;

				// functions.php :: $input_fields
				foreach ( $input_fields as $key => $field ) {
					if ( ! empty( $field['type'] ) && 'textarea' === $field['type'] ) {
						$field['tabindex'] = $tabindex;
						$tabindex++;
						text_area_input( $field );
					}
				}

			?>
            <button tabindex="<?php echo $tabindex; ?>" id="submit-top" type="submit" class="btn save btn-primary">Save</button>
            <div class="clear"></div>
        </div>
        <div class="col-md col-sm-12">
			<?php
				// request.php :: $input_fields
				foreach ( $input_fields as $key => $field ) {
					if ( 'textarea' !== $field['type'] ) {
						$tabindex++;
						$field['tabindex'] = $tabindex;
						if( true !== $field[ 'disabled' ]) {
						    form_input( $field );
						}
					}
				}
				$tabindex++;
			?>
            <button tabindex="<?php echo $tabindex; ?>" id="submit" type="submit" class="btn save btn-primary">Save</button>
        </div>
    </div>
</form>

<div class="controls row">
    <?php require 'inc/pagination_arrows.php'; ?>
</div>