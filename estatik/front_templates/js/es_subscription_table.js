(function($) {
    'use strict';
    
    $.fn.SubscriptionTable = function(options) {
        var checkedEl = $(this).find('input[type=radio]:checked');
        if (checkedEl.length) {
            $(checkedEl).closest('.es-table-inner').addClass('es-tr-active');
            $(this).find('.es-table-total .es-table-total-value').html(checkedEl.data('price'));
        } else {
            $(this).find('.es-table-total').hide();
        }
        $('.es_paypal_standard_form').hide();
        $('.es_paypal_standard_form_' + $(this).val()).show();
        
        $(this).find('input[type=radio]').click(function() {
            $(this).closest('table.es-subscription-table').find('.es-table-total').show();
            $(this).closest('table.es-subscription-table').find('.es-table-inner').removeClass('es-tr-active');
            $(this).closest('.es-table-inner').addClass('es-tr-active');
            $(this).closest('table.es-subscription-table').find('.es-table-total .es-table-total-value').html($(this).data('price'));
            $('.es_paypal_standard_form').hide();
            $('.es_paypal_standard_form_' + $(this).val()).show();
        });
    };
    
    $(document).ready(function() {
        $('.es-subscription-table').SubscriptionTable({
            
        });
    });
}(jQuery));
