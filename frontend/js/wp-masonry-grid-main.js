var wpmg = wpmg || {};

if(jQuery){
    console.log('WP-Masonry-grid loaded');

    jQuery(function($){

        wpmg.front = {

            removeFilters: function(){
                $("input[name='wpmg[filter][title]']").val("");
                $("input[name='wpmg[tax][seguimentos][]']").each(function (i, e) {
                    $(e).removeAttr('checked');
                });
                $("input[name='wpmg[filter][letter]']").removeAttr('checked');

            },

            //Concerta posicionamento da paginação quando tem apenas um ou dois resultados na busca
            fixPaginationPosition: function(){
                var h = $('.masonry-item:last-child').height();
                $('.masonry-wrapper').css('height',h + 50);
            },

            ajaxPagination: function(){
                $(document).on('click', '#wpmg-loadmore',function(event){
                    event.preventDefault;
                    $e = $('#wpmg-loadmore');
                    $n = $('#wpmg-ajax-pagination-nonce');
                    $.ajax({
                        url: wpmg_ajax.ajaxurl,
                        type: 'post',
                        data: {
                            action: 'wpmg_ajax_pagination',
                            page: $e.data('page'),
                            security: $n.val()
                        },
                        dataType: 'JSON',
                        beforeSend: function(){
                            $e.text('Carregando...');
                        },
                        success: function( result ) {
                            alert( 'ok' );
                            console.log(result);
                            // console.log(result.data[0]);
                            
                            $e.text('Ver mais');
                        },
                        error: function(jqXHR, textStatus){
                            console.log('Erro ao processar ajax: ' + textStatus);
                        }
                    })
                });
            }

        };

        $(document).ready(function () {
            wpmg.front.fixPaginationPosition();
            wpmg.front.ajaxPagination();
        });



    });

}