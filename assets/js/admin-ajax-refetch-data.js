; (function ($) {
    // Event Debouncing
    function debounce(callback, wait, immediate) {
        var timeout;
        return function () {
            var context = this, args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate) callback.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) callback.apply(context, args);
        };
    }

    // jQuery
    $(function () {
        // ajax search refetch button action
        $('#ajax_search_refetch_products').on('click', debounce(function(e) {
            // Add loader class and make button text empty
            $(this).text('').attr({
                disabled: true,
                class: 'loader'
            });

            // Handle ajax request
            $.ajax({
                url: refetch_product.admin_url,
                type: 'post',
                data: {
                    action: 'refetch_ajax_search',
                    ajxsrc_nonce: refetch_product.wp_nonce,
                    refetch: 'start_refetch',
                }
            })
            .done(function( response ){
                if( response.data.success ) {
                    $('#ajax_search_refetch_products').text('Refetch').attr('class', 'button button-primary').removeAttr('disabled');
                    alert( response.data.message );
                }
            })
        }, 300));

    });
}(jQuery));
