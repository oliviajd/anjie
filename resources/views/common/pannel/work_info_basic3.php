<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            基本信息
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
        </div>
    </div>
</div>

