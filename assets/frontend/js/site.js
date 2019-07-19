jQuery(document).ready(function($){
	SITE.menuHead();
	SITE.clickSearch();
	SITE.backTop();
	SITE.contact();
	SITE.setViewType();
    SITE.showTabProductDetail();
    setInterval(function(){SITE.clock()},1000);
});

SITE={
    menuHead:function(){
        $('.mbButtonMenu').click(function(){
            $('.menuTop').addClass('on');
            $('.mbButtonMenu').fadeOut();
            $('.mbButtonMenuL').fadeIn();
            $('.overlay-mb-bg').fadeIn();
        });
        $('.mbButtonMenuL').click(function(){
            $('.menuTop').removeClass('on');
            $('.mbButtonMenuL').fadeOut();
            $('.mbButtonMenu').fadeIn();
            $('.overlay-mb-bg').fadeOut();
        });
    },
	clickSearch:function(){
		$('#clickSearch').click(function(){
			var keyword = $('#txtsearch').val();
            if(keyword == ''){
                $('#txtsearch').focus();
                $('.s').addClass('error');
            }else{
                $('#frmSearch').submit();
            }
		});
	},
	backTop:function(){
		jQuery(window).scroll(function() {
            if(jQuery(window).scrollTop() > 0) {
				jQuery("#back-top").fadeIn();
			} else {
				jQuery("#back-top").fadeOut();
			}
		});
		jQuery("#back-top").click(function(){
			jQuery("html, body").animate({scrollTop: 0}, 1000);
			return false;
		});
	},
	contact:function(){
		jQuery('#submitContact').click(function(){
			var valid = true;
			if(jQuery('#txtName').val() == ''){
				jQuery('#txtName').addClass('error');
				valid = false;
			}else{
				jQuery('#txtName').removeClass('error');
			}
			
			if(jQuery('#txtMobile').val() == ''){
				jQuery('#txtMobile').addClass('error');
				valid = false;
			}else{
				var regex = /^[0-9-+]+$/;
				var phone = jQuery('#txtMobile').val();
				if (regex.test(phone)) {
			        jQuery('#txtMobile').removeClass('error');
			    }else{
					jQuery('#txtMobile').addClass('error');	
				}
			}
			if(jQuery('#txtAddress').val() == ''){
				jQuery('#txtAddress').addClass('error');
				valid = false;
			}else{
				jQuery('#txtAddress').removeClass('error');
			}
			
			if(jQuery('#txtMessage').val() == ''){
				jQuery('#txtMessage').addClass('error');
				valid = false;
			}else{
				jQuery('#txtMessage').removeClass('error');
			}
			if(valid==false){
				return false;
			}
			return valid;
		});
	},
    //Index
    skitterLarge:function(){
        $('.skitter-large').skitter({
            numbers: false,
            dots: true,
            numbers_align: 'center',
            preview: false,
            interval:5000,
        });
    },
    //DetailProduct
	ZoomX:function(){
        $('.jqzoom').jqzoom({
            zoomWidth: 400,
            zoomHeight: 450,
            xOffset: 10,
            zoomType: 'standard',
            lens: true,
            preloadImages: false,
            alwaysOn: false,
            title: false
        });
    },
    ZoomInt:function(s, l, t){
        $('.iMain').html('<a rel="nofollow" title="'+ t +'" href="' + l + '" class="jqzoom"><img width="450" height="auto" alt="'+t+'" src="' + s + '"></a>');
        SITE.ZoomX();
    },
	iThumbClick:function(){
		jQuery('.iThumb a').click(function(e){
            e.preventDefault();
            var title = jQuery(this).attr('title');
			var path = jQuery(this).attr('data');
            SITE.ZoomInt(path, path, title);
		});
	},
	iThumbSlick:function(){
		$(".iThumb .view").slick({
			dots: false,
			infinite: false,
			slidesToShow:5,
			slidesToScroll: 5,
			vertical: true,
		});
	},
    setViewType:function(){
        $('.type-view-item').click(function(){
            var a = $(this),
                b = a.hasClass('type-view-col') ? 2 : 1,
                c = 'active';
            if (a.hasClass(c)) return;
            $('.' + c).removeClass(c);
            a.addClass(c);
            $.cookie(c, b, { path: '/' });
            if (b == 2) {
                $('.line-content-prod').removeClass('view-row').addClass('view-grid');
            }
            else {
                $('.line-content-prod').removeClass('view-grid').addClass('view-row');
            }
        });
        SITE.setViewTypeF5();
    },
    //F5 Page
    setViewTypeF5:function(){
        var c = 'active',
            v = $.cookie(c);
            $('.type-view-item').removeClass('active');
        if (v == '2') {
            $('.line-content-prod').removeClass('view-row').addClass('view-grid');
            $('.type-view-col').addClass(c);
        }
        else {
            $('.line-content-prod').removeClass('view-grid').addClass('view-row');
            $('.type-view-row').addClass(c);
        }
    },
    showTabProductDetail:function(){
        $('.ttabs .tabNormal').click(function(){
            var data = $(this).attr('data');
            $('.ttabs .tabNormal').removeClass('act');
            $(this).addClass('act');
            $('.product-tabs .ictabs').removeClass('act');
            $('.product-tabs .ictabs.'+data).addClass('act');
        });
    },
    ajaxGetCommentInProduct:function(){
        var url = BASE_URL + 'ajaxGetCommentInProduct';
        var pid = jQuery('#pageProduct input[name="pid"]').val();
        var _token = jQuery('input[name="_token"]').val();
        if(pid > 0 && _token != ''){
            jQuery.ajax({
                type: "POST",
                url: url,
                data: "pid="+encodeURI(pid) + "&_token=" + encodeURI(_token),
                success: function(data){
                    if(data != ''){
                        $('.box-comment-show').html(data);
                    }
                }
            });
        }
    },
    btnSubmitComment:function(){
        $('#btnSubmitComment').click(function(){
            var url = BASE_URL + 'ajaxAddCommentInProduct';
            var rqMailPhone = $('.rqMailPhone').val();
            var rqContent = $('.rqContent').val();
            var pid = jQuery('#pageProduct input[name="pid"]').val();
            var _token = jQuery('input[name="_token"]').val();
            var valid = 1;
            if(rqMailPhone == ''){
                jQuery('.rqMailPhone').addClass('error');
                valid = 0;
            }else{
                //Check mail
                var regex = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
                var checkMail = regex.test(rqMailPhone);
                if(!checkMail){
                    jQuery('.rqMailPhone').addClass('error');
                    valid = 0;
                }else{
                    jQuery('.rqMailPhone').removeClass('error');
                    valid = 1;
                }
                //Check phone
                var regexPhone = new RegExp(/[0-9 -()+]+$/);
                var checkPhone = regexPhone.test(rqMailPhone);
                if(!checkPhone){
                    jQuery('.rqMailPhone').addClass('error');
                    valid = 0;
                }else{
                    jQuery('.rqMailPhone').removeClass('error');
                    valid = 1;
                }
            }
            if(rqContent == ''){
                jQuery('.rqContent').addClass('error');
                valid = 0;
            }else{
                jQuery('.rqContent').removeClass('error');
            }
            if(pid > 0 && _token != '' && rqMailPhone != '' && rqContent != '' && valid == 1){
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "pid="+encodeURI(pid) + "&_token=" + encodeURI(_token) + "&rqMailPhone=" + encodeURI(rqMailPhone)+ "&rqContent=" + encodeURI(rqContent),
                    success: function(data){
                        if(data == 'ok'){
                            jAlert('Cảm ơn bạn đã gửi comment!', 'Thông báo');
                            jQuery('.rqMailPhone').val('');
                            jQuery('.rqContent').val('');
                        }else{
                            jAlert(data, 'Thông báo');
                        }
                    }
                });
            }else{
                jAlert('Không đúng định dạng mail hoặc số điện thoại!', 'Thông báo');
            }
        });
    },
    clock:function(){
        var time = new Date();
        var hours = time.getHours().toString();
        if (hours == 0) {hours = '12'} else if (hours.length == 1) {hours = '0' + hours};
        var minutes = time.getMinutes().toString();
        if (minutes.length == 1) {minutes = '0' + minutes};
        var seconds = time.getSeconds().toString();
        if (seconds.length == 1) {seconds = '0' + seconds};
        var string = hours + ' : ' + minutes + ' : ' + seconds;
        jQuery('#clock').html(string);
    },
}