; (function ($) {
    $(function () {
        const $checkbox = $('#ajax_search_enable_search');
        if( $checkbox.length ) {
            $checkbox.on('click', function(e) {
                if( ! $(this).attr('checked') ) {
                    $(this).attr('checked', true);
                    $(this).parents('tr').next().show();
                } else {
                    $(this).attr('checked', false);
                    $(this).parents('tr').next().hide();
                }
            });

            if( $checkbox.attr( 'checked' ) ) {
                $checkbox.parents('tr').next().show();
            };
        }

    });
}(jQuery));
