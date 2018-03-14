<?php if ($edit['artifical_manual']) { ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            人工审批意见
        </h3>
    </div>
    <div class="panel-body">
        <p class="lead">1.Lead to emphasize importance</p>
        <p class="text-green">2.Text green to emphasize success</p>
        <p class="text-aqua">3.Text aqua to emphasize info</p>
        <p class="text-light-blue">4.Text light blue to emphasize info (2)</p>
        <p class="text-red">5.Text red to emphasize danger</p>
        <p class="text-yellow">6.Text yellow to emphasize warning</p>
        <p class="text-muted">7.Text muted to emphasize general</p>
        <div class="input-group margin-bottom">
            <span class="input-group-addon">审核结果</span>
            <select name="artificial_status" class="form-control" >
                    <option value="2" <?php echo $detail['artificial_status'] == 2 ? 'selected' : '' ?>>拒绝</option>
                    <option value="1" <?php echo $detail['artificial_status'] == 1 ? 'selected' : '' ?>>通过</option>
                    <option value="3" <?php echo $detail['artificial_status'] == 3 ? 'selected' : '' ?>>待补件</option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label">备注：</label>
            <textarea name="artificial_description" class="comments form-control" rows="3" placeholder=""><?php echo $detail['artificial_description']?></textarea>
        </div>
    </div>
</div>
<?php } else {?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            人工审批意见
        </h3>
    </div>
    <div class="panel-body">
        <p class="lead">1.Lead to emphasize importance</p>
        <p class="text-green">2.Text green to emphasize success</p>
        <p class="text-aqua">3.Text aqua to emphasize info</p>
        <p class="text-light-blue">4.Text light blue to emphasize info (2)</p>
        <p class="text-red">5.Text red to emphasize danger</p>
        <p class="text-yellow">6.Text yellow to emphasize warning</p>
        <p class="text-muted">7.Text muted to emphasize general</p>
        <div class="input-group margin-bottom">
            <span class="input-group-addon">审核结果</span>
            <select name="artificial_status" class="form-control" disabled="disabled" >
                    <option value="2" <?php echo $detail['artificial_status'] == 2 ? 'selected' : '' ?>>拒绝</option>
                    <option value="1" <?php echo $detail['artificial_status'] == 1 ? 'selected' : '' ?>>通过</option>
                    <option value="3" <?php echo $detail['artificial_status'] == 3 ? 'selected' : '' ?>>待补件</option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label">备注：</label>
            <textarea name="artificial_description" class="comments form-control" rows="3" placeholder="" disabled="disabled"><?php echo $detail['artificial_description']?></textarea>
        </div>
    </div>
</div>
<?php } ?>
