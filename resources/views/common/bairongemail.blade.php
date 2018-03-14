<h3>百融失信数据返回</h3>
<table id="example1" class="table table-bordered table-hover" style='border:1px solid #F00;width:400'>
    <thead>
        <tr>
            <th style='border:1px solid #F00'>身份证</th>
            <th style='border:1px solid #F00'>手机号</th>
            <th style='border:1px solid #F00'>姓名</th>
            <th style='border:1px solid #F00'>当前时间戳</th>
            <th style='border:1px solid #F00'>salt</th>
            <th style='border:1px solid #F00'>orgSign</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th style='border:1px solid #F00'>{{$arr['id_card']}}</th>
            <th style='border:1px solid #F00'>{{$arr['tel']}}</th>
            <th style='border:1px solid #F00'>{{$arr['name']}}</th>
            <th style='border:1px solid #F00'>{{$arr['currenttime']}}</th>
            <th style='border:1px solid #F00'>{{$arr['salt']}}</th>
            <th style='border:1px solid #F00'>{{$arr['orgSign']}}</th>
        </tr>
    </tfoot>
</table>