<h3>fico数据返回</h3>
<table id="example1" class="table table-bordered table-hover" style='border:1px solid #F00;width:400'>
    <thead>
        <tr>
            <th style='border:1px solid #F00'>work_id</th>
            <th style='border:1px solid #F00'>返回码（retCode）</th>
            <th style='border:1px solid #F00'>唯一id（scoreID）</th>
            <th style='border:1px solid #F00'>分数（score）</th>
            <th style='border:1px solid #F00'>理由（reason）</th>
            <th style='border:1px solid #F00'>评分建议（recAction）</th>
            <th style='border:1px solid #F00'>错误信息（errMsg）</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th style='border:1px solid #F00'>{{$arr['work_id']}}</th>
            <th style='border:1px solid #F00'>{{$arr['retCode']}}</th>
            <th style='border:1px solid #F00'>{{$arr['scoreID']}}</th>
            <th style='border:1px solid #F00'>{{$arr['score']}}</th>
            <th style='border:1px solid #F00'>{{$arr['reason']}}</th>
            <th style='border:1px solid #F00'>{{$arr['errMsg']}}</th>
        </tr>
    </tfoot>
</table>