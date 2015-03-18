<?php    
/*
 * getListByPage
 * 根据页码获取列表
 * @param string $table 表名
 * @param string $order 排序
 * @param array $where 条件 默认为array()
 * @param int $num 每页显示数量 默认为10
 * @param int $ajax 是否AJAX
 * @return array $result 数组
 *         + array $result['list'] 结果集
           + string $result['page'] 分页
 */
function getListByPage($table, $order, $where = array(), $num = 10, $ajax = 0, $p = "") {

    //if ($num > C('LIST_MAX_COUNT')) {
      //  return 0;
   // }
    // 初始化参数
    $_GET['p'] = intval($_GET['p'])? intval($_GET['p']) : 1;
    $num = intval($num) > 0 ? intval($num) : 10;

    if ($p) {
        $_GET['p'] = intval($p)? intval($p) : 1;
    }

    $Source = M($table);
    $count= $Source->where($where)->count();

    // 要返回的数组
    $result = array();

    // 获取总页数
    $regNum = ceil($count / $num);

    // 验证当前请求页码是否大于总页数
    if ($_GET['p'] > $regNum) {
        return $result;
    }

    if (intval($ajax)) {
        $Page = new \Think\AjaxPage($count,$num);
    } else {
        $Page = new \Think\Page($count,$num);
    }

    //$Page->('')
    $result['page'] = trim($Page->show());
    $result['list'] = $Source->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
    return $result;
}

//注入SQL语句的分页
//使用时应注意，sql语句勿以分号结尾。
function getMyListByPage($Sql,$PageItems){
		$PageIndex = intval($_GET['p'])? intval($_GET['p']) : 1;
		$m = D();
		$count = count($m->query($Sql));
		$Page = new \Think\Page($count,$PageItems);
		$show = $Page->show();
		$list = $m->query($Sql." Limit ".($PageIndex-1)*$PageItems.",".$PageItems);
		$result['page'] = $show;
		$result['list'] = $list; 
		return $result;
}

// 中文字符串截取
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix='...') {
    if (strlen($str)<=3*$length) {
        return $str;
    }
    if (function_exists("mb_substr")) {
        return mb_substr($str, $start, $length, $charset) . $suffix;
    } elseif (function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset) . $suffix;
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    return $slice . $suffix;
}

/*
 * getValueByField
 * 获取数组字段值
 * @param array $array 数组 默认为 array()
 * @param string $field 字段名 默认为id
 *
 * @return array $result 数组(各字段值)
 *
 */
function getValueByField($array = array(), $field = 'id') {
    $result = array();
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $result[] = $value[$field];
        }
    }
    return $result;
}

/*
 * getDataByArray
 * 通过关联数组获取数据
 * @param string $table 表名
 * @param array $array 数组
 * @param string $arrayField 数组的字段
 * @param string $getField 要获取的字段
 *
 * @return array $result 获取的数据
 *      使用参考：通过活动获取对应的课时列表,传递M(课时), 活动数组及课时ID字段
 */
function getDataByArray($table, $array, $arrayField, $getField = '*') {
    $result = array();
    $result = M($table)->where(array($arrayField => array('IN', implode(',', getValueByField($array, $arrayField)))))->field($getField)->select();
    return setArrayByField($result, $arrayField);
}

/*
 * setArrayByField
 * 根据字段重组数组
 * @param array $array 数组 默认为 array()
 * @param string $field 字段名 默认为id
 *
 * @return array $result 重组好的数组
 *
 */
function setArrayByField($array = array(), $field = 'id') {
    $result = array();
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $result[$value[$field]] = $value;
        }
    }
    return $result;
}

// 获取文件后缀名
function getFileExt($filename) {

    $pathinfo = pathinfo($filename);
    return $pathinfo['extension'];
}

function myStripSlashes($str) {
    return stripslashes($str);
}

function myAddSlashes($str) {
    return get_magic_quotes_gpc() ? $str : addslashes($str);
}

function mk_dir($dir, $mode = 0755) {
    if (is_dir($dir) || @mkdir($dir,$mode)) return true;
    if (!mk_dir(dirname($dir),$mode)) return false;
    return @mkdir($dir,$mode);
}


// 获取中英文混搭字符串的长度
function strAllLength($str,$charset='utf-8'){
    if($charset=='utf-8') {
        $str = iconv('utf-8','gb2312',$str);
    }
    $num = strlen($str);
    $cnNum = 0;
    for($i=0;$i<$num;$i++){
        if(ord(substr($str,$i+1,1))>127){
            $cnNum++;
            $i++;
        }
    }
    $enNum = $num-($cnNum*2);
    $number = ($enNum/2)+$cnNum;
    return ceil($number);
}


// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from, $to) {

    $from = strtoupper($from) == 'UTF8'? 'utf-8': $from;
    $to   = strtoupper($to)   == 'UTF8'? 'utf-8': $to;

    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }

    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding ($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if($key != $_key )
                unset($fContents[$key]);
        }
        return $fContents;
    } else {
        return $fContents;
    }
}

// 过滤
function flipParam() {
    if ($_POST) {
        $_POST = flip($_POST);
    }

    if ($_GET) {
        $_GET = flip($_GET);
    }

    if ($_REQUEST) {
        $_REQUEST = flip($_REQUEST);
    }
}

// 过滤
function flip($arr) {
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            $arr[$key] = flip($value);
        } else {
            $arr[$key] = htmlspecialchars(addslashes(trim($value)));
        }
    }
    return $arr;
}

// 获取目录下的所有文件
function getFiles($dir) {
    $files = array();
    if(!is_dir($dir)) {
        return $files;
    }
    $handle = opendir($dir);
    if($handle) {
        while(false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $filename = $dir.$file;
                if(is_file($filename)) {
                    $files[] = $filename;
                }else {
                    $files = array_merge($files, $this->get_files($filename));
                }
            }
        }
        closedir($handle);
    }
    return $files;
}

/*
 * download
 * 下载
 * @param string $filePath 文件相对路径 例: /Uploads/test/
 * @param string $fileName 下载文件名称 例: uploads
 * @param string $ext  文件后缀名 例: rar
 *
 */
function download($filePath, $fileName, $ext, $flag = true) {
    if ($flag) {
        $filePath = $filePath . $fileName . '.' . $ext;
    }
    $filesize = filesize($filePath);
    $downloadType = C('DOWNLOAD_TYPE');
    $type = $downloadType[$ext] ? $downloadType[$ext] : 'octet-stream';
    // fopen读取文件，重新输出
    if ($handle = fopen($filePath, "r")) {

        Header("Content-type:text/html;charset=utf8");
        Header("Content-type: application/" . $type);
        Header("Accept-Ranges: bytes");
        Header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        Header("Pragma: public");
        Header("Content-Length: ". $filesize);
        Header("Content-Disposition: attachment; filename=" . $fileName . '.' . $ext);
        readfile($filePath);
        fclose($handle);
        clearstatcache();
        exit();
    } else {
        Header('Location: http://'.$_SERVER['HTTP_HOST']);
    }
}
