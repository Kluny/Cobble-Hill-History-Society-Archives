jQuery(function() {

    jQuery(".subject-image").on('click', function(){
        jQuery(".subject-image").toggleClass("expanded");
        jQuery(this).parent().toggleClass("col-md-9");
    });

    jQuery(".form-control").on('input', function(){
        jQuery(".save").attr("style", "background-color: red; border: red; opacity: 0.5;");
    });

    // if field is empty and cookie is full, set value to cookie value


    // input_1_3 refers to the donor field.
    jQuery("#input_1_3").val(localStorage.getItem("Donor"));


    // todo: uncomment and test.
/*    jQuery(".repeat").each( function() {
        val(localStorage.getItem(jQuery(this).name()))
    }); */

    // if donor field is changed, add it to cookie
    jQuery("#input_1_3").on('change', function() {
        localStorage.setItem('Donor', jQuery(this).val());
    });

/*    jQuery(".repeat").on('change', function() {
        localStorage.setItem(jQuery(this).name(), jQuery(this).val());
    });

    */

});

