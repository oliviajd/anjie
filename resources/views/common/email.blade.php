<h3>银行数据错误</h3>
<table id="example1" class="table table-bordered table-hover" style='border:1px solid #F00;width:400'>
    <thead>
        <tr>
            <th style='border:1px solid #F00'>work_id</th>
            <th style='border:1px solid #F00'>银行状态码</th>
            <th style='border:1px solid #F00'>银行状态提示</th>
            <th style='border:1px solid #F00'>银行接口代码</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th style='border:1px solid #F00'>{{$arr['work_id']}}</th>
            <th style='border:1px solid #F00'>{{$arr['status']}}</th>
            <th style='border:1px solid #F00'>{{$arr['iretmsg']}}</th>
            <th style='border:1px solid #F00'>{{$arr['transcode']}}</th>
        </tr>
    </tfoot>
</table>