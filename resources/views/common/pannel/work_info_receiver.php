<?php if ($edit['receiver'] == true) {?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            受理人信息
        </h3>
    </div>
    <div class="panel-body">
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">姓名</span>
                    <input name="receiver_name" type="text" class="form-control" value="<?php echo $detail['receiver_name']?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">手机</span>
                    <input name="receiver_telephone" type="text" class="form-control" value="<?php echo $detail['receiver_telephone']?>">
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else {?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            受理人信息
        </h3>
    </div>
    <div class="panel-body">
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">姓名</span>
                    <input name="receiver_name" type="text" class="form-control" value="<?php echo $detail['receiver_name']?>" disabled="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">手机</span>
                    <input name="receiver_telephone" type="text" class="form-control" value="<?php echo $detail['receiver_telephone']?>" disabled="">
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
