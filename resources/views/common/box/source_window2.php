<div style="position: fixed;margin-right: 15px;" id="source_window">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">客户影像资料</h3>
            <a href="#" id="zipDownload" style="position: absolute;left: 130px;top: 9px;">（打包下载）</a>
            <a href="#" id="wordcreate" style="position: absolute;left: 250px;top: 9px;">（word生成）</a>
        </div>
        <div class="box-body">
            <div class="input-group">
                <div class="input-group-btn" id="source_type">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span>身份证</span>
                        <span class="fa fa-caret-down"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:;" data-type="1" data-st="image" data-edit="<?php echo @$source_edit[1] == 1 ? 1 : 2?>" <?php echo @$source_edit[1] == 1 ? 'style="color:#000;"' : 2?>>身份证</a></li>
                        <li><a href="javascript:;" data-type="2" data-st="image" data-edit="<?php echo @$source_edit[2] == 1 ? 1 : 2?>" <?php echo @$source_edit[2] == 1 ? 'style="color:#000;"' : 2?>>授权书</a></li>
<!--                        <li><a href="javascript:;" data-type="3" data-st="image" data-edit="<?php echo @$source_edit[2] == 1 ? 1 : 2?>" <?php echo @$source_edit[2] == 1 ? 'style="color:#000;"' : 2?>>征信报告</a></li>-->
                    </ul>
                </div>
                <!-- /btn-group -->
                <input type="text" class="form-control" placeholder="" disabled=disabled>
                <span class="input-group-addon">
                    <form id="source" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
                        <a id="source_button" class="a_add_file" style="position:relative; overflow:hidden;display: inline-block;"><i class="fa  fa-cloud-upload"></i> 添加
                            <input type="file" name="file" multiple="" accept="image/gif,image/jpg,image/jpeg,image/png" class="button_add_file" style="position:absolute; right:0; top:0; font-size:100px; opacity:0; filter:alpha(opacity=0);">
                        </a>
                    </form>
                </span>
                <span id="source_length" class="input-group-addon">共0张</span>
            </div>
            <div style="background: #ddd;">
                <div id="galley">
                    <ul class="pictures"></ul>
                </div>
            </div>
        </div>
    </div>
</div>