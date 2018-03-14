<aside class="main-sidebar">
    <title><?php if(isset($title)) {echo $title;}?></title>
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <?php if(isset($head_portrait)) {?>
                <img src="{{ asset($head_portrait)}}" class="img-circle" alt="User Image">
                <?php } else {?>
                <img src="{{ asset('/bower_components/AdminLTE/dist/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image">
                <?php }?>
            </div>
            <div class="pull-left info">
                <p> <?php echo (isset($username) ? $username : '');?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>
        <ul class="sidebar-menu">
            <?php if(isset($category)) {?>
            <?php foreach($category as $key=>$value) {?>
            <?php if (isset($methodinfo['cid']) ? $value['id'] == $methodinfo['cid'] : false) {?>
                <li class="treeview active">
                    <?php } else {?>
                    <li class="treeview">
                        <?php }?>
                        <a href="#"><i class="<?php if(isset($value['icon'])) {echo $value['icon']; }?>"></i> <span><?php if(isset($value['menu_title'])) { echo $value['menu_title'] ;}?></span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                          </span>
                      </a>
                      <ul class="treeview-menu ">
                        <?php foreach($method as $k=>$v) {?>
                        <?php if($v['cid'] == $value['id'] && $v['method_id'] == $methodinfo['method_id']) {?>
                        <li class="active"><a href=<?php if(isset($v['url'])) {echo $v['url']; }?>><?php if(isset($v['method_name_cn'])) {echo $v['method_name_cn'];}?></a></li>
                        <?php } elseif($v['cid'] == $value['id'] && $v['method_id'] !== $methodinfo['method_id']){?>
                        <li><a href=<?php if(isset($v['url'])) {echo $v['url']; }?>><?php if(isset($v['method_name_cn'])) {echo $v['method_name_cn'];}?></a></li>
                        <?php }?>
                        <?php }?>
                    </ul>
                </li>
                <?php }?>
                <?php }?>
            </ul>
            <!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>