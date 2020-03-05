<?php $value = empty( $_GET['archive_search'] ) ? '': urldecode( $_GET['archive_search'] ); ?>
    <div class="col-md-3">
        <form method="get" action="#">
            <label for="search"></label>
            <input type="text" name="archive_search" id="search" value="<?php echo esc_html( $value ); ?>">
            <button id="search-submit" type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

