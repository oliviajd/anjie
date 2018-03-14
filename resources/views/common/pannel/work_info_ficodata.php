<?php if ($edit['ficodata'] == true) {?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            fico数据查询（总分850分，分数越高越好，信用越好）
        </h3>
    </div>
    <?php if($detail['retCode'] == '000'){?>
    <div class="panel-body">
       <!--  <h4>个人信息</h4> -->
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">评分分数</span>
                    <input name="score" type="text" class="form-control score" value="<?php echo $detail['score'] ?>">
                </div>
            </div>
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon">得分理由</span>
                    <input name="reason" type="text" class="form-control reason" value="<?php echo $detail['reason'] ?>" disabled="disabled">
                </div>
            </div>
        </div>
        <!-- <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">审批建议</span>
                    <input type="text" name="recAction" class="form-control recAction" value="<?php echo $detail['recAction']?>">
                </div>
            </div>
        </div> -->
    </div>
    <?php } elseif($detail['retCode'] == '999'){?>
    <div class="panel-body">
       用户数据不足，无法进行评分
    </div>
    <?php } elseif($detail['retCode'] == '201'){?>
    <div class="panel-body">
       fico评分异常
    </div>
    <?php } elseif($detail['retCode'] == '901' || $detail['retCode'] == '902' || $detail['retCode'] == '909'){?>
    <div class="panel-body">
       fico评分系统忙，请重新查询
       <button type="" class="btn btn-default ficoquery pull-right" style="width: 140px;height: 40px;">
            查询
       </button>
    </div>
    <?php } else {?>
    <div class="panel-body">
       暂无评分
    </div>
    <?php }?>
</div>
<?php } else {?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            fico数据查询（总分850分，分数越高越好，信用越好）
        </h3>
    </div>
    <?php if($detail['retCode'] == '000'){?>
    <div class="panel-body">
       <!--  <h4>个人信息</h4> -->
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">评分分数</span>
                    <input name="score" type="text" class="form-control score" disabled="disabled" value="<?php echo $detail['score'] ?>">
                </div>
            </div>
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon">得分理由</span>
                    <input name="reason" type="text" class="form-control reason" disabled="disabled" value="<?php echo $detail['reason'] ?>" disabled="disabled">
                </div>
            </div>
        </div>
        <!-- <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">审批建议</span>
                    <input type="text" name="recAction" class="form-control recAction" disabled="disabled" value="<?php echo $detail['recAction']?>">
                </div>
            </div>
        </div> -->
    </div>
    <?php } elseif($detail['retCode'] == '999'){?>
    <div class="panel-body">
       用户数据不足，无法进行评分
    </div>
    <?php } elseif($detail['retCode'] == '201'){?>
    <div class="panel-body">
       fico评分异常
    </div>
    <?php } elseif($detail['retCode'] == '901' || $detail['retCode'] == '902' || $detail['retCode'] == '909'){?>
    <div class="panel-body">
       fico评分系统忙，请重新查询
       <button type="" class="btn btn-default ficoquery pull-right" style="width: 140px;height: 40px;">
            查询
       </button>
    </div>
    <?php } else {?>
    <div class="panel-body">
       暂无评分
    </div>
    <?php }?>
</div>
<?php } ?>

