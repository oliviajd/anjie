<?php if ($edit['contact'] == true) {?>
<div class="panel panel-default contact_div <?php echo ($detail['customer_marital_status'] == '1' || $detail['customer_marital_status'] == '') ? 'hide' : ''?>">
    <div class="panel-heading">
        <h3 class="panel-title">
            联系人信息
        </h3>
    </div>
    <div class="panel-body">
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">联系人姓名</span>
                    <input name="contacts_man_name" type="text" class="form-control contacts_man_name" placeholder="请输入姓名" value="<?php echo $detail['contacts_man_name']?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">关系</span>
                    <select name="contacts_man_relationship" class="form-control contacts_man_relationship">
                        <option value="">请选择</option>
                        <option value="1" <?php echo $detail['contacts_man_relationship'] == '1' ? 'selected' : '' ?>>父母</option>
                        <option value="2" <?php echo $detail['contacts_man_relationship'] == '2' ? 'selected' : '' ?>>配偶</option>
                        <option value="3" <?php echo $detail['contacts_man_relationship'] == '3' ? 'selected' : '' ?>>亲戚</option>
                        <option value="4" <?php echo $detail['contacts_man_relationship'] == '4' ? 'selected' : '' ?>>其他</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">身份证号</span>
                    <input name="contacts_man_certificate_number"  maxlength="18" onKeyUp="value=value.replace(/[^\d|xX]/g,'')" type="text" class="form-control contacts_man_certificate_number" placeholder="请输入身份证号" value="<?php echo $detail['contacts_man_certificate_number']?>">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">手机号码</span>
                    <input name="contacts_man_telephone" type="text" maxlength="11" onKeyUp="value=value.replace(/[^\d]/g,'')" class="form-control contacts_man_telephone" placeholder="请输入手机号" value="<?php echo $detail['contacts_man_telephone']?>">
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else {?>
<div class="panel panel-default contact_div <?php echo ($detail['customer_marital_status'] == '1' || $detail['customer_marital_status'] == '')  ? 'hide' : ''?>">
    <div class="panel-heading">
        <h3 class="panel-title">
            联系人信息
        </h3>
    </div>
    <div class="panel-body">
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">联系人姓名</span>
                    <input name="contacts_man_name" type="text" class="form-control contacts_man_name" value="<?php echo $detail['contacts_man_name']?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">关系</span>
                    <select name="contacts_man_relationship" class="form-control contacts_man_relationship" disabled="disabled">
                        <option value="">请选择</option>
                        <option value="1" <?php echo $detail['contacts_man_relationship'] == '1' ? 'selected' : '' ?>>父母</option>
                        <option value="2" <?php echo $detail['contacts_man_relationship'] == '2' ? 'selected' : '' ?>>配偶</option>
                        <option value="3" <?php echo $detail['contacts_man_relationship'] == '3' ? 'selected' : '' ?>>亲戚</option>
                        <option value="4" <?php echo $detail['contacts_man_relationship'] == '4' ? 'selected' : '' ?>>其他</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">身份证号</span>
                    <input name="contacts_man_certificate_number" type="text" maxlength="18" onKeyUp="value=value.replace(/[^\d|xX]/g,'')" class="form-control contacts_man_certificate_number" value="<?php echo $detail['contacts_man_certificate_number']?>" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">手机号码</span>
                    <input name="contacts_man_telephone" type="text" maxlength="11" onKeyUp="value=value.replace(/[^\d]/g,'')" class="form-control contacts_man_telephone" value="<?php echo $detail['contacts_man_telephone']?>" disabled="disabled">
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php } ?>
