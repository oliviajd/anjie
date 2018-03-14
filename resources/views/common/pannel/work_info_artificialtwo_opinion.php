<?php if ($edit['artificialtwo_opinion'] == true) {?>   
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" style="color:#F00">
                二审意见
            </h3>
        </div>
        <div class="panel-body">
            <div class="row margin-bottom">
                <div class="col-md-12">
                    <div class="input-group">
                        <span class="input-group-addon">★审核结果</span>
                        <select name="artificialtwo_status" class="form-control artificialtwo_status">
                            <option value="1" <?php echo $detail['artificialtwo_status'] == '1' ? 'selected' : '' ?>>通过</option>
                            <option value="2" <?php echo $detail['artificialtwo_status'] == '2' ? 'selected' : '' ?>>拒绝</option>
                            <option value="3" <?php echo $detail['artificialtwo_status'] == '3' ? 'selected' : '' ?>>待补件</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="input-group margin-bottom refuse2_div <?php echo $detail['artificialtwo_status'] !== '2' ? 'hide' : ''?> ">
                <span class="input-group-addon">★拒绝原因</span>
                <span class="input-group-addon">
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="1" <?php echo in_array('1', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>客户否认申请</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="2" <?php echo in_array('2', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>非本人签名</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="3" <?php echo in_array('3', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>申请人主动取消申请</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="4" <?php echo in_array('4', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>黑名单</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="5" <?php echo in_array('5', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>人行征信有不良记录</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="6" <?php echo in_array('6', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>申请人不配合调查</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="7" <?php echo in_array('7', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>公安网信息有误</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="8" <?php echo in_array('8', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>无法联系申请人</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="9" <?php echo in_array('9', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>其他</label>
                    </div>
                </span>
                <div class="input-group"></div>
            </div>
            <div class="row margin-bottom">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="artificialtwo_description" class="control-label">备注：</label>
                        <textarea class="comments form-control" rows="3" placeholder="" name="artificialtwo_description"><?php echo $detail['artificialtwo_description']?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else {?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" style="color:#F00">
                二审意见
            </h3>
        </div>
        <div class="panel-body">
            <div class="row margin-bottom">
                <div class="col-md-12">
                    <div class="input-group">
                        <span class="input-group-addon">审核结果</span>
                        <select name="artificialtwo_status" class="form-control artificialtwo_status" disabled="disabled">
                            <option value="1" <?php echo $detail['artificialtwo_status'] == '1' ? 'selected' : '' ?>>通过</option>
                            <option value="2" <?php echo $detail['artificialtwo_status'] == '2' ? 'selected' : '' ?>>拒绝</option>
                            <option value="3" <?php echo $detail['artificialtwo_status'] == '3' ? 'selected' : '' ?>>待补件</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="input-group margin-bottom refuse_div <?php echo $detail['artificialtwo_status'] !== '2' ? 'hide' : ''?> ">
                <span class="input-group-addon">拒绝原因</span>
                <span class="input-group-addon">
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="1" <?php echo in_array('1', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>客户否认申请</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="2" <?php echo in_array('2', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>非本人签名</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="3" <?php echo in_array('3', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>申请人主动取消申请</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="4" <?php echo in_array('4', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>黑名单</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="5" <?php echo in_array('5', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>人行征信有不良记录</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="6" <?php echo in_array('6', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>申请人不配合调查</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="7" <?php echo in_array('7', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>公安网信息有误</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="8" <?php echo in_array('8', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>无法联系申请人</label>
                    </div>
                    <div class="checkbox">
                        <label><input name="artificialtwo_refuse_reason" type="checkbox" value="9" <?php echo in_array('9', explode(',', $detail['artificialtwo_refuse_reason'])) ? 'checked=checked' : '' ?>>其他</label>
                    </div>
                </span>
                <div class="input-group"></div>
            </div>
            <div class="row margin-bottom">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="artificialtwo_description" class="control-label">备注：</label>
                        <textarea class="comments form-control" rows="3" placeholder="" name="artificialtwo_description" disabled="disabled"><?php echo $detail['artificialtwo_description']?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
