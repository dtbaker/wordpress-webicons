var social_icons_remember_url = {};
jQuery(function($){
    $('body').delegate('.enabled_icons .webicon','click',function(){
        social_icons_remember_url[$(this).attr('title')] = $(this).parent().find('input').val();
        $(this).parent().remove();
        return false;
    })
    .delegate('.disabled_icons .webicon','click',function(){
        var ei = $(this).parents('.social_icon_holder').first().find('.enabled_icons');
        ei.show();
        var n = $(this).data('icon-name');
        var key = ei.find('.social_icon_prefix').val();
        if(ei.hasClass('single')){
            // only do a single icon.
            // if it's visual composer
            var vc = ei.parents('.wpb_edit_form_elements').first();
            if(vc){
                vc.find('[name=icon]').val(n);
            }
            ei.find('.enabled_icons_holder').empty().append('<div><a href="#" title="' + n + '" class="webicon ' + n + ' small">' + n + '</a> <input type="text" name="link" value="' + ( typeof social_icons_remember_url[n] != 'undefined' ? social_icons_remember_url[n] : 'http://' ) + '" class="wpb_vc_param_value  vc_param-name-link"></div>');
        }else {
            // see if this icon is already added.
            if (ei.find('.' + n + '').length > 0) {
                alert('Icon already enabled');
            } else {
                ei.find('.enabled_icons_holder').append('<div><a href="#" title="' + n + '" class="webicon ' + n + ' small">' + n + '</a> <input type="text" name="' + key + '[' + n + ']" value="' + ( typeof social_icons_remember_url[n] != 'undefined' ? social_icons_remember_url[n] : 'http://' ) + '"></div>');
            }
        }
    });

});