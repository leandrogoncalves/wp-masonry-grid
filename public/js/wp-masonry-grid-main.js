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

            }

        };

        //Concerta posicionamento da paginação quando tem apenas um ou dois resultados na busca
        $(document).ready(function () {
            var h = $('.masonry-item:last-child').height();
            $('.masonry-wrapper').css('height',h + 50);
        });



    });

}