<div class="modal fade" id="source_window" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">  
    <div class="modal-dialog" role="document">  
        <div class="modal-content">  
            <div class="modal-header">  
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">  
                    <span aria-hidden="true">×</span>  
                </button>  
                <h4 class="modal-title" id="myModalLabel">客户影像资料</h4>  
                <a href="#" id="zipDownload" style="position: absolute;left: 130px;top: 18px;">（打包下载）</a>
            </div>  
            <div class="modal-body" style="height: 720px;"> 
            	<div class="box-body">
            		<div class="col-md-3" style="padding-left: 0;">
            			
            			<div class="list-group" id="source_type">
            				<div class="panel-heading">
	                            <h3 class="panel-title" style="color:#F00">
	                                照片
	                            </h3>
	                        </div>
		                    <a class="list-group-item active" href="#" data-type="1" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>><span class="badge">0</span><span href="javascript:;" data-type="1" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>>身份证</span></a>
		                    <a class="list-group-item" href="#" data-type="2" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[2] == 1 ? 'style="color:#000;"' : 2?>><span class="badge">0</span><span href="javascript:;" data-type="2" data-st="image" data-edit="<?php echo @$source_edit[2] == 1 ? 1 : 2?>" <?php echo @$source_edit[2] == 1 ? 'style="color:#000;"' : 2?>>授权书</span></a>
		                    <a class="list-group-item" href="#" data-type="3" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[3] == 1 ? 'style="color:#000;"' : 2?>><span class="badge">0</span><span href="javascript:;" data-type="3" data-st="image" data-edit="<?php echo @$source_edit[3] == 1 ? 1 : 2?>" <?php echo @$source_edit[3] == 1 ? 'style="color:#000;"' : 2?>>征信报告</span></a>
		                    <a class="list-group-item" href="#" data-type="4" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[4] == 1 ? 'style="color:#000;"' : 2?>><span class="badge">0</span><span href="javascript:;" data-type="4" data-st="image" data-edit="<?php echo @$source_edit[4] == 1 ? 1 : 2?>" <?php echo @$source_edit[4] == 1 ? 'style="color:#000;"' : 2?>>户口本</span></a>
		                    <a class="list-group-item" href="#" data-type="5" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[5] == 1 ? 'style="color:#000;"' : 2?>><span class="badge">0</span><span href="javascript:;" data-type="5" data-st="image" data-edit="<?php echo @$source_edit[5] == 1 ? 1 : 2?>" <?php echo @$source_edit[5] == 1 ? 'style="color:#000;"' : 2?>>房产证明</span></a>
		                    <a class="list-group-item" href="#" data-type="6" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[6] == 1 ? 'style="color:#000;"' : 2?>><span class="badge">0</span><span href="javascript:;" data-type="6" data-st="image" data-edit="<?php echo @$source_edit[6] == 1 ? 1 : 2?>" <?php echo @$source_edit[6] == 1 ? 'style="color:#000;"' : 2?>>银行流水</span></a>
		                    <a class="list-group-item" href="#" data-type="12" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>><span class="badge">0</span><span href="javascript:;" data-type="12" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>>驾驶证</span></a>
		                    <a class="list-group-item" href="#" data-type="13" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>><span class="badge">0</span><span href="javascript:;" data-type="13" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>>公司合同</span></a>
		                    <a class="list-group-item" href="#" data-type="14" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>><span class="badge">0</span><span href="javascript:;" data-type="14" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>>收入证明</span></a>
		                    <a class="list-group-item" href="#" data-type="15" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>><span class="badge">0</span><span href="javascript:;" data-type="15" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>>其他</span></a>
		                    <div class="panel-heading">
	                            <h3 class="panel-title" style="color:#F00">
	                                视频
	                            </h3>
	                        </div>
		                    <a class="list-group-item" href="#" data-type="7" data-st="video" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>"><span class="badge">0</span><span href="javascript:;" data-type="7" data-st="video" data-edit="<?php echo @$source_edit[7] == 1 ? 1 : 2?>" <?php echo @$source_edit[7] == 1 ? 'style="color:#000;"' : 2?>>面签视频</span></a>
		                    <a class="list-group-item" href="#" data-type="8" data-st="video" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>"><span class="badge">0</span><span href="javascript:;" data-type="8" data-st="video" data-edit="<?php echo @$source_edit[8] == 1 ? 1 : 2?>" <?php echo @$source_edit[8] == 1 ? 'style="color:#000;"' : 2?>>家访视频</span></a>
		                </div>
            		</div>
	               	
                    <div class="input-group col-md-9">
                    	
                        
                        <!-- /btn-group -->
                        <input type="text" class="form-control" placeholder="" disabled=disabled>
                        <span class="input-group-addon">
                            <form id="source" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
                                <a id="source_button" class="a_add_file" style="position:relative; overflow:hidden;display: inline-block;"><i class="fa  fa-cloud-upload"></i> 添加
                                    <input type="file" name="file" multiple="" accept="image/gif,image/jpg,image/jpeg,image/png,video/*" class="button_add_file" style="position:absolute; right:0; top:0; font-size:100px; opacity:0; filter:alpha(opacity=0);">
                                </a>
                            </form>
                        </span>
                        <!--<span id="source_length" class="input-group-addon">共0张</span>-->
                    </div>
                    <div style="background: #ddd;width: 637px; height: 616px;float: right;" class="galleryDiv">
                        <div id="galley">
                            <ul class="pictures"></ul>
                        </div>
                    </div>
                </div>
            </div>   
        </div>  
    </div>  
</div>
