jQuery(document).ready(function($){
	ADMIN.back();
	ADMIN.checkAllItem();
	ADMIN.deleteItem();
	ADMIN.restoreItem();
	ADMIN.addSizeProduct();
    ADMIN.clickAllChecked();

    ADMIN.getListDictrictId();
	ADMIN.getListWardId();
	ADMIN.f5GetListWardId();

	ADMIN.addAutoCodeProductOrder();
    ADMIN.delteItemCodeInOrderDetail();
    ADMIN.autocompleteCodeProductInOrderDetail();
    ADMIN.btnChangeOrderStatusFast();
    ADMIN.clickLoadOrderCustomer();
    ADMIN.changeProductSale();

    ADMIN.commentInOneOrderPopup();
    ADMIN.commentInOneOrderView();
    ADMIN.commentInOneOrderDelete();

    ADMIN.printOnceOrder();
    ADMIN.printOrdersChecked();
    ADMIN.btnConfirmOrderPrint();
    ADMIN.btnDestroyConfirmOrderPrint();


    ADMIN.commentInOneCommentProductView();
    ADMIN.commentInOneCommentProductDelete();
});

ADMIN = {
	deleteItem:function(){
		jQuery('a#deleteMoreItem').click(function(){
			var total = jQuery( "input:checked" ).length;
			if(total==0){
				jAlert('Vui lòng chọn ít nhất 1 bản ghi để xóa!', 'Thông báo');
				return false;
			}else{
				jConfirm('Bạn muốn xóa [OK]:Đồng ý [Cancel]:Bỏ qua?)', 'Xác nhận', function(r) {
					if(r){
						jQuery('form#formListItem').submit();
						return true;
					}
				});
				return false;
			}
		});
	},
	restoreItem:function(){
		jQuery('a#restoreMoreItem').click(function(){
			var total = jQuery( "input:checked" ).length;
			if(total==0){
				jAlert('Vui lòng chọn ít nhất 1 bản ghi để khôi phục!', 'Thông báo');
				return false;
			}else{
				jConfirm('Bạn muốn khôi phục [OK]:Đồng ý [Cancel]:Bỏ qua?)', 'Xác nhận', function(r) {
					if(r){
						jQuery('form#formListItem').attr("action", BASE_URL+"admin/trash/restore");
                        jQuery('form#formListItem').submit();
						return true;
					}
				});
				return false;
			}
		});
	},
	back:function(){
		jQuery("button[type=reset]").click(function(){
	   		window.history.back();
	   });
	},
	checkAllItem:function(){
		jQuery("input#checkAll").click(function(){
            var checkedStatus = this.checked;
            jQuery("input.checkItem").each(function(){
                this.checked = checkedStatus;
            });
        });
	},
	checkAllClass:function(strs){
		if(strs != ''){
			jQuery("input." + strs).click(function(){
				var checkedStatus = this.checked;
				jQuery("input.item_" + strs).each(function(){
					this.checked = checkedStatus;
				});
			});
		}
	},
	clickAllChecked:function(){
		jQuery(".btnClickAllAction").click(function(){
			 var checkBoxes = $("input[class*='item_']");
		         checkBoxes.prop("checked", !checkBoxes.prop("checked"));
		});
	},
    addSizeProduct:function(){
        jQuery('.btn-add-size').click(function(){
            var item_size = '<div class="p-size-item"><div class="p-size"><span>Cỡ</span><input name="size[]" class=""></div><div class="p-num"><span>Số lượng</span><input name="num[]" class=""></div><span class="del-size">Xóa</span></div>';
            jQuery('.list-size').append(item_size);
            jQuery('.del-size').click(function(){
                jQuery(this).parent('div.p-size-item').remove();
            });
        });
        jQuery('.del-size').click(function(){
            jQuery(this).parent('div.p-size-item').remove();
        });
    },
    //P D W
    getListDictrictId:function(){
        jQuery('#listProviceId').change(function(){
            var proviceId = $(this).val();
            var _token = $('input[name="_token"]').val();
            if(proviceId > -1){
                var url = BASE_URL+'admin/dictrict/ajaxGetDictrictByProvice';
                jQuery('#listDictrictId').html('');
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "proviceId="+encodeURI(proviceId) + '&_token='+_token,
                    success: function(data){
                        if(data != ''){
                            data = jQuery.parseJSON(data);
                            jQuery('#listDictrictId').append(data);
                            return false;
                        }
                    }
                });
            }
        });
        //Ward edit
        var proviceId = $('#listProviceId').val();
        var dictrictId = $('#listDictrictId').attr('data');
        var _token = $('input[name="_token"]').val();
        if(proviceId > -1){
            var url = BASE_URL+'admin/dictrict/ajaxGetDictrictByProvice';
            jQuery('#listDictrictId, #listDictrictId').html('');
            jQuery.ajax({
                type: "POST",
                url: url,
                data: "proviceId="+encodeURI(proviceId) + "&dictrictId="+encodeURI(dictrictId) + '&_token='+_token,
                success: function(data){
                    if(data != ''){
                        data = jQuery.parseJSON(data);
                        jQuery('#listDictrictId').append(data);
                        if($('.page-content-box').hasClass('adminOrder')){
                            ADMIN.f5GetListWardId();
                        }
                        return false;
                    }
                }
            });
        }
    },
    getListWardId:function(){
        jQuery('#listDictrictId').change(function(){
            var dictrictId = $(this).val();
            var _token = $('input[name="_token"]').val();
            if(dictrictId > -1){
                var url = BASE_URL+'admin/ward/ajaxGetWardByDictrict';
                jQuery('#listWardId').html('');
                ADMIN.changeDictrictGetPriceShip(dictrictId);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "dictrictId="+encodeURI(dictrictId) + '&_token='+_token,
                    success: function(data){
                        if(data != ''){
                            data = jQuery.parseJSON(data);
                            jQuery('#listWardId').append(data);
                            return false;
                        }
                    }
                });
            }
        });
    },
    f5GetListWardId:function(){
        //ward edit
        var dictrictId = $('#listDictrictId').attr('data');
        var wardId = $('#listWardId').attr('data');
        var _token = $('input[name="_token"]').val();
        if(dictrictId > -1){
            var url = BASE_URL+'admin/ward/ajaxGetWardByDictrict';
            jQuery('#listWardId').html('');
            jQuery.ajax({
                type: "POST",
                url: url,
                data: "dictrictId="+encodeURI(dictrictId) + "&wardId="+encodeURI(wardId) + '&_token='+_token,
                success: function(data){
                    if(data != ''){
                        data = jQuery.parseJSON(data);
                        jQuery('#listWardId').append(data);
                        return false;
                    }
                }
            });
        }
    },
    clickLoadOrderCustomer:function(){
        jQuery('.clickLoadOrderCustomer').click(function(){
            var orderPhone = $('input[name="order_phone"]').val();
            var _token = $('input[name="_token"]').val();
            if(orderPhone != ''){
                var url = BASE_URL+'admin/emailCustomer/ajaxGetOrderCustomer';
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "orderPhone="+encodeURI(orderPhone) + "&_token="+encodeURI(_token),
                    success: function(data){
                        if(data != ''){
                            data = jQuery.parseJSON(data);
                            jQuery('input[name="order_title"]').val(data.customer_full_name);
                            jQuery('input[name="order_address"]').val(data.customer_address);
                            jQuery('input[name="order_name_facebook"]').val(data.customer_name_facebook);
                            jQuery('input[name="order_nick_facebook"]').val(data.customer_link_facebook);
                            jQuery('input[name="order_email"]').val(data.customer_email);
                            var customer_provice_id = data.customer_provice_id;
                            var customer_dictrict_id = data.customer_dictrict_id;
                            var customer_ward_id = data.customer_ward_id;
                            if(customer_provice_id > 0){
                                jQuery('#listProviceId option[value="'+customer_provice_id+'"]').attr('selected', 'selected');
                                var url = BASE_URL+'admin/dictrict/ajaxGetDictrictByProvice';
                                jQuery('#listDictrictId').html('');
                                jQuery.ajax({
                                    type: "POST",
                                    url: url,
                                    data: "proviceId="+encodeURI(customer_provice_id) + "&_token="+encodeURI(_token),
                                    success: function(data){
                                        if(data != ''){
                                            data = jQuery.parseJSON(data);
                                            jQuery('#listDictrictId').append(data);
                                            if(customer_dictrict_id > 0){
                                                jQuery('#listDictrictId option[value="'+customer_dictrict_id+'"]').attr('selected', 'selected');
                                                var url = BASE_URL+'admin/ward/ajaxGetWardByDictrict';
                                                jQuery('#listWardId').html('');
                                                jQuery.ajax({
                                                    type: "POST",
                                                    url: url,
                                                    data: "dictrictId="+encodeURI(customer_dictrict_id) + "&_token="+encodeURI(_token),
                                                    success: function(data){
                                                        if(data != ''){
                                                            data = jQuery.parseJSON(data);
                                                            jQuery('#listWardId').append(data);
                                                            if(customer_ward_id > 0){
                                                                jQuery('#listWardId option[value="'+customer_ward_id+'"]').attr('selected', 'selected');
                                                            }
                                                            return false;
                                                        }
                                                    }
                                                });
                                            }
                                            return false;
                                        }
                                    }
                                });
                            }
                            return false;
                        }
                    }
                });
            }
        });
    },
    //Add code click
    addAutoCodeProductOrder:function(){
        jQuery('#click-add-pcode').click(function(){
            var item_pcode = '<div class="item-product"><input type="hidden" name="pid[]"  autocomplete="off"/>Mã<span class="red">*</span> <input name="pcode[]" type="text" autocomplete="off"> Size<span class="red">*</span> <input type="text" name="psize[]" autocomplete="off"/> SL<span class="red">*</span> <input name="pnum[]" type="text" autocomplete="off" value="1"> <span class="del-pcode" title="Xóa">X</span></div>';
            jQuery('#list-pcode').append(item_pcode);
            ADMIN.delteItemCodeInOrderDetail();
            ADMIN.autocompleteCodeProductInOrderDetail();
        });
    },
    //Delete item code
    delteItemCodeInOrderDetail:function(){
        jQuery('.del-pcode').click(function(){
            var _this = jQuery(this);
            jConfirm('Bạn muốn xóa [OK]:Đồng ý [Cancel]:Bỏ qua?)', 'Xác nhận', function(r) {
                if(r){
                    _this.parent('div.item-product').remove();
                }
            });
        });
    },
    autocompleteCodeProductInOrderDetail:function(){
        jQuery('#list-pcode input[name *= "pcode"]').unbind().keyup(function(e){
            var key_code = e.which || e.keyCode;
            var _token = $('input[name="_token"]').val();
            if(key_code >= 48 && key_code <= 90){
                var url = BASE_URL + 'admin/product/ajaxLoadItemCodeProductInOrderDetail';
                var keyword = jQuery(this).val();
                jQuery('ul.listCode').remove();
                var dataId = [];
                var i = 0;
                jQuery('#list-pcode input[name *= "pid"]').each(function(){
                    dataId[i] = jQuery(this).val();
                    i++;
                });
                var input_current = jQuery(this);
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "keyword="+encodeURI(keyword) + "&dataId="+encodeURI(dataId)  + "&_token="+encodeURI(_token),
                    success: function(data){
                        if(data!=''){
                            var parent = input_current.parents('.item-product');
                            parent.css('position','relative').append(data);
                            jQuery('ul.listCode').show();
                            jQuery('ul.listCode li').click(function(){
                                var code = jQuery(this).attr('datacode');
                                parent.find('input[name *= "pcode"]').val(code);
                                var pid = jQuery(this).attr('dataid');
                                parent.find('input[name *= "pid"]').val(pid);
                                jQuery('ul.listCode').remove();
                            });
                            return false;
                        }
                    }
                });
            }
        });
    },
    btnChangeOrderStatusFast:function(){
        jQuery('#btnChangeOrderStatusFast').click(function(){
            var dataId = [];
            var i = 0;
            $("input[name*='checkItem']").each(function () {
                if ($(this).is(":checked")) {
                    dataId[i] = $(this).val();
                    i++;
                }
            });
            if(dataId.length == 0) {
                jAlert('Click chọn ít nhất 1 đơn hàng chuyển trạng thái.', 'Thông báo');
                return false;
            }
            var status = jQuery('select[name="order_status_change_fast"]').val();
            var _token = $('input[name="_token"]').val();
            jConfirm('Bạn muốn cập nhật trạng thái đơn hàng [OK]:Yes[Cancel]:No?', 'Xác nhận', function(r) {
                if(r){
                    url = BASE_URL + 'admin/order/btnChangeOrderStatusFast';
                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: {dataId:dataId, status:status, _token:_token},
                        success: function(data){
                            if(data != ''){
                                jAlert(data, 'Thông báo');
                            }else{
                                jAlert('Cập nhật trạng thái thành công.', 'Thông báo');
                            }
                            window.location.reload();
                            return false;
                        }
                    });
                }
            });
        });
    },
    changeDictrictGetPriceShip:function(dictrictId){
        if(dictrictId > 0){
            var url = BASE_URL+'admin/order/changeDictrictGetPriceShip';
            var order_price_post = jQuery('input[name="order_price_post"]').val();
            var _token = $('input[name="_token"]').val();
            if(order_price_post == '') {
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "dictrictId=" + encodeURI(dictrictId) + "&_token=" + encodeURI(_token),
                    success: function (data) {
                        if (data != '') {
                            jQuery('input[name*="order_price_post"]').val(data);
                            return false;
                        }
                    }
                });
            }
        }
    },
	changeProductSale:function(){
		jQuery('#product_sale').change(function(){
			 var dataId = [];
		     var i = 0;
			 var _token = $('input[name="_token"]').val();
		     $("input[name*='checkItem']").each(function () {
	            if ($(this).is(":checked")) {
	                dataId[i] = $(this).val();
	                i++;
	            }
	        });
		    if(dataId.length == 0) {
		    	jAlert('Vui lòng chọn ít nhất 1 bản ghi để chuyển trạng thái!', 'Thông báo');
	            return false;
	        }
		    var valueChange = $(this).val();
		    if(parseInt(valueChange) == -1){
	            jAlert('Bạn chưa chọn trạng thái để cập nhật!', 'Thông báo');
	            return false;
	        }
		    var url = BASE_URL+'admin/product/change-sale';
		    if(valueChange >- 1){
		    	jConfirm('Bạn thay đổi trạng thái [OK]:Yes[Cancel]:No?', 'Xác nhận', function(r) {
					if(r){
						jQuery.ajax({
							type: "POST",
							url: url,
							data: {listId:dataId, valueChange:valueChange, _token:_token},
							success: function(data){
								window.location.reload();
							}
						});
					}
		    	});
		    }
		});
	},
    //Comment
    commentInOneOrderView:function(){
        jQuery('.txtclicknote').click(function(){
            var frmcomment = jQuery('#frmcomment').val();
            var orderId = jQuery('input#id_hiden').val();
            var url = BASE_URL + 'admin/order/ajaxcomment';
            var _token = $('input[name="_token"]').val();

            if(frmcomment==''){
                jQuery('#frmcomment').addClass('error');
                return false;
            }else{
                if(orderId > 0){
                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: "frmcomment="+encodeURI(frmcomment) + "&orderId="+encodeURI(orderId) + "&_token="+encodeURI(_token),
                        success: function(data){
                            if(data != ''){
                                data = jQuery.parseJSON(data);
                                jQuery('.list-comment ul').append(data);
                                jQuery('#frmcomment').val('');
                                ADMIN.commentInOneOrderDelete();
                                return false;
                            }
                        }
                    });
                }
            }
        });
    },
    commentInOneOrderPopup:function(){
        jQuery('a.item-comment').unbind("click").click(function(){
            jQuery('#sys_PopupCommentOrder').modal('show');
            var orderId = jQuery(this).attr('rel');
            var _token = $('input[name="_token"]').val();
            jQuery('.OrderIdComment').attr('data', orderId);

            //Get all comment of order
            var url_all_comment = BASE_URL + 'admin/order/popupajaxgetallcommentorder';
            jQuery('.modal-dialog-comment .list-comment ul').html('');
            jQuery.ajax({
                type: "POST",
                url: url_all_comment,
                data: "orderId="+encodeURI(orderId) + "&_token="+encodeURI(_token),
                success: function(data){
                    if(data!=''){
                        jQuery('.modal-dialog-comment .list-comment ul').append(data);
                        jQuery('#frmcomment').val('');
                        ADMIN.commentInOneOrderDelete();
                        return false;
                    }
                }
            });

            //Send comment
            jQuery('.txtclickcomment').unbind("click").click(function(){
                var frmcomment = jQuery('#frmcomment').val();
                var orderId = jQuery('span.OrderIdComment').attr('data');
                var url = BASE_URL + 'admin/order/ajaxcomment';
                var _token = $('input[name="_token"]').val();
                if(frmcomment==''){
                    jQuery('#frmcomment').addClass('error');
                    return false;
                }else{
                    if(orderId > 0){
                        jQuery.ajax({
                            type: "POST",
                            url: url,
                            data: "frmcomment="+encodeURI(frmcomment) + "&orderId="+encodeURI(orderId) + "&_token="+encodeURI(_token),
                            success: function(data){
                                if(data != ''){
                                    data = jQuery.parseJSON(data);
                                    jQuery('.list-comment ul').append(data);
                                    jQuery('#frmcomment').val('');
                                    ADMIN.commentInOneOrderDelete();
                                    return false;
                                }
                            }
                        });
                    }
                }
            });

            //Get detail order click
            var url_detail_order = BASE_URL + 'admin/order/popupajaxgetoneorder';
            var orderId = jQuery('span.OrderIdComment').attr('data');
            var _token = $('input[name="_token"]').val();
            jQuery('.detail-once-order').html('');
            jQuery.ajax({
                type: "POST",
                url: url_detail_order,
                data: "orderId="+encodeURI(orderId) + "&_token="+encodeURI(_token),
                success: function(data){
                    if(data!=''){
                        jQuery('.detail-once-order').append(data);
                        return false;
                    }
                }
            });
        });
    },
    commentInOneOrderDelete:function(){
        jQuery('.comment-delete').click(function(){
            var id = jQuery(this).attr('data');
            var url = BASE_URL + 'admin/order/ajaxdeletecomment';
            var _token = $('input[name="_token"]').val();
            removeItem = jQuery(this);
            if(id > 0){
                jConfirm('Bạn muốn xóa [OK]:Yes[Cancel]:No?', 'Xác nhận', function(r) {
                    if(r){
                        jQuery.ajax({
                            type: "POST",
                            url: url,
                            data: "id="+encodeURI(id) + "&_token="+encodeURI(_token),
                            success: function(data){
                                if(data == 'ok'){
                                    removeItem.parent('li').remove();
                                    return false;
                                }
                            }
                        });
                    }
                });
            }
        });
    },
    printOnceOrder:function(){
        jQuery('a.item-print').unbind("click").click(function(event){
            var orderId = jQuery(this).attr('rel');
            var url = BASE_URL+'admin/order/printer/'+orderId;
            event.preventDefault();
            window.open(url, '_blank');
        });
    },
    printOrdersChecked:function(){
        jQuery('#btnOrderPrint').unbind("click").click(function(event){
            var dataId = [];
            var i = 0;
            $("input[name*='checkItem']").each(function () {
                if ($(this).is(":checked")) {
                    dataId[i] = $(this).val();
                    i++;
                }
            });
            if(dataId.length == 0) {
                jAlert('Click chọn ít nhất 1 đơn hàng để in.', 'Thông báo');
                return false;
            }
            jConfirm('Bạn muốn in [OK]:Yes[Cancel]:No?', 'Xác nhận', function(r) {
                if(r){
                    var url = BASE_URL + 'admin/order/btnOrdersPrint?dataId=' + dataId;
                    event.preventDefault();
                    window.open(url, '_blank');
                }
            });
        });
    },
    btnConfirmOrderPrint:function(){
        jQuery('#btnConfirmOrderPrint').unbind().click(function(){
            var dataId = [];
            var i = 0;
            var _token = $('input[name="_token"]').val();
            $("input[name*='checkItem']").each(function () {
                if ($(this).is(":checked")) {
                    dataId[i] = $(this).val();
                    i++;
                }
            });
            if(dataId.length == 0) {
                jAlert('Click chọn ít nhất 1 đơn hàng để xác nhận in.', 'Thông báo');
                return false;
            }
            jConfirm('Bạn muốn xác nhận đơn hàng đã in [OK]:Yes[Cancel]:No?', 'Xác nhận', function(r) {
                if(r){
                    url = BASE_URL + 'admin/order/btnConfirmOrderPrint';
                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: {dataId:dataId, _token:_token},
                        success: function(data){
                            jAlert('Xác nhận đơn hàng đã in thành công.', 'Thông báo');
                            window.location.reload();
                            return false;
                        }
                    });
                }
            });
        })
    },
    btnDestroyConfirmOrderPrint:function(){
        $('.btnDestroyConfirmOrderPrint').unbind().click(function(){
            var dataId = $(this).attr('dataid');
            var _token = $('input[name="_token"]').val();
            jConfirm('Bạn muốn xác nhận hủy in [OK]:Yes[Cancel]:No?', 'Xác nhận', function(r) {
                if(r){
                    url = BASE_URL + 'admin/order/btnDestroyConfirmOrderPrint';
                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: "dataId="+encodeURI(dataId) + "&_token="+encodeURI(_token),
                        success: function(data){
                            jAlert('Xác nhận hủy in thành công.', 'Thông báo');
                            window.location.reload();
                            return false;
                        }
                    });
                }
            });
        });
    },
    //Comment product
    commentInOneCommentProductView:function(){
        jQuery('.txtclicknoteProduct').click(function(){
            var frmcomment = jQuery('#frmcomment').val();
            var commentId = jQuery('input#id_hiden').val();
            var url = BASE_URL + 'admin/comment-product/ajaxcomment';
            var _token = $('input[name="_token"]').val();

            if(frmcomment==''){
                jQuery('#frmcomment').addClass('error');
                return false;
            }else{
                if(commentId > 0){
                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: "frmcomment="+encodeURI(frmcomment) + "&commentId="+encodeURI(commentId) + "&_token="+encodeURI(_token),
                        success: function(data){
                            if(data != ''){
                                data = jQuery.parseJSON(data);
                                jQuery('.list-comment ul').append(data);
                                jQuery('#frmcomment').val('');
                                ADMIN.commentInOneCommentProductDelete();
                                return false;
                            }
                        }
                    });
                }
            }
        });
    },
    commentInOneCommentProductDelete:function(){
        jQuery('.comment-product-delete').click(function(){
            var id = jQuery(this).attr('data');
            var url = BASE_URL + 'admin/comment-product/ajaxdeletecomment';
            var _token = $('input[name="_token"]').val();
            removeItem = jQuery(this);
            if(id > 0){
                jConfirm('Bạn muốn xóa [OK]:Yes[Cancel]:No?', 'Xác nhận', function(r) {
                    if(r){
                        jQuery.ajax({
                            type: "POST",
                            url: url,
                            data: "id="+encodeURI(id) + "&_token="+encodeURI(_token),
                            success: function(data){
                                if(data == 'ok'){
                                    removeItem.parent('li').remove();
                                    window.location.reload();
                                    return false;
                                }
                            }
                        });
                    }
                });
            }
        });
    },
}