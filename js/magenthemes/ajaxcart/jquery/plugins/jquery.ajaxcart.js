$mt(function($) {
    var themeResize = function(){
        width = $mt(window).width();
        if(width <= 767){
            $('body').find('.btn-cart').addClass('btn-cart-mobile');
        }else{
            $('body').find('.btn-cart').removeClass('btn-cart-mobile');
        }
    }
    themeResize();
    $(window).resize(function(){
        themeResize();
    });
    $('.btn-cart').live('click', function () {
        var cart = $('.mt-maincart');
        if($('.product-view').length>0){
            if($('#qty').val()>0 && $('.validation-failed').length==0){
                var currentImg = $('.product-view').find('p.product-image img');
            }
        }else{
            var currentImg = $(this).parents('.item').find('a.product-image img');
        }
        if (currentImg && $(cart).length>0 && $(this).hasClass('option-file') == false && $(this).hasClass('btn-cart-mobile') == false) {
            var imgclone = currentImg.clone()
                .offset({ top:currentImg.offset().top, left:currentImg.offset().left })
                .addClass('imgfly')
                .css({'opacity':'0.7', 'position':'absolute', 'height':'180px', 'width':'180px', 'z-index':'1000'})
                .appendTo($('body'))
                .animate({
                    'top':cart.offset().top + 10,
                    'left':cart.offset().left + 10,
                    'width':55,
                    'height':55
                }, 1000, 'easeInOutExpo');
            imgclone.animate({'width':0, 'height':0});
        }
        return false;
    });
    $('.options-cart').live('click', function() {
        $.colorbox({
            iframe: true,
            href:this.href,
            opacity:	0.5,
            speed:		300,
            innerWidth:'65%',
            innerHeight:'65%'
        });
        $('body').find('img.imgfly').remove();
    });

    $('.show-options').live('click', function(e){
        if($('.btn-cart-mobile').length == 0){
            $('#options-cart-' + $(this).attr('data-id')).trigger('click');
        }else{
            return window.location.href=$(this).attr('data-submit');
        }
    });
    $('.mt-maincart').hover(
        function () {
            $(this).addClass('cart-active').find('.ajaxcart').stop().delay(200).slideDown();
        },
        function () {
            $(this).removeClass('cart-active').find('.ajaxcart').stop().delay(200).slideUp();
        }
    );
    if($('.product-view').length>0 && $('.option-file').length == 0 && $('.btn-cart-mobile').length == 0){
        productAddToCartForm.submit = function(button, url) {
            if (this.validator.validate()) {
                var form = this.form;
                var oldUrl = form.action;
                if (url) {
                    form.action = url;
                }
                var e = null;
                if (!url) {
                    url = $('#product_addtocart_form').attr('action');
                }
                url = url.replace("checkout/cart","ajaxcart/index");
                var data = $('#product_addtocart_form').serialize();
                data += '&isAjax=1';
                try {
                    if($('#qty').val()==0){
                        if($('.error_qty').length==0){
                            $('<span/>').html('The quantity not zero!')
                                .addClass('error_qty')
                                .appendTo($('.add-to-cart'));
                        }
                    }else{
                        $('.error_qty').remove();
                        $("span.textrepuired").html('');
                        $('.mt-cart-loading').show();
                        $('.ajaxcart').hide();
                        $('.mt-cart-label').hide();
                        $.ajax( {
                            url : url,
                            dataType : 'json',
                            type : 'post',
                            data : data,
                            success : function(data) {
                                window.parent.setAjaxData(data,true);
                                $('.mt-cart-loading').hide();
                                $('.mt-cart-label').show();
                                $.colorbox.close();
                                setTimeout(function () {
                                    $mt('.ajaxcart').slideUp();
                                }, 4000);
                                if (button && button != 'undefined') {
                                    button.disabled = false;
                                }
                            }
                        });
                    }
                } catch (e) {
                }
                this.form.action = oldUrl;
                if (e) {
                    throw e;
                }

            }
            return false;
        }.bind(productAddToCartForm);
    }
});
function setAjaxData(data,iframe){
    if(data.status == 'ERROR'){
        alert(data.message);
    }else{
        $mt('.mt-maincart').html('');
        if($mt('.mt-maincart')){
            $mt('.mt-maincart').append(data.output);
        }
        $mt.colorbox.close();
    }
    showPPopup(data.message);
}
function showPPopup(message){
    $mt('body').append('<div class="message-alert"></div>');
    $mt('.message-alert').html(message).append('<button></button>');
    $mt('.message-alert').animate({opacity:1}, 300);
    $mt('button').click(function () {
        $mt('.message-alert').animate({opacity:0}, 300);
    });
    $mt('.message-alert').animate({opacity: 1},'300', function () {
        setTimeout(function () {
            $mt('.message-alert').animate({opacity: 0},'300', function () {
                $mt(this).animate({opacity:0},300, function(){ $mt(this).detach(); })
            });
        }, 9000)
    });
}
function setLocation(url)
{
    var checkUrl = (url.indexOf('checkout/cart') > -1);
    var pass = true;
    if($mt('body').find('.btn-cart-mobile').length > 0){
        pass = false;
    }
    if(checkUrl && pass){
        $mt('.mt-cart-loading').show();
        $mt('.ajaxcart').hide();
        $mt('.mt-cart-label').hide();
        data = '&isAjax=1&qty=1';
        url = url.replace("checkout/cart","ajaxcart/index");
        try {
            $mt.ajax( {
                url : url,
                dataType : 'json',
                data: data,
                type: 'post',
                success : function(data) {
                    setAjaxData(data);
                    $mt('.mt-cart-loading').hide();
                    $mt('.mt-cart-label').show();
                    $mt('body').find('img.imgfly').remove();
                    $mt('.header .links').replaceWith(data.toplink);
                }
            });
        } catch (e) {
        }
        return false;
    }
    return window.location.href=url;
}
function deletecart(url){
    $mt('.mt-cart-loading').show();
    $mt('.ajaxcart').hide();
    $mt('.mt-cart-label').hide();
    if (confirm("Are you sure you would like to remove this item from the shopping cart?")) {
        data = '&isAjax=1&qty=1';
        url = url.replace("checkout/cart","ajaxcart/index");
        $mt.ajax( {
            url : url,
            dataType : 'json',
            data: data,
            type: 'post',
            success : function(data) {
                setAjaxData(data,false);
                $mt('.mt-cart-loading').hide();
                $mt('.mt-cart-label').show();
                $mt('.header .links').replaceWith(data.toplink);
            }
        });
    }else{
        $mt('.mt-cart-loading').hide();
        $mt('.mt-cart-label').hide();
    }
}
