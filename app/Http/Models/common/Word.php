<?php

namespace App\Http\Models\common;

use Request;
use App\Http\Models\common\Constant;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * word文档
 *
 * @author 
 */
class Word 
{

    /**
     * 
     * @return 生成html头部
     * 
     */
    public function start() 
    {
        ob_start();
		echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
		xmlns:w="urn:schemas-microsoft-com:office:word"
		xmlns="http://www.w3.org/TR/REC-html40">';
    }

    /**
     * 保存到相应路径
     * 
     */
    public function save($path) 
    {
        $image = "http://static02.ifcar99.com/anjie/uploads/image/20180131/9b02c1378a9eef3cafcfd46ec197727f.jpg";
	    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>征信报告图片</title>
		<style>
		@font-face {
		font-family:"Times New Roman";
		}
		@font-face {
		font-family:"&#23435;&#20307;";
		}
		@font-face {
		font-family:"Arial";
		}
		table{border-collapse:collapse;border-color:#000;}
		td{ border-color:#000; padding:10px 5px; vertical-align:middle;}
		h1{ text-align:center}
		h3{ text-align:right;}
		</style>
		<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View><w:TrackMoves>false</w:TrackMoves><w:TrackFormatting/><w:ValidateAgainstSchemas/><w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid><w:IgnoreMixedContent>false</w:IgnoreMixedContent><w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText><w:DoNotPromoteQF/><w:LidThemeOther>EN-US</w:LidThemeOther><w:LidThemeAsian>ZH-CN</w:LidThemeAsian><w:LidThemeComplexScript>X-NONE</w:LidThemeComplexScript><w:Compatibility><w:BreakWrappedTables/><w:SnapToGridInCell/><w:WrapTextWithPunct/><w:UseAsianBreakRules/><w:DontGrowAutofit/><w:SplitPgBreakAndParaMark/><w:DontVertAlignCellWithSp/><w:DontBreakConstrainedForcedTables/><w:DontVertAlignInTxbx/><w:Word11KerningPairs/><w:CachedColBalance/><w:UseFELayout/></w:Compatibility><w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel><m:mathPr><m:mathFont m:val="Cambria Math"/><m:brkBin m:val="before"/><m:brkBinSub m:val="--"/><m:smallFrac m:val="off"/><m:dispDef/><m:lMargin m:val="0"/> <m:rMargin m:val="0"/><m:defJc m:val="centerGroup"/><m:wrapIndent m:val="1440"/><m:intLim m:val="subSup"/><m:naryLim m:val="undOvr"/></m:mathPr></w:WordDocument></xml><![endif]-->
		</head>
		<body>
		<h1>征信报告图片</h1>
		<table border="1" cellpadding="3" cellspacing="0" >
		<tr >
		<td width="53" valign="center" >身份证</td>
		<td width="570" valign="center" colspan="12" ><img src="'.$image.'" width="120" height="120" /></td>
		</tr>
		<tr >
		<td width="53" valign="center" >授权书</td>
		<td width="570" valign="center" colspan="12" ><img src="'.$image.'" width="120" height="120" /></td>
		</tr>
		</table>
		</body>';

		$this->end();
  		$data = ob_get_contents();
  		ob_end_clean();
    
  		$this->wirtefile ($path,$data);
    }
    /**
     * 结束html
     * 
     */
    public function end()
    {
    	echo "</html>";
    }

    /**
     * 写入HTML
     * 
     */
    public function wirtefile($fn,$data) 
    {
        $fp=fopen($fn,"wb");
		fwrite($fp,$data);
		fclose($fp);
    }

}
