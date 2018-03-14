<script src="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/js/vendor/jquery.ui.widget_0b44270.js"></script>
<script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<script src="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/js/jquery.fileupload_1a6f827.js"></script>
<!-- The File Upload processing plugin -->
<script src="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/js/jquery.fileupload-process_840f652.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/js/jquery.fileupload-image_7c40367.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/js/jquery.fileupload-audio_a7234df.js"></script>
<!-- The File Upload video preview plugin -->
<script src="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/js/jquery.fileupload-video_0a9ee29.js"></script>
<!-- The File Upload validation plugin -->
<script src="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/js/jquery.fileupload-validate_a144e61.js"></script>
<!-- The File Upload user interface plugin -->
<script src="/bower_components/AdminLTE/plugins/jQuery-File-Upload-master/js/jquery.fileupload-ui_9ff69cd.js"></script>
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>取消</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    {% if (file.type.indexOf('image') >= 0) { %}
                        <a href="javascript:;" title="{%=file.name%}" ><img data-action="zoom" width="160" src="{%=file.thumbnailUrl%}"></a>
                    {% } else { %}
                        <a href="{%=file.download_url?file.download_url:file.url%}" title="{%=file.name%}" download="{%=file.name%}" target=_blank><img width="160" src="{%=file.thumbnailUrl%}"></a>
                    {% } %}
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}">{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=file.size?o.formatFileSize(file.size):''%}</span>
        </td>
        <td>
            {% if (file.delete === false) { %}

            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>删除</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>