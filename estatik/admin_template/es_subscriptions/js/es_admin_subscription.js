(function($) {
    function es_show_paypal_fields() {
        var mode = $('select[name=es_paypal_mode]').val();
        if (mode == undefined || mode == 'sandbox') {
            $('.es-paypal-block.live').hide();
            $('.es-paypal-block.sandbox').show();
        } else {
            $('.es-paypal-block.live').show();
            $('.es-paypal-block.sandbox').hide();
        }
    }
    function es_show_paypal_type() {
        var type = $('select[name=es_paypal_type]').val();
        $('.es_paypal_block').hide();
        $('.es_settings_paypal_' + type).show();
    }
    $(document).ready(function() {
        es_show_paypal_fields();
        es_show_paypal_type();
        $('select[name=es_paypal_mode]').change(es_show_paypal_fields);
        $('select[name=es_paypal_type]').change(es_show_paypal_type);
    });
}(jQuery));