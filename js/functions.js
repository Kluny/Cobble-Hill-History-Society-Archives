jQuery(function () {

    jQuery(".subject-image").on('click', function () {
        jQuery(".subject-image").toggleClass("expanded");
        jQuery(this).parent().toggleClass("col-md-9");
    });

    jQuery(".form-control").on('input', function () {
        jQuery(".save").attr("style", "background-color: red; border: red; opacity: 0.5;");
    });

    var repeaters = jQuery(".repeat input");

    repeaters.each(function () {
        if (null !== localStorage.getItem(this.name)) {
            this.value = localStorage.getItem(this.name);
        }
    });

    repeaters.on('change', function () {
        localStorage.setItem(this.name, this.value);
    });

    var checkboxes = jQuery(".checkbox-repeat input");

    // The goal is to save the values of the radio buttons in local storage such
    // that they will populate on page refresh but not interfere with default values
    // on first page load.

    checkboxes.each(function () {
        // set values to stored value if one is present
        if( null !== localStorage.getItem(this.id) ) {
            this.checked = localStorage.getItem(this.id);
        }
    });

    checkboxes.on('click', function () {
        // update with newly entered value
        var name = this.name;

        // remove all old values
        jQuery('input[name="'+ name +'"]').each( function() {
            localStorage.removeItem(this.id, this.checked);
        });

        // add new value to storage
        localStorage.setItem(this.id, this.checked);

    });
});

