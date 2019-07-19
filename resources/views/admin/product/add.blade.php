@extends('admin.layout.html')
@section('header')
    @include('admin.block.header')
@stop
@section('left')
    @include('admin.block.left')
@stop
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="{{URL::route('admin.dashboard')}}">Trang chủ</a>
                </li>
                <li class="active">@if($id==0)Thêm mới @else Sửa @endif sản phẩm</li>
            </ul>
        </div>
        <div class="page-content">
            <div class="col-xs-12">
                <div class="row">
                    @if($error != '')
                        <div class="alert-admin alert alert-danger">{!! $error !!}</div>
                    @endif
                    <form class="form-horizontal paddingTop30" name="txtForm" action="" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="tabbable">
                                <ul class="nav nav-tabs padding-18">
                                    <li id="tab-product-1" class="active" data-tabs="tabProduct">
                                        <a data-toggle="tab" href="#tab-pane-product-1">
                                            <i class="blue fa fa-file-word-o bigger-120"></i>
                                            <span class="hidden-320 ng-binding">Thông tin hàng hóa</span>
                                        </a>
                                    </li>
                                    <li id="tab-product-2" data-tabs="tabProduct">
                                        <a data-toggle="tab" href="#tab-pane-product-2">
                                            <i class="blue fa fa-server bigger-120"></i>
                                            <span class="hidden-320 ng-binding">Thông tin web</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content no-border create-product">
                                <div id="tab-pane-product-1" data-tab-pane="tabProduct" class="tab-pane in active">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Danh mục</label>
                                                <select class="form-control input-sm" name="product_catid">
                                                    {!! $optionCategoryProduct !!}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Tiêu đề<span>*</span></label>
                                                <input type="text" class="form-control input-sm" name="product_title" value="@if(isset($data['product_title'])){{stripcslashes($data['product_title'])}}@endif">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Mã<span>*</span></label>
                                                <input type="text" class="form-control input-sm" name="product_code" value="@if(isset($data['product_code'])){{$data['product_code']}}@endif">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Mã nhà sản xuất</label>
                                                <input type="text" class="form-control input-sm" name="product_code_factory" value="@if(isset($data['product_code_factory'])){{$data['product_code_factory']}}@endif">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Giá nhập</label>
                                                <div class="controls">
                                                    <input type="text" class="form-control input-sm formatMoney" name="product_price_input" value="@if(isset($data['product_price_input'])){{$data['product_price_input']}}@endif" data-v-max="999999999999999" data-v-min="0" data-a-sep="." data-a-dec="," data-a-sign=" đ" data-p-sign="s">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Giá thị trường</label>
                                                <div class="controls">
                                                    <input type="text" class="form-control input-sm formatMoney" name="product_price_normal" value="@if(isset($data['product_price_normal'])){{$data['product_price_normal']}}@endif" data-v-max="999999999999999" data-v-min="0" data-a-sep="." data-a-dec="," data-a-sign=" đ" data-p-sign="s">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Giá bán</label>
                                                <div class="controls">
                                                    <input type="text" class="form-control input-sm formatMoney" name="product_price" value="@if(isset($data['product_price'])){{$data['product_price']}}@endif" data-v-max="999999999999999" data-v-min="0" data-a-sep="." data-a-dec="," data-a-sign=" đ" data-p-sign="s">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Bán sỉ</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm" name="product_wholesale">
                                                        {!! $optionWholesale !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Khuyến mãi</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm" name="product_khuyenmai">
                                                        {!! $optionKhuyenMai !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Giảm giá</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm" name="product_giamgia">
                                                        {!! $optionGiamGia !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Mới</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm" name="product_moi">
                                                        {!! $optionMoi !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Nổi bật</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm" name="product_focus">
                                                        {!! $optionFocus !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Thứ tự</label>
                                                <div class="controls">
                                                    <input type="text" class="form-control input-sm" name="product_order_no" value="@if(isset($data['product_order_no'])){{$data['product_order_no']}}@endif">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Tình trạng hàng</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm" name="product_sale">
                                                        {!! $optionSale !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Nhà cung cấp</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm" name="product_supplier">
                                                        {!! $optionSupplier !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Trạng thái</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm" name="product_status">
                                                        {!! $optionStatus !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="control-group">
                                                <label class="control-label">Kích cỡ</label>
                                                <div class="controls">
                                                    <div class="list-size">
                                                        <?php
                                                        if(isset($data['product_size_no'])){
                                                        $product_size_no = unserialize($data['product_size_no']);
                                                        ?>
                                                        @if(is_array($product_size_no) && !empty($product_size_no))
                                                            @foreach($product_size_no as $size)
                                                                <div class="p-size-item">
                                                                    <div class="p-size"><span>Cỡ</span><input name="size[]" value="{{$size['size'] }}"></div>
                                                                    <div class="p-num"><span>Số lượng</span><input name="num[]" value="{{$size['no'] }}"></div>
                                                                    <span class="del-size">Xóa</span>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                        <?php }else{ ?>
                                                        <div class="p-size-item">
                                                            <div class="p-size"><span>Cỡ</span><input name="size[]"></div>
                                                            <div class="p-num"><span>Số lượng</span><input name="num[]"></div>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <span class="btn-add-size"> Click thêm size...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="tab-pane-product-2" data-tab-pane="tabProduct" class="tab-pane">
                                    <div class="line">
                                        <div class="control-group">
                                            <label class="control-label">Mô tả</label>
                                            <textarea class="form-control input-sm" name="product_intro">@if(isset($data['product_intro'])){{stripcslashes($data['product_intro'])}}@endif</textarea>
                                        </div>
                                        <br/>
                                        <div class="control-group">
                                            <label class="control-label">Ảnh</label>
                                            <a href="javascript:;"class="btn btn-primary link-button" onclick="UploadAdmin.uploadMultipleImages(3);">Upload ảnh</a>
                                            <input name="image_primary" type="hidden" id="image_primary" value="@if(isset($data['product_image'])){{trim($data['product_image'])}}@endif">
                                        </div>
                                        <div class="clearfix"></div><br/>
                                        <div class="control-group">
                                            <!--Hien Thi Anh-->
                                            <ul id="sys_drag_sort" class="ul_drag_sort">
                                                @if(isset($product_image_other))
                                                    @foreach($product_image_other as $k=>$v)
                                                        <li id="sys_div_img_other_{{$k}}">
                                                            <div class="div_img_upload">
                                                                <img src="{{$v['src_img_other']}}" height="80">
                                                                <input type="hidden" id="sys_img_other_{{$k}}" name="img_other[]" value="{{$v['img_other']}}" class="sys_img_other">
                                                                <div class='clear'></div>
                                                                <input type="radio" id="checked_image_{{$k}}" name="checked_image" value="{{$k}}"
                                                                       @if(isset($product_image) && ($product_image == $v['img_other'])) checked="checked" @endif
                                                                       onclick="UploadAdmin.checkedImage('{{$v['img_other']}}','{{$k}}');">
                                                                <label for="checked_image_{{$k}}" style='font-weight:normal'>Ảnh đại diện</label>
                                                                <br/>
                                                                <a href="javascript:void(0);" id="sys_delete_img_other_{{$k}}" onclick="UploadAdmin.removeImage('{{$k}}', '{{$data['product_id']}}', '{{$v['img_other']}}', '3');">Xóa ảnh</a>
                                                                <span style="display: none"><b>{{$k}}</b></span>
                                                            </div>
                                                        </li>
                                                        @if(isset($product_image) && $product_image == $v['img_other'])
                                                            <input type="hidden" id="sys_key_image_primary" name="sys_key_image_primary" value="{{$k}}">
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <input type="hidden" id="sys_key_image_primary" name="sys_key_image_primary" value="-1">
                                                @endif

                                            </ul>
                                            <input name="list1SortOrder" id ='list1SortOrder' type="hidden" />
                                            <!--Hien Thi Anh-->
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="control-group">
                                            <label class="control-label">Nội dung</label>
                                            <div class="controls"><button type="button" onclick="UploadAdmin.getInsertImageContent(3, 'open')" class="btn btn-primary">Chèn ảnh vào nội dung</button></div>
                                            <div class="controls">
                                                <textarea class="form-control input-sm" name="product_content">@if(isset($data['product_content'])){{stripslashes($data['product_content'])}}@endif</textarea>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Meta title</label>
                                            <div class="controls">
                                                <input type="text" class="form-control input-sm" name="meta_title" value="@if(isset($data['meta_title'])){{stripcslashes($data['meta_title'])}}@endif">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Meta keywords</label>
                                            <div class="controls">
                                                <textarea class="form-control input-sm" name="meta_keywords">@if(isset($data['meta_keywords'])){{stripcslashes($data['meta_keywords'])}}@endif</textarea>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Meta description</label>
                                            <div class="controls">
                                                <textarea class="form-control input-sm" name="meta_description">@if(isset($data['meta_description'])){{stripcslashes($data['meta_description'])}}@endif</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-12">
                                <div class="control-group">
                                    {!! csrf_field() !!}
                                    <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
                                    <button type="submit" name="txtSubmit" id="buttonSubmit" class="btn btn-primary">Lưu lại</button>
                                    <button type="reset" class="btn">Bỏ qua</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Popup Upload Img-->
<div class="modal fade" id="sys_PopupUploadImgOtherPro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Upload ảnh</h4>
            </div>
            <div class="modal-body">
                <form name="uploadImage" method="post" action="#" enctype="multipart/form-data">
                    <div class="form_group">
                        <div id="sys_show_button_upload">
                            <div id="sys_mulitplefileuploader" class="btn btn-primary">Upload ảnh</div>
                        </div>
                        <div id="status"></div>

                        <div class="clearfix"></div>
                        <div class="clearfix" style='margin: 5px 10px; width:100%;'>
                            <div id="div_image"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Popup Upload Img-->

<!--Popup chen anh vào noi dung-->
<div class="modal fade" id="sys_PopupImgOtherInsertContent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Click ảnh để chèn vào nội dung</h4>
            </div>
            <div class="modal-body">
                <form name="uploadImage" method="post" action="#" enctype="multipart/form-data">
                    <div class="form_group">
                        <div class="clearfix"></div>
                        <div class="clearfix" style='margin: 5px 10px; width:100%;'>
                            <div id="div_image" class="float_left"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Popup chen anh vào noi dung-->

<script type="text/javascript">
	CKEDITOR.replace('product_intro');
	CKEDITOR.replace('product_content');
    jQuery('.formatMoney').autoNumeric('init');
    //Keo Tha Anh
   jQuery("#sys_drag_sort").dragsort({ dragSelector: "div", dragBetween: true, dragEnd: saveOrder });
    function saveOrder() {
        var data = jQuery("#sys_drag_sort li div span").map(function() { return jQuery(this).children().html(); }).get();
        jQuery("input[name=list1SortOrder]").val(data.join(","));
    };
    //Chen Anh Vao Noi Dung
    function insertImgContent(src){
        CKEDITOR.instances.product_content.insertHtml('<img src="'+src+'"/>');
    }
</script>
@stop