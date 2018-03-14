<?php if ($edit['job'] == true) {?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            职业信息
        </h3>
    </div>
    <div class="panel-body">
        <div class="row margin-bottom">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-addon">雇佣类型</span>
                    <select name="hire_type" class="form-control">
                        <option value="1" <?php echo $detail['hire_type'] == '1' ? 'selected' : '' ?>>受薪人士</option>
                        <option value="2" <?php echo $detail['hire_type'] == '2' ? 'selected' : '' ?>>自雇人士</option>
                        <option value="3" <?php echo $detail['hire_type'] == '3' ? 'selected' : '' ?>>应届生</option>
                        <option value="4" <?php echo $detail['hire_type'] == '4' ? 'selected' : '' ?>>企业主</option>
                    </select>
                </div>
            </div>
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-addon">单位名称</span>
                    <input name="customer_company_name" type="text" class="form-control" value="<?php echo $detail['customer_company_name']?>">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">部门</span>
                    <input name="customer_department" type="text" class="form-control" value="<?php echo $detail['customer_department']?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">职位</span>
                    <input name="customer_position" type="text" class="form-control" value="<?php echo $detail['customer_position']?>">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">单位邮编</span>
                    <input name="customer_company_postcode" type="text" class="form-control" value="<?php echo $detail['customer_company_postcode']?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">单位电话</span>
                    <input name="customer_company_phone_number" type="text" class="form-control" value="<?php echo $detail['customer_company_phone_number']?>">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">当月税后收入</span>
                    <input name="customer_monthly_income" type="text" class="form-control" value="<?php echo $detail['customer_monthly_income']?>">
                    <span class="input-group-addon">元</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">现单位在职时间</span>
                    <input name="customer_hiredate_month" type="text" class="form-control" value="<?php echo $detail['customer_hiredate_month']?>">
                    <span class="input-group-addon">月</span>
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-addon">行业性质</span>
                    <select name="customer_industry_nature" class="form-control">
                        <option value="0">请选择</option>
                        <option value="1" <?php echo $detail['customer_industry_nature'] == '1' ? 'selected' : '' ?>>制造业</option>
                        <option value="2" <?php echo $detail['customer_industry_nature'] == '2' ? 'selected' : '' ?>>批发/零售/贸易</option>
                        <option value="3" <?php echo $detail['customer_industry_nature'] == '3' ? 'selected' : '' ?>>金融业</option>
                        <option value="4" <?php echo $detail['customer_industry_nature'] == '4' ? 'selected' : '' ?>>能源</option>
                        <option value="5" <?php echo $detail['customer_industry_nature'] == '5' ? 'selected' : '' ?>>网路/信息服务/电子商务</option>
                        <option value="6" <?php echo $detail['customer_industry_nature'] == '6' ? 'selected' : '' ?>>酒店/旅游/餐饮</option>
                        <option value="7" <?php echo $detail['customer_industry_nature'] == '7' ? 'selected' : '' ?>>水利/环境/公共设施管理业</option>
                        <option value="8" <?php echo $detail['customer_industry_nature'] == '8' ? 'selected' : '' ?>>房地产/建筑业</option>
                        <option value="9" <?php echo $detail['customer_industry_nature'] == '9' ? 'selected' : '' ?>>政府机构</option>
                        <option value="10" <?php echo $detail['customer_industry_nature'] == '10' ? 'selected' : '' ?>>交通运输/仓储/物流</option>
                        <option value="11" <?php echo $detail['customer_industry_nature'] == '11' ? 'selected' : '' ?>>法律</option>
                        <option value="12" <?php echo $detail['customer_industry_nature'] == '12' ? 'selected' : '' ?>>商业咨询/顾问服务</option>
                        <option value="13" <?php echo $detail['customer_industry_nature'] == '13' ? 'selected' : '' ?>>卫生/社会保障/福利业</option>
                        <option value="14" <?php echo $detail['customer_industry_nature'] == '14' ? 'selected' : '' ?>>文化/体育/娱乐业</option>
                        <option value="15" <?php echo $detail['customer_industry_nature'] == '15' ? 'selected' : '' ?>>媒体/公关/出版业</option>
                        <option value="16" <?php echo $detail['customer_industry_nature'] == '16' ? 'selected' : '' ?>>教育/培训/科研</option>
                        <option value="17" <?php echo $detail['customer_industry_nature'] == '17' ? 'selected' : '' ?>>其他</option>
                    </select>
                </div>
            </div>
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-addon">经济类型</span>
                    <select name="customer_economic_style" class="form-control">
                        <option value="0">请选择</option>
                        <option value="1" <?php echo $detail['customer_economic_style'] == '1' ? 'selected' : '' ?>>政府机关/事业单位</option>
                        <option value="2" <?php echo $detail['customer_economic_style'] == '2' ? 'selected' : '' ?>>国有事业</option>
                        <option value="3" <?php echo $detail['customer_economic_style'] == '3' ? 'selected' : '' ?>>个人经营/自由职业</option>
                        <option value="4" <?php echo $detail['customer_economic_style'] == '4' ? 'selected' : '' ?>>民营企业</option>
                        <option value="5" <?php echo $detail['customer_economic_style'] == '5' ? 'selected' : '' ?>>中外合资/中外合作/外商独资</option>
                        <option value="6" <?php echo $detail['customer_economic_style'] == '6' ? 'selected' : '' ?>>其他</option>
                    </select>
                </div>
            </div>
        </div>
        <h4>现单位地址</h4>
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">省</span>
                    <select name="company_province" id="c_sheng" class="form-control">
                        <option value="1">浙江省</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">市</span>
                    <select name="company_city" id="c_shi" class="form-control">
                        <option value="1">杭州市</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">区</span>
                    <select name="company_town" id="c_qu"  class="form-control">
                        <option value="1">西湖区</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="input-group margin-bottom">
            <span class="input-group-addon">详细地址</span>
            <input name="company_address_add" type="text" class="form-control" value="<?php echo $detail['company_address_add']?>">
        </div>
        <h4>收入说明</h4>
        <div class="input-group margin-bottom">
            <span class="input-group-addon">收入来源</span>
            <select name="customer_main_income_source" class="form-control" >
                <option value="0">请选择</option>
                <option value="1" <?php echo $detail['customer_main_income_source'] == '1' ? 'selected' : '' ?>>工薪（工商银行代发工资）</option>
                <option value="2" <?php echo $detail['customer_main_income_source'] == '2' ? 'selected' : '' ?>>工薪（他行代发工资）</option>
                <option value="3" <?php echo $detail['customer_main_income_source'] == '3' ? 'selected' : '' ?>>现金</option>
                <option value="4" <?php echo $detail['customer_main_income_source'] == '4' ? 'selected' : '' ?>>租金收入</option>
                <option value="5" <?php echo $detail['customer_main_income_source'] == '5' ? 'selected' : '' ?>>经营收入</option>
                <option value="6" <?php echo $detail['customer_main_income_source'] == '6' ? 'selected' : '' ?>>其他</option>
            </select>
        </div>
        <div class="input-group margin-bottom">
            <span class="input-group-addon">收入说明</span>
            <input name="customer_income_description" type="text" class="form-control" value="<?php echo $detail['customer_income_description']?>">
        </div>
        <h4>卡片寄送</h4>
        <div class="input-group margin-bottom">
            <span class="input-group-addon">送卡地址</span>
            <select name="customer_card_address" class="form-control" >
                <option value="1" <?php echo $detail['customer_card_address'] == '1' ? 'selected' : '' ?>>住宅地址</option>
                <option value="2" <?php echo $detail['customer_card_address'] == '2' ? 'selected' : '' ?>>单位地址</option>
            </select>
        </div>
    </div>
</div>
<?php } else {?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            职业信息
        </h3>
    </div>
    <div class="panel-body">
        <div class="row margin-bottom">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-addon">雇佣类型</span>
                    <select name="hire_type" class="form-control" disabled="disabled">
                        <option value="1" <?php echo $detail['hire_type'] == '1' ? 'selected' : '' ?>>受薪人士</option>
                        <option value="2" <?php echo $detail['hire_type'] == '2' ? 'selected' : '' ?>>自雇人士</option>
                        <option value="3" <?php echo $detail['hire_type'] == '3' ? 'selected' : '' ?>>应届生</option>
                        <option value="4" <?php echo $detail['hire_type'] == '4' ? 'selected' : '' ?>>企业主</option>
                    </select>
                </div>
            </div>
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-addon">单位名称</span>
                    <input name="customer_company_name" type="text" class="form-control" value="<?php echo $detail['customer_company_name']?>" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">部门</span>
                    <input name="customer_department" type="text" class="form-control" value="<?php echo $detail['customer_department']?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">职位</span>
                    <input name="customer_position" type="text" class="form-control" value="<?php echo $detail['customer_position']?>" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">单位邮编</span>
                    <input name="customer_company_postcode" type="text" class="form-control" value="<?php echo $detail['customer_company_postcode']?>" disabled="disabled">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">单位电话</span>
                    <input name="customer_company_phone_number" type="text" class="form-control" value="<?php echo $detail['customer_company_phone_number']?>" disabled="disabled">
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">当月税后收入</span>
                    <input name="customer_monthly_income" type="text" class="form-control" value="<?php echo $detail['customer_monthly_income']?>" disabled="disabled">
                    <span class="input-group-addon">元</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon">现单位在职时间</span>
                    <input name="customer_hiredate_month" type="text" class="form-control" value="<?php echo $detail['customer_hiredate_month']?>" disabled="disabled">
                    <span class="input-group-addon">月</span>
                </div>
            </div>
        </div>
        <div class="row margin-bottom">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-addon">行业性质</span>
                    <select name="customer_industry_nature" class="form-control" disabled="disabled">
                        <option value="0">请选择</option>
                        <option value="1" <?php echo $detail['customer_industry_nature'] == '1' ? 'selected' : '' ?>>制造业</option>
                        <option value="2" <?php echo $detail['customer_industry_nature'] == '2' ? 'selected' : '' ?>>批发/零售/贸易</option>
                        <option value="3" <?php echo $detail['customer_industry_nature'] == '3' ? 'selected' : '' ?>>金融业</option>
                        <option value="4" <?php echo $detail['customer_industry_nature'] == '4' ? 'selected' : '' ?>>能源</option>
                        <option value="5" <?php echo $detail['customer_industry_nature'] == '5' ? 'selected' : '' ?>>网路/信息服务/电子商务</option>
                        <option value="6" <?php echo $detail['customer_industry_nature'] == '6' ? 'selected' : '' ?>>酒店/旅游/餐饮</option>
                        <option value="7" <?php echo $detail['customer_industry_nature'] == '7' ? 'selected' : '' ?>>水利/环境/公共设施管理业</option>
                        <option value="8" <?php echo $detail['customer_industry_nature'] == '8' ? 'selected' : '' ?>>房地产/建筑业</option>
                        <option value="9" <?php echo $detail['customer_industry_nature'] == '9' ? 'selected' : '' ?>>政府机构</option>
                        <option value="10" <?php echo $detail['customer_industry_nature'] == '10' ? 'selected' : '' ?>>交通运输/仓储/物流</option>
                        <option value="11" <?php echo $detail['customer_industry_nature'] == '11' ? 'selected' : '' ?>>法律</option>
                        <option value="12" <?php echo $detail['customer_industry_nature'] == '12' ? 'selected' : '' ?>>商业咨询/顾问服务</option>
                        <option value="13" <?php echo $detail['customer_industry_nature'] == '13' ? 'selected' : '' ?>>卫生/社会保障/福利业</option>
                        <option value="14" <?php echo $detail['customer_industry_nature'] == '14' ? 'selected' : '' ?>>文化/体育/娱乐业</option>
                        <option value="15" <?php echo $detail['customer_industry_nature'] == '15' ? 'selected' : '' ?>>媒体/公关/出版业</option>
                        <option value="16" <?php echo $detail['customer_industry_nature'] == '16' ? 'selected' : '' ?>>教育/培训/科研</option>
                        <option value="17" <?php echo $detail['customer_industry_nature'] == '17' ? 'selected' : '' ?>>其他</option>
                    </select>
                </div>
            </div>
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-addon">经济类型</span>
                    <select name="customer_economic_style" class="form-control" disabled="disabled">
                        <option value="0">请选择</option>
                        <option value="1" <?php echo $detail['customer_economic_style'] == '1' ? 'selected' : '' ?>>政府机关/事业单位</option>
                        <option value="2" <?php echo $detail['customer_economic_style'] == '2' ? 'selected' : '' ?>>国有事业</option>
                        <option value="3" <?php echo $detail['customer_economic_style'] == '3' ? 'selected' : '' ?>>个人经营/自由职业</option>
                        <option value="4" <?php echo $detail['customer_economic_style'] == '4' ? 'selected' : '' ?>>民营企业</option>
                        <option value="5" <?php echo $detail['customer_economic_style'] == '5' ? 'selected' : '' ?>>中外合资/中外合作/外商独资</option>
                        <option value="6" <?php echo $detail['customer_economic_style'] == '6' ? 'selected' : '' ?>>其他</option>
                    </select>
                </div>
            </div>
        </div>
        <h4>现单位地址</h4>
        <div class="row margin-bottom">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">省</span>
                    <select name="company_province" id="c_sheng"  class="form-control" disabled="disabled">
                        <option value="1">浙江省</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">市</span>
                    <select name="company_city" id="c_shi"  class="form-control" disabled="disabled">
                        <option value="1">杭州市</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">区</span>
                    <select name="company_town" id="c_qu"  class="form-control" disabled="disabled">
                        <option value="1">西湖区</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="input-group margin-bottom">
            <span class="input-group-addon">详细地址</span>
            <input name="company_address_add" type="text" class="form-control" value="<?php echo $detail['company_address_add']?>" disabled="disabled">
        </div>
        <h4>收入说明</h4>
        <div class="input-group margin-bottom">
            <span class="input-group-addon">收入来源</span>
            <select name="customer_main_income_source" class="form-control" disabled="disabled">
                <option value="0">请选择</option>
                <option value="1" <?php echo $detail['customer_main_income_source'] == '1' ? 'selected' : '' ?>>工薪（工商银行代发工资）</option>
                <option value="2" <?php echo $detail['customer_main_income_source'] == '2' ? 'selected' : '' ?>>工薪（他行代发工资）</option>
                <option value="3" <?php echo $detail['customer_main_income_source'] == '3' ? 'selected' : '' ?>>现金</option>
                <option value="4" <?php echo $detail['customer_main_income_source'] == '4' ? 'selected' : '' ?>>租金收入</option>
                <option value="5" <?php echo $detail['customer_main_income_source'] == '5' ? 'selected' : '' ?>>经营收入</option>
                <option value="6" <?php echo $detail['customer_main_income_source'] == '6' ? 'selected' : '' ?>>其他</option>
            </select>
        </div>
        <div class="input-group margin-bottom">
            <span class="input-group-addon">收入说明</span>
            <input name="customer_income_description" type="text" class="form-control" value="<?php echo $detail['customer_income_description']?>" disabled="disabled">
        </div>
        <h4>卡片寄送</h4>
        <div class="input-group margin-bottom">
            <span class="input-group-addon">送卡地址</span>
            <select name="customer_card_address" class="form-control" disabled="disabled">
                <option value="1" <?php echo $detail['customer_card_address'] == '1' ? 'selected' : '' ?>>住宅地址</option>
                <option value="2" <?php echo $detail['customer_card_address'] == '2' ? 'selected' : '' ?>>单位地址</option>
            </select>
        </div>
    </div>
</div>
<?php } ?>
