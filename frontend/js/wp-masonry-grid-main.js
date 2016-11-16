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
            // fixPaginationPosition: function(){
            //     var h = $('.masonry-item:last-child').height();
            //     $('.masonry-wrapper').css('height',h + 50);
            // },

            
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
                            console.log(result);
                            // console.log(result.data[0]);


                            if(result.data != undefined && result.data != 0){
                                var _parser = new DOMParser()
                                    , _grid = document.querySelector('.masonry-wrapper')
                                    , _item = document.createElement('span')
                                    , _dom;

                                for(var i = 0; i < result.data.length; i++){
                                    // _dom = _parser.parseFromString(result.data[i], "text/xml");
                                    // _dom = _dom.documentElement;
                                    // _dom.firstChild.style.padding = '0';
                                    // _el = _dom.firstElementChild;
                                    // console.log(_el);
                                    salvattore.appendElements(_grid, [_item]);
                                    _item.outerHTML = result.data[i];
                                }

                            }


                            $e.text('Ver mais');
                            $e.data('page',result.page);

                            if(result.no_more) $e.hide();

                        },
                        error: function(jqXHR, textStatus){
                            console.log('Erro ao processar ajax: ' + textStatus);
                        }
                    })
                });
            },

            filterByLetter: function(){
                $('#filtro-letras input').on('click', function (event) {
                    event.preventDefault();
                    $('#frm_filtroLoja').submit();
                });
            }

        };

        $(document).ready(function () {
            wpmg.front.fixPaginationPosition();
            wpmg.front.ajaxPagination();
            wpmg.front.filterByLetter();
        });



    });

}