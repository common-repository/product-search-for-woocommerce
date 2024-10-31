; (function ($) {
    'use strict'
    $(function () {
        // create ajax list view DOM
        var __ul  = document.createElement('ul');
        var __btn = document.createElement('button');
        __btn.classList.add('woo_ajax_search_more');
        $('div.woo-ajax-search-results').html(__ul);

        // Select search form field
        var wooSearchBar = $('form.woo-ajax-search-form input[name="s"]');
        // wooSearchBar.attr('placeholder', ajax_search.placeholder);

        /**
         * Event Debouncing
         *
         * @param {object} callback
         * @param {number} wait
         * @param {null} immediate
         * @returns
         */
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

        /**
         * Remove appended search indices
         *
         * @param {object} success
         * @param {object} data
         */
        function resetAppendedSearchIndex( data ) {
            if ( Number( data.length ) > 0 ) {
                $('.woo-ajax-search-results ul').empty();
            } else {
                $('.woo-ajax-search-results ul').empty();
                var __li_notFound = document.createElement('li');
                ajax_search.notFound.length > 2 ?
                    __li_notFound.textContent = ajax_search.notFound :
                        __li_notFound.textContent = 'No result found';

                __ul.appendChild(__li_notFound);

            }
        }

        // Ajax search request
        function wooSearchAjaxRequest(val, offset = 0) {
            $.ajax({
                url: ajax_search.admin_url,
                type: 'post',
                data: {
                    action: 'query_ajax_search',
                    ajax_search_nonce: ajax_search.wp_nonce,
                    indices: val,
                    offset: offset
                },
            })
            .done(function (response) {
                // Remove appended search indices.
                resetAppendedSearchIndex(response.data);
                var allPages = ( Number(response.page) - 1 );

                // View all search indices in list view.
                response.data.map(function (searchVal) {
                    // Create elements
                    var __li_searchList = document.createElement('li');
                    var __a_anchor      = document.createElement('a');
                    __a_anchor.setAttribute('href', searchVal.url);
                    __a_anchor.classList.add('ajax_search__product');

                    var __div_content    = document.createElement('div');
                    __div_content.classList.add('ajax_search__product-content');
                    __a_anchor.appendChild(__div_content);

                    if (ajax_search.product_img.length) {
                        var __div_thumb       = document.createElement('div');
                        __div_thumb.innerHTML = searchVal.attachment;
                        __div_thumb.classList.add('ajax_search__product-image');
                        __div_content.appendChild(__div_thumb);
                    }

                    if (ajax_search.product_price.length) {
                        var __div_price       = document.createElement('div');
                        __div_price.innerHTML = searchVal.price;
                        __div_price.classList.add('ajax_search__product-price');
                        __a_anchor.appendChild(__div_price);
                    }

                    var __h4_title = document.createElement('h4');
                    __h4_title.textContent = searchVal.post_title;
                    __h4_title.classList.add('ajax_search__product-title');
                    __div_content.appendChild(__h4_title);

                    // Populate DOMs
                    __li_searchList.appendChild(__a_anchor);
                    __ul.appendChild(__li_searchList);

                });

                // More button to navigate more search result
                if( allPages > offset ) {
                    __btn.textContent = 'More...';
                    __btn.removeAttribute('disabled');
                    __ul.appendChild(__btn);

                } else if( allPages <= 0 ) {
                    __btn.remove();

                } else {
                    wooSearchOffset = 0;
                    __btn.setAttribute('disabled', true);
                    __btn.textContent = 'No more results';
                    __ul.appendChild(__btn);

                }
            })
            .fail(function (error) {
                console.log(error);
            });
        }

        // More button click
        var wooSearchOffset = 0;
        $('body').on('click', '.woo_ajax_search_more', function (e) {
            wooSearchOffset++;
            wooSearchAjaxRequest(wooSearchBar.val(), wooSearchOffset);
        });

        // Ajax search form field action
        wooSearchBar.on('keyup', debounce(function (e) {
            var searchLength            = $(this).val().length;
            var displayList             = $('div.woo-ajax-search-results').show();
            var __li_loading            = document.createElement('li');
            var ajxLoadingContent       = (ajax_search.loading.length > 0) ? ajax_search.loading : 'loading...';
            __li_loading.textContent    = ajxLoadingContent;

            // Check if search field is empty
            if (searchLength > 2) {
                displayList;
            } else if (searchLength > 0) {
                $('div.woo-ajax-search-results ul').empty();
                displayList;
                __ul.appendChild(__li_loading);
                return;
            } else {
                $('div.woo-ajax-search-results').hide();
                return;
            }

            // Trigger ajax requests
            wooSearchAjaxRequest( $(this).val() );

        }, 500));
    });
}(jQuery));
