<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            来源信息
        </h3>
    </div>
    <div class="panel-body">
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">产品名称</span>
                    <input type="text" class="form-control" value="<?php echo $detail['product_name']?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">业务地区</span>
                    <input type="text" class="form-control" value="<?php echo $detail['salesman_city']?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">申请件来源</span>
                    <input type="text" class="form-control" value="<?php echo $detail['merchant_name']?>" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">业务人员</span>
                    <input type="text" class="form-control" value="<?php echo $detail['salesman_name']?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">申请时间</span>
                    <input type="text" class="form-control" value="<?php echo date('Y-m-d H:i:s', $detail['create_time'])?>" disabled="disabled">
                </div>
            </div>
        </div>
    </div>
</div>