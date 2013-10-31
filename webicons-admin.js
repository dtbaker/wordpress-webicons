var social_icons_remember_url = {};
jQuery(window).load(function(){
    jQuery('body').delegate('.enabled_icons .webicon','click',function(){
        social_icons_remember_url[jQuery(this).attr('title')] = jQuery(this).parent().find('input').val();
        jQuery(this).parent().remove();
        return false;
    })
        .delegate('.disabled_icons .webicon','click',function(){
        var ei = jQuery(this).parents('.social_icon_holder').first().find('.enabled_icons');
        ei.show();
        var key = ei.find('.social_icon_prefix').val();
        var n = jQuery(this).data('icon-name');
        // see if this icon is already added.
        if(ei.find('.'+n+'').length>0){
            alert('Already enabled');
        }else{
            ei.find('.enabled_icons_holder').append('<div><a href="#" title="'+n+'" class="webicon ' + n + ' small">'+n+'</a> <input type="text" name="'+key+'['+n+']" value="' + ( typeof social_icons_remember_url[n] != 'undefined' ? social_icons_remember_url[n] : 'http://' ) + '"></div>');
        }
    });

});