jQuery(document).ready(function($){
	CART.addCart();
	CART.updatCart();
	CART.deleletAllItem();
	CART.delelteOneItem();
	CART.paymentOrder();
});

CART={
	addCart:function(){
		jQuery('#submitBuy').click(function(){
			var url = BASE_URL + 'them-vao-gio-hang.html';
			var pid = jQuery(this).attr('data-pid');
			var psize = jQuery('#productSize').val();
			var pnum = jQuery('#productNum').val();
			var _token = jQuery('input[name="_token"]').val();
			if(pid > 0 && pnum >0 && psize != 0){
				jQuery('body').append('<div class="loading"></div>');
				jQuery.ajax({
					type: "POST",
					url: url,
					data: "pid="+encodeURI(pid) + "&psize=" + encodeURI(psize) + "&pnum=" + encodeURI(pnum) + "&_token=" + encodeURI(_token),
					success: function(data){
						jQuery('body').find('div.loading').remove();
						if(data == 1){
							jAlert('Đã thêm vào giỏ hàng!', 'Thông báo');
							window.location.reload();
						}else{
							if(data != ''){
								jAlert(data, 'Cảnh báo');
							}else{
								jAlert('Không tồn tại sản phẩm!', 'Cảnh báo');
							}
							return false;
						}
					}
				});
			}else{
				jQuery('#productSize').addClass('error');
				jAlert('Bạn vui lòng chọn kích thước sản phẩm!', 'Cảnh báo');
				return false;
			}
		});
	},
	updatCart:function(){
		jQuery('#updateCart').click(function(){
			var updateCart = BASE_URL + 'gio-hang.html';
			jConfirm('Bạn có muốn cập nhật đơn hàng không [OK]:Đồng ý [Cancel]:Bỏ qua ?', 'Xác nhận', function(r) {
				if(r){
					jQuery('#txtFormShopCart').attr('action', updateCart).submit();
				}
			});
			return true;
		});
	},
	deleletAllItem:function(){
		jQuery('#dellAllCart').click(function(e){
			var url = BASE_URL + 'xoa-gio-hang.html';
			var all = jQuery(this).attr('data');
			var _token = jQuery('input[name="_token"]').val();
			jConfirm('Bạn có muốn xóa không [OK]:Đồng ý [Cancel]:Bỏ qua ?', 'Xác nhận', function(r) {
				if(r){
					jQuery.ajax({
						type: "POST",
						url: url,
						data: "all=" + encodeURI(all) + "&_token="+encodeURI(_token),
						success: function(data){
							if(data != ''){
								window.location.reload();
							}
						}
					});	
				}
			});
			return true;
		});	
	},
	delelteOneItem:function(){
		jQuery('.delOneItemCart').click(function(){
			var url = BASE_URL + 'xoa-mot-san-pham-trong-gio-hang.html';
			var pid = jQuery(this).attr('data');
			var psize = jQuery(this).attr('data-size');
			var _token = jQuery('input[name="_token"]').val();
			jConfirm('Bạn có muốn xóa không [OK]:Đồng ý [Cancel]:Bỏ qua ?', 'Xác nhận', function(r) {
				if(r){
					jQuery.ajax({
						type: "POST",
						url: url,
						data: "pid="+encodeURI(pid) + "&psize="+encodeURI(psize) + "&_token="+encodeURI(_token),
						success: function(data){
							if(data != ''){
								window.location.reload();
							}
						}
					});	
				}
			});
			return true;	
		});	
	},
	paymentOrder:function(){
		jQuery('#submitPaymentOrder').click(function(){
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
			if(valid==false){
				return false;
			}
			return valid;
		});
	}
}