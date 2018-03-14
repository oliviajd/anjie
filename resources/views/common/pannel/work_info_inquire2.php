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
            <!--征信报告图片-->
            <div class="input-group">
                <div class="input-group-btn" id="source_type2">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span>征信报告</span>
                    	
                    </button>
                </div>
                <!-- /btn-group -->
                <input type="text" class="form-control" placeholder="" disabled=disabled>
                <span class="input-group-addon">
                    <form id="source2" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
                        <a id="source_button2" class="a_add_file" style="position:relative; overflow:hidden;display: inline-block;"><i class="fa  fa-cloud-upload"></i> 添加
                            <input type="file" name="file" multiple="" accept="image/gif,image/jpg,image/jpeg,image/png" class="button_add_file" style="position:absolute; right:0; top:0; font-size:100px; opacity:0; filter:alpha(opacity=0);">
                        </a>
                    </form>
                </span>
                <span id="source_length2" class="input-group-addon">共0张</span>
                
            </div>
            <div class="row" id="creditImgList" style="margin-top: 10px;">
				<!--<div class="col-xs-6 col-md-3">
    				<div class="thumbnail">
    					<div style="height: 100px;overflow: hidden;">
    						<img src="http://feature-cfl-img.anjietest-feature.ifcar99.com/uploads/image/20171129/fbe0eaedaecfd8647c656d3340abb40f.png" alt="http://feature-cfl-img.anjietest-feature.ifcar99.com/uploads/image/20171129/fbe0eaedaecfd8647c656d3340abb40f.png" style="width: 100%; height: 100%;" onclick="imgModal(this.src)")">
    					</div>
    					<div class="caption">
    						<p><div class="btn btn-primary delThisBtn" data-src='creditSource[s].src' data-id='creditSource[s].id' role="button">删除</div></p>
    					</div>
    				</div>
				</div>-->
				
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
            <!--征信报告图片-->
            <div class="input-group">
                <div class="input-group-btn" id="source_type2">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span>征信报告</span>
                    	
                    </button>
                </div>
                <!-- /btn-group -->
                <input type="text" class="form-control" placeholder="" disabled=disabled>
                <span class="input-group-addon">
                    <form id="source2" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
                        <a id="source_button2" class="a_add_file" style="position:relative; overflow:hidden;display: inline-block;"><i class="fa  fa-cloud-upload"></i> 添加
                            <input type="file" name="file" multiple="" accept="image/gif,image/jpg,image/jpeg,image/png" class="button_add_file" style="position:absolute; right:0; top:0; font-size:100px; opacity:0; filter:alpha(opacity=0);">
                        </a>
                    </form>
                </span>
                <span id="source_length2" class="input-group-addon">共0张</span>
                
            </div>
            <div class="row" id="creditImgList" style="margin-top: 10px;">
				
			</div>
            <?php }?>
        </div>
    </div>
    <?php
}?>