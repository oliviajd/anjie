@extends('layouts.admin_template')
@section('content')
<div class="row" style="width: 1800px;">
    <div id="mid" class="col-xs-6">
    <div data-ride="carousel" class="carousel slide" id="carousel-container">
    <div class="carousel-inner">
        <div class="item"><img alt="First slide" src="/images/test3.jpg"></img></div>
        <div class="item active"><img alt="Second slide" src="/images/test3.jpg"></img></div>
        <div class="item"><img alt="Third slide" src="/images/test3.jpg"></img></div>
        </div>
        <ol class="carousel-indicators">
        <li data-slide-to="0" data-target="#carousel-container"></li>
        <li data-slide-to="1" data-target="#carousel-container"></li>
        <li data-slide-to="2" data-target="#carousel-container" class="active"></li>
        </ol>
        <a data-slide="prev" href="#carousel-container" class="left carousel-control">
        <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a data-slide="next" href="#carousel-container" class="right carousel-control">
        <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
    </div>
</div>
@endsection