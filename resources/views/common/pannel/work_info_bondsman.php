<?php if ($edit['bondsman'] == true) {?>
<div class="panel panel-default bondsman_div <?php echo $detail['customer_has_bondsman'] !== '1' ? 'hide' : ''?>" >
    <div class="panel-heading">
        <h3 class="panel-title">
            担保人信息
        </h3>
    </div>
    <div class="panel-body">
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">担保人姓名</span>
                    <input name="bondsman_name" type="text" class="form-control bondsman_name" value="<?php echo $detail['bondsman_name']?>" placeholder="请输入姓名">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">身份证号</span>
                    <input name="bondsman_certificate_number" type="text" maxlength="18" onKeyUp="value=value.replace(/[^\d|xX]/g,'')" class="form-control bondsman_certificate_number" value="<?php echo $detail['bondsman_certificate_number']?>" placeholder="请输入身份证号">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">手机号</span>
                    <input name="bondsman_telephone" type="text" maxlength="11" onKeyUp="value=value.replace(/[^\d]/g,'')" class="form-control bondsman_telephone" value="<?php echo $detail['bondsman_telephone']?>" placeholder="请输入手机号">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">工作单位</span>
                    <input name="bondsman_company_name" type="text" class="form-control bondsman_company_name" value="<?php echo $detail['bondsman_company_name']?>" placeholder="请输入工作单位">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">单位电话</span>
                    <input name="bondsman_company_telephone" type="text" class="form-control bondsman_company_telephone" value="<?php echo $detail['bondsman_company_telephone']?>" placeholder="请输入单位电话">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon">单位地址</span>
                    <input name="bondsman_company_address" type="text" class="form-control bondsman_company_address" value="<?php echo $detail['bondsman_company_address']?>" placeholder="请输入单位详细地址">
                </div>
            </div>
        </div>

    </div>
</div>
<?php } else {?>
<div class="panel panel-default bondsman_div <?php echo $detail['customer_has_bondsman'] !== '1' ? 'hide' : ''?>">
    <div class="panel-heading">
        <h3 class="panel-title">
            担保人信息
        </h3>
    </div>
    <div class="panel-body">
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">担保人姓名</span>
                    <input name="bondsman_name" type="text" class="form-control bondsman_name" value="<?php echo $detail['bondsman_name']?>" placeholder="" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">身份证号</span>
                    <input name="bondsman_certificate_number" type="text" maxlength="18" onKeyUp="value=value.replace(/[^\d|xX]/g,'')" class="form-control bondsman_certificate_number" value="<?php echo $detail['bondsman_certificate_number']?>" placeholder="" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">手机号</span>
                    <input name="bondsman_telephone" type="text" maxlength="11" onKeyUp="value=value.replace(/[^\d]/g,'')" class="form-control bondsman_telephone" value="<?php echo $detail['bondsman_telephone']?>" placeholder="" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">工作单位</span>
                    <input name="bondsman_company_name" type="text" class="form-control bondsman_company_name" value="<?php echo $detail['bondsman_company_name']?>" placeholder="" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">单位电话</span>
                    <input name="bondsman_company_telephone" type="text" class="form-control bondsman_company_telephone" value="<?php echo $detail['bondsman_company_telephone']?>" placeholder="" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon">单位地址</span>
                    <input name="bondsman_company_address" type="text" class="form-control bondsman_company_address" value="<?php echo $detail['bondsman_company_address']?>" placeholder="" disabled="disabled">
                </div>
            </div>
        </div>

    </div>
</div>
<?php } ?>
