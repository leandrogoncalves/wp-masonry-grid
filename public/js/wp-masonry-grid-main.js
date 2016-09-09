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

    });

}