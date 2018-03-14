<?php if ($edit['basic2'] == true) {?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            基本信息
        </h3>
    </div>
    <div class="panel-body">
       <!--  <h4>个人信息</h4> -->
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">客户姓名</span>
                    <input name="customer_name" type="text" class="form-control customer_name" value="<?php echo $detail['customer_name'] ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">性别</span>
                    <input name="customer_sex" type="text" class="form-control customer_sex" value="<?php echo $detail['customer_sex'] ?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">年龄</span>
                    <input type="text" class="form-control customer_age" value="<?php echo $detail['customer_age']?>" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">身份证号</span>
                    <input type="text" name="customer_certificate_number" class="form-control customer_certificate_number" onKeyUp="value=value.replace(/[^\d|xX]/g,'')" value="<?php echo $detail['customer_certificate_number']?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">手机号</span>
                    <input type="text" name="customer_telephone" class="form-control customer_telephone" value="<?php echo $detail['customer_telephone']?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">★户口所在地</span>
                    <input type="text" name="hukou" class="form-control hukou" value="<?php echo $detail['hukou']?>">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon">居住地址</span>
                    <input type="text" name="customer_address" class="form-control customer_address" value="<?php echo $detail['customer_address']?>">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group margin-bottom">
                    <span class="input-group-addon">★婚姻状况</span>
                    <select name="customer_marital_status" class="form-control customer_marital_status">
                        <option value="">请选择</option>
                        <option value="1" <?php echo $detail['customer_marital_status'] == 1 ? 'selected' : '' ?>>已婚</option>
                        <option value="2" <?php echo $detail['customer_marital_status'] == 2 ? 'selected' : '' ?>>未婚</option>
                        <option value="3" <?php echo $detail['customer_marital_status'] == 3 ? 'selected' : '' ?>>离婚</option>
                        <option value="4" <?php echo $detail['customer_marital_status'] == 4 ? 'selected' : '' ?>>丧偶</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group margin-bottom">
                    <span class="input-group-addon">★担保人</span>
                    <select name="customer_has_bondsman" class="form-control customer_has_bondsman">
                        <option value="">请选择</option>
                        <option value="1" <?php echo $detail['customer_has_bondsman'] == 1 ? 'selected' : '' ?>>有担保人</option>
                        <option value="2" <?php echo $detail['customer_has_bondsman'] == 2 ? 'selected' : '' ?>>无担保人</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">★工作单位</span>
                    <input name="customer_company_name" type="text" class="form-control customer_company_name" value="<?php echo $detail['customer_company_name']?>" placeholder=" 请输入工作单位">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">单位电话</span>
                    <input name="customer_company_phone_number" type="text" class="form-control customer_company_phone_number" value="<?php echo $detail['customer_company_phone_number']?>" placeholder=" 请输入单位电话">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon">单位地址</span>
                    <input name="company_address" type="text" class="form-control company_address" value="<?php echo $detail['company_address']?>" placeholder="请输入单位详细地址">
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else {?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            基本资料
        </h3>
    </div>
    <div class="panel-body">
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">客户姓名</span>
                    <input name="customer_name" type="text" class="form-control customer_name" value="<?php echo $detail['customer_name'] ?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">性别</span>
                    <input name="customer_sex" type="text" class="form-control customer_sex" value="<?php echo $detail['customer_sex'] ?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">年龄</span>
                    <input type="text" class="form-control customer_age" value="<?php echo $detail['customer_age']?>" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">身份证号</span>
                    <input type="text" class="form-control customer_certificate_number" onKeyUp="value=value.replace(/[^\d|xX]/g,'')" value="<?php echo $detail['customer_certificate_number']?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">手机号</span>
                    <input type="text" class="form-control customer_telephone" value="<?php echo $detail['customer_telephone']?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">户口所在地</span>
                    <input type="text" name="hukou" class="form-control hukou" value="<?php echo $detail['hukou']?>" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon">居住地址</span>
                    <input type="text" class="form-control customer_address" value="<?php echo $detail['customer_address']?>" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group margin-bottom">
                    <span class="input-group-addon">婚姻状况</span>
                    <select name="customer_marital_status" class="form-control customer_marital_status" disabled="disabled">
                        <option value="1" <?php echo $detail['customer_marital_status'] == 1 ? 'selected' : '' ?>>已婚</option>
                        <option value="2" <?php echo $detail['customer_marital_status'] == 2 ? 'selected' : '' ?>>未婚</option>
                        <option value="3" <?php echo $detail['customer_marital_status'] == 3 ? 'selected' : '' ?>>离婚</option>
                        <option value="4" <?php echo $detail['customer_marital_status'] == 4 ? 'selected' : '' ?>>丧偶</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group margin-bottom">
                    <span class="input-group-addon">担保人</span>
                    <select name="customer_has_bondsman" class="form-control customer_has_bondsman" disabled="disabled">
                        <option value="1" <?php echo $detail['customer_has_bondsman'] == 1 ? 'selected' : '' ?>>有</option>
                        <option value="2" <?php echo $detail['customer_has_bondsman'] == 2 ? 'selected' : '' ?>>无</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">工作单位</span>
                    <input name="customer_company_name" type="text" class="form-control customer_company_name" value="<?php echo $detail['customer_company_name']?>" disabled="disabled" placeholder="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">单位电话</span>
                    <input name="customer_company_phone_number" type="text" class="form-control customer_company_phone_number" disabled="disabled" value="<?php echo $detail['customer_company_phone_number']?>" placeholder="">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon">单位地址</span>
                    <input name="company_address" type="text" class="form-control company_address" disabled="disabled" value="<?php echo $detail['company_address']?>" placeholder="">
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

