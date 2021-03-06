<?php if ($edit['inquire'] == true) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" style="color:#F00">
                人行征信
            </h3>
        </div>
        <div class="panel-body">
            <div class="input-group margin-bottom">
                <!--<label for="identity_card_number" class=" control-label">* 人行征信结果：</label>-->
                <span class="input-group-addon">★征信结果</span>
                <select name="inquire_result" class="form-control inquire_result">
                    <option value="">请选择</option>
                    <option value="1" <?php echo $detail['inquire_result'] == '1' ? 'selected' : ''?>>通过</option>
                    <option value="2" <?php echo $detail['inquire_result'] == '2' ? 'selected' : ''?>>拒绝</option>
                </select>
            </div>
            <div class="form-group">
                <label for="inquire_description" class="control-label">★备注：</label>
                <textarea class="comments form-control inquire_description" rows="3" placeholder="" name="inquire_description"><?php echo $detail['inquire_description']?></textarea>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                人行征信
            </h3>
        </div>
        <div class="panel-body">
            <div class="input-group margin-bottom">
                <span class="input-group-addon">征信结果</span>
                <select name="inquire_result" class="form-control inquire_result" disabled="disable">
                    <option value="1" <?php echo $detail['inquire_result'] == '1' ? 'selected' : ''?>>通过</option>
                    <option value="2" <?php echo $detail['inquire_result'] == '2' ? 'selected' : ''?>>拒绝</option>
                    <option value="3" <?php echo $detail['inquire_result'] == '3' ? 'selected' : ''?>>未征信</option>
                </select>
            </div>
            <?php if($detail['inquire_description'] !== '' && $detail['inquire_description'] !== null) {?>
            <div class="form-group">
                <label for="inquire_description" class="control-label">备注：</label>
                <textarea class="comments form-control inquire_description" rows="3" placeholder="" name="inquire_description" disabled=""><?php echo $detail['inquire_description']?></textarea>
            </div>
            <?php }?>
        </div>
    </div>
    <?php
}?>