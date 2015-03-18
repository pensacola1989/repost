<?php
// 判断用户是否登录
// 如果登录返回PassportID 否则返回false
/*
function isLogin() {
    
    $id = M('SellerInfo')->field();
}

// 获取PassportID
function getPassportId() {

    if ($_COOKIE['passport_id']) {
        return intval($_COOKIE['passport_id']);
    } else {
        return 0;
    }
}

// 设置PassportID
function setPassportId($id) {
    if (is_null($id)) { // 销毁Passport
        Cookie('passport_id', NULL);
    } elseif (!empty($id)) { // 设置Passport
        $value = $id;
        Cookie('passport_id', $value);
        return $value;
    }
}

 */
