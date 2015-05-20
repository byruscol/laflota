function jcInit(){
    jQuery('.jcarousel')
            .jcarousel({
                // Options go here
            });

        
        jQuery('.jcarousel-control-prev')
            .on('jcarouselcontrol:active', function() {
                jQuery(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                jQuery(this).addClass('inactive');
            })
            .jcarouselControl({
                // Options go here
                target: '-=1'
            });

        
        jQuery('.jcarousel-control-next')
            .on('jcarouselcontrol:active', function() {
                jQuery(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                jQuery(this).addClass('inactive');
            })
            .jcarouselControl({
                // Options go here
                target: '+=1'
            });

        jQuery('.jcarousel-pagination')
            .on('jcarouselpagination:active', 'a', function() {
                jQuery(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                jQuery(this).removeClass('active');
            })
            .jcarouselPagination({
                // Options go here
            });
    
}
/*(function($) {
    jQuery(function() {
        
        jQuery('.jcarousel')
            .jcarousel({
                // Options go here
            });

        
        jQuery('.jcarousel-control-prev')
            .on('jcarouselcontrol:active', function() {
                jQuery(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                jQuery(this).addClass('inactive');
            })
            .jcarouselControl({
                // Options go here
                target: '-=1'
            });

        
        jQuery('.jcarousel-control-next')
            .on('jcarouselcontrol:active', function() {
                jQuery(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                jQuery(this).addClass('inactive');
            })
            .jcarouselControl({
                // Options go here
                target: '+=1'
            });

        jQuery('.jcarousel-pagination')
            .on('jcarouselpagination:active', 'a', function() {
                jQuery(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                jQuery(this).removeClass('active');
            })
            .jcarouselPagination({
                // Options go here
            });
    });
})(jQuery);*/
