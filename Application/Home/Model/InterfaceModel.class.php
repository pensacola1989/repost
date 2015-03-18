<?php
namespace Home\Model;
use Think\Model;
header('Content-Type: text/html;charset = utf-8');
class InterfaceModel extends Model {

     // 获得前端的基本展示信息
    public function getPointCustomStyle($sellerNick) {

        $pointInfo = M('SellerUiSettingsInfo')->field('sellerid,UISettings,sellernick,background,modulewidth,MainBgColor,PopupSettings')->where(array('SellerNick' => $sellerNick))->find();

        if ($pointInfo) {

            $data['SellerId'] = $pointInfo['sellerid'];
            $data['SellerNick'] = $pointInfo['sellernick'];
            $PopupSettings = is_null($pointInfo['PopupSettings']) ? '' : $pointInfo['PopupSettings'];
            // $PopupSettings示例http://img01.taobaocdn.com/imgextra/i1/289589474/T2aCZ9XM0XXXXXXXXX-289589474.png;18;#ffffff
            // 对弹出层的信息进行拆分
            $popArr = explode(';', $PopupSettings);
            
            if (count($PopupSettings) == 3) {

                if ($popArr[0] != '') {
                    $data['PopupBg'] = $popArr[0];
                }

                if ($popArr[1] != '') {
                    $data['PopupFontSize'] = $popArr[1];
                }

                if ($popArr[2] != '') {
                    $data['PopupFontColor'] = $popArr[2];
                }
            }

            $data['MainBg'] = $pointInfo['background'];
            $data['MainColor'] = $pointInfo['MainBgColor'];
            $data['ModuleWidth'] = $pointInfo['modulewidth'];

            if ($pointInfo['UISettings']) {
                
                //对UISettings进行拆分，UISettings格式member,16,21,213,67,;fav,66,688,60,40,;reg,66,463,61,38,;share,65,575,60,40,;exchange,67,352,60,36,;myjoin,131,894,60,20,;tradepoint,66,795,128,41,;luckytitle,193,74,165,40,;luckytime,271,37,218,14,;luckydogs,311,38,234,115,;luckydetail,440,209,60,18,;game,172,344,595,306,http://img01.taobaocdn.com/imgextra/i1/289589474/T2aUxVX4hXXXXXXXXX-289589474.jpg;click,278,471,341,94,;
                $uiArr = explode(';', $pointInfo['UISettings']);

                foreach ($uiArr as $val) {

                    $valArr = explode(',', $val);
                    if (count($valArr) == 6) {
                        $uiInfo['SettingName'] = $valArr[0];
                        $uiInfo['Top'] = intval($valArr[1]);
                        $uiInfo['Left'] = intval($valArr[2]);
                        $uiInfo['Width'] = intval($valArr[3]);
                        $uiInfo['Height'] = intval($valArr[4]);
                        $uiInfo['Link'] = $valArr[5];

                        $data['UISettingsList'][] = $uiInfo;
                    }
                }
            }

        }

        return $data;
    }

    // 读取抽奖活动信息
    public function GetLuckyActivityInfo($sellernick, $sellerid) {

        $luckyInfo = M('SellerActivityInfo')->field('AcType,SellerId,title,datetype,startdate,enddate,LimitJoinTimes,LimitLuckyTimes,LimitPerEveryday,AcMemo')->where(array('SellerId' => $sellerid))->find();

        if ($luckyInfo) {

            $data['Title'] = $luckyInfo['title'];
            $data['DateType'] = is_null($luckyInfo[1])? 0 : $luckyInfo['datetype'];
            $data['StartDate'] = $luckyInfo['startdate'];
            $data['EndDate'] = $luckyInfo['enddate'];

            if ($luckInfo['LimitJoinTimes']) {
                $data['LimitJoinTimes'] = $luckyInfo['LimitJoinTimes'];
            }

            if ($luckInfo['LimitLuckyTimes']) {
                $data['LimitLuckyTimes'] = $luckyInfo['LimitLuckyTimes'];
            }

            if ($luckInfo['LimitPerEveryday']) {
                $data['LimitPerEveryday'] = $luckyInfo['LimitPerEveryday'];
            }

            if ($luckInfo['AcType']) {
                $data['LuckyType'] = $luckyInfo['AcType'];
            }

            $data['LuckyDogs'] = $this->GetLuckyDogList($sellerid);
            $data['LuckyAwardInfo'] = $this->GetLuckyAwardsInfo($sellerid);
            $data['Memo'] = $luckyInfo['AcMemo'];
        }

        return $data;
    }

    // 获取卖家积分配置
    public function GetPointSettings($sellerId) {

        $selpInfo = M('SellerPointInfo')->field('pointtype,pointvalue,pointcondition')->where(array('SellerId' => $sellerId))->select();

        if ($selpInfo) {

            foreach ($selpInfo as $val) {
                
                $data['PointType'] = intval($val['pointtype']);
                $data['PointValue'] = intval($val['pointvalue']);
                $data['PointCondition'] = $val['pointcondition'];

                $dataArr[] = $data;
            }
        }
        return $dataArr;
    }

    // 获取中奖信息清单
    public function GetLuckyDogList($sellerId) {

        $sql = "select m.membernick,a.AwardTitle from t_member_activity d " .
                     " inner join t_seller_award_info a on d.AwardSign=a.AwardSign and a.sellerid=d.sellerid " .
                     "inner join t_member_info m on  m.ID=d.memberid and d.sellerid=m.sellerid where d.sellerid =" . $sellerId ." order by d.id desc limit 0,20";
        $luckyDogArr = M()->query($sql);
        
        if ($luckyDogArr) {

            foreach ($luckyDogArr as $val) {

                $mn = $val['membernick'];
                $data[] = mb_substr($mn, 0, 1, 'UTF-8') . '***' . mb_substr($mn, -1, 1, 'UTF-8') . ' 获得' . $val['AwardTitle'] . ';';
            }
        }

        return $data;
    }

    // 获取抽奖的奖品信息
    public function GetLuckyAwardsInfo($sellerId) {

        $awardArr = M('SellerAwardInfo')->field('AwardTitle,AwardSign,awardtype,awardurl,AwardPicUrl,AwardGetUrl')->where(array('SellerId' => $sellerId))->select();

        if ($awardArr) {

            foreach ($awardArr as $val) {
                
                $data['AwardTitle'] = $val['AwardTitle'];
                $data['AwardSign'] = $val['AwardSign'];
                $data['AwardType'] = $val['awardtype'];
                $data['AwardUrl'] = $val['awardurl'];
                $data['AwardPicUrl'] = $val['AwardPicUrl'];
                $data['AwardGetUrl'] = $val['AwardGetUrl'];
                $datas[] = $data;
            }
        }

        return $datas;
    }

    // 获取买家收藏数据信息
    public function GetFavoriteInfo($sellerId, $showCount = 0) {

        if ($showCount) {
            
            $favInfo = M('SellerFavorite')->field('ItemId,Points,ItemTitle,ItemPicUrl,ItemUrl')->where(array('SellerId' => $sellerId))->limit("0, {$showCount}")->select(); 
        } else {
            
            $favInfo = M('SellerFavorite')->field('ItemId,Points,ItemTitle,ItemPicUrl,ItemUrl')->where(array('SellerId' => $sellerId))->select(); 
        }

        if ($favInfo) {

            foreach ($favInfo as $val) {
                
                $data['ItemId'] = intval($val['ItemId']);
                $data['Points'] = intval($val['Points']);
                $data['ItemTitle'] = $val['ItemTitle'];
                $data['ItemUrl'] = $val['ItemUrl'];
                $data['PicUrl'] = $val['ItemPicUrl'];

                $datas[] = $data;
            }
        }

        return $datas;
    }

    // 获得兑换信息
    public function GetExchangeInfo($sellerId, $showCount = 0) {

        if ($showCount) {

            $exInfo = M('SellerExchange')->field('id,itemtype,itemtitle,points,itemprice,itemnum,perlimit,startdate,enddate,picurl,itemurl,geturl')->where(array('SellerId' => $sellerId))->limit("0, {$showCount}")->select();
        } else {
            
            $exInfo = M('SellerExchange')->field('id,itemtype,itemtitle,points,itemprice,itemnum,perlimit,startdate,enddate,picurl,itemurl,geturl')->where(array('SellerId' => $sellerId))->select();
        }

        if ($exInfo) {

            foreach ($exInfo as $val) {

                $data['ID'] = intval($val['id']);
                $data['Points'] = intval($val['points']);
                $data['ItemNum'] = intval($val['itemnum']);
                $data['PerLimit'] = intval($val['perlimit']);
                $data['ItemType'] = intval($val['itemtype']);
                $data['ItemPrice'] = floatval($val['itemprice']);
                $data['GetUrl'] = $val['geturl'];
                $data['ItemUrl'] = $val['itemurl'];
                $data['PicUrl'] = $val['picurl'];
                $data['StartDate'] = $val['startdate'];
                $data['EndDate'] = $val['enddate'];
                $data['ItemTitle'] = $val['itemtitle'];

                $datas[] = $data;
            }
        }

        return $datas;
    }

    // 获取卖家分享数据
    public function GetShareInfo($sellerId, $showCount = 0) {

        if ($showCount) {

            $shaInfo = M('SellerShare')->field('id,sharedesc,points,shareurl,picurl')->where(array('SellerId' => $sellerId))->limit("0, {$showCount}")->select();
        } else {

             $shaInfo = M('SellerShare')->field('id,sharedesc,points,shareurl,picurl')->where(array('SellerId' => $sellerId))->select();
        }

        if ($shaInfo) {

            foreach ($shaInfo as $val) {

                $data['Points'] = intval($val['points']);
                $data['ShareDesc'] = $val['sharedesc'];
                $data['ShareUrl'] = $val['shareurl'];
                $data['PicUrl'] = $val['picurl'];
                $data['ShareId'] = intval($val['id']);

                $datas[] = $data;
            }
        }

        return $datas;
    }

    // 获取买家的真实昵称
    public function GetMemberNick($sellerId, $memberMixNick, $bmnonly, $memberId = -1) {

        if ($sellerId) {

            $memberInfo = M('MemberInfo')->field('MemberNick,ID,memberMixNick')->where(array('SellerId' => $sellerId, 'MemberOnlyMixNick' => $bmnonly))->find();

            if ($memberInfo && count($memberInfo)) {

                $memberId = intval($memberInfo['id']);

                //这里是第一次没有从模块访问时不能得到模块混淆昵称，所以如果不存在，则更新填补
                if (!$memberInfo['memberMixNick']) {

                    if (trim($memberMixNick)) {

                        $res = M('MemberInfo')->where(array('SellerId' => $sellerId, 'ID' => $memberId))->save(array('MemberMixNick' => $memberMixNick));
                    }
                }

                return $memberInfo['MemberNick'];
            }
        }
    }

    public function SaveMemberNickAndGetMemberId($sellerId, $memberMixNick, $bmnonly, $memberNick) {

        $res = M('MemberInfo')->add(array('SellerId' => $sellerId, 'MemberNick' => $memberNick, 'MemberMixNick' => $memberMixNick, 'MemberOnlyMixNick' => $bmnonly, 'Created' => date('Y-m-d H:i:s', time())));

        if ($res) {

            return intval($res);
        }
    }

    // 获取会员的基本信息
    public function GetMemberInfo($sellerId, $memberId, $bmn) {

        if ($memberId && $memberId != 0 && $memberId != -1) {

            $where['ID'] = $memberId;
        } elseif ($bmn) {

            $where['MemberOnlyMixNick'] = $bmn;
        }

        $where['SellerId'] = $sellerId;

        $memInfo = M('MemberInfo')->field('id,MemberNick,MemberLevel,Points,IconFav,IconShare,IconLucky,IconTrade,IconExchange')->where($where)->find();

        if ($memInfo) {

            $data['Id'] = is_null($memInfo['id']) ? 0 : intval($memInfo['id']);
            $data['Nick'] = is_null($memInfo['MemberNick']) ? '' : $memInfo['MemberNick'];
            $data['Level'] = is_null($memInfo['MemberLevel']) ? 0 : intval($memInfo['MemberLevel']);
            $data['Point'] = is_null($memInfo['Points']) ? 0 : intval($memInfo['Points']);
            $data['IconFav'] = is_null($memInfo['IconFav']) ? 0 : intval($memInfo['IconFav']);
            $data['IconShare'] = is_null($memInfo['IconShare']) ? 0 : intval($memInfo['IconShare']);
            $data['IconLucky'] = is_null($memInfo['IconLucky']) ? 0 : intval($memInfo['IconLucky']);
            $data['IconTrade'] = is_null($memInfo['IconTrade']) ? 0 : intval($memInfo['IconTrade']);
            $data['IconExchange'] = is_null($memInfo['IconExchange']) ? 0 : intval($memInfo['IconExchange']);
        } else {
            $data['Id'] = -1;
        }

        return $data;
    }

    // 获取买家的抽奖活动的信息
    public function GetLuckyFewInfo($sellerId) {

        $luckFewInfo = M('SellerActivityInfo')->field('sellerid,startdate,enddate,points,DateType,LimitJoinTimes,LimitLuckyTimes,LimitPerEveryday')->where(array('SellerId' => $sellerId))->find();

        if ($luckFewInfo) {

            return $luckFewInfo;
        }
    }

    public function IsExceedMaxJoinLimit($sellerId, $bueryId, $maxJoinLimit) {

        $sql = 'select  count(id)  from t_member_activity where sellerId=' . $sellerId . 'and memberid=' . $buerId;
        $res = M()->query($sql);

        if ($maxJoinLimit - intval($res) <= 0) {

            return true;
        }
    }

    public function GetMemberPoints($sellerId, $memberId) {

        if ($memberId == -1) {
            return 0;
        }

        $memPoint = M('MemberInfo')->where(array('SellerId' => $sellerId, 'ID' => $memberId))->getField('Points');

        if ($memPoint) {

            return $memPoint;
        }
    }

    public function IsExceedMaxLuckyLimit($sellerId, $bueryId, $maxLuckyLimit) {

        $sql = "select  count(id)  from T_LuckyDrawTimes where sellerId=" . $sellerId . " and memberid=" . $bueryId . " and luckystatus>0";

        $res = M()->query($sql);

        if ($maxLuckyLimit - $res <= 0) {

            return true;
        }
    }

    public function SaveLuckyInfo($sellerid, $memberid, $points, $awardSign, $status, $source) {

        if ($memberid != -1) {

            $DrawDate = date('Y-m-d H:i:s', time());
            
            $res = M('MemberActivity')->add(array('SellerId' => $sellerid, 'MemberId' => $memberid, 'AwardSign' => $awardSign, 'Points' => $points, 'DrawDate' => $DrawDate, 'LuckyStatus' => $status, 'Source' => $source));
            
            $sql ="update t_member_info set Points=Points-" . $points . ",memberlevel=memberlevel+1, iconlucky=iconlucky+1 where sellerid=" . $sellerid . " and id=" . $memberid;
            
            $res = M()->query($sql);
        }
    }

    // 获取活动的详细说明，针对memo字段
    public function GetLuckyMemo($sellerid) {
    
        $memo = M('SellerActivityInfo')->where(array('SellerId' => $sellerid))->getField('AcMemo');
        if ($memo) {

            $search = array('\r', '\n');
            $replace = array('', '<br>');
            $memo = str_replace($search, $replace, $memo);
            return $memo;
        }
    }


    // 更新中奖后的状态
    public function UpdateAwardStatus($memberId, $sellerId, $awardsign) {

        $id = M('MemberActivity')->where(array('SellerId' => $sellerId, 'MemberId' => $memberId, 'AwardSign' => $awardsign))->order('id desc')->getField('id');

        if ($id) {

            $FetchDate = date('Y-m-d H:i:s', time());
            $res = M('MemberActivity')->where(array('ID' => $id))->save(array('LuckyStatus' => 2, 'FetchDate' => $FetchDate));
        }

        if ($res) {
            return 1;
        } else {
            return 0;
        }

    }

    public function IsExchanged($sellerId, $exchangeId, $memberId) {

        $sql = "select StartDate,enddate,PerLimit,itemnum,(PerLimit-(select count(id) from t_member_exchange where sellerid=" . $sellerId . " and exchangeId=" . $exchangeId . " and memberid=" . $memberId . ")) as pl" . ",(itemNum-(select count(id) from t_member_exchange where sellerid=" . $sellerId . " and exchangeId=" . $exchangeId . ")) as ip, "."((select points from t_member_info where sellerid=" . $sellerId . " and id=" . $memberId . ")- Points) as iscan from t_seller_exchange  where sellerid=" . $sellerId . " and ID=" . $exchangeId;

        $res = M()->query($sql);

        if ($res) {

            $start = strtotime($res[0]['StartDate']);
            $end = strtotime($res[0]['enddate']);
            $pl = intval($res[0]['pl']);
            $ip = intval($res[0]['ip']);
            $iscal = intval($res[0]['iscan']);

            if ($start > time()) {

                return "兑换活动未开始";
            } elseif ($end < time()) {

                return "兑换活动已过期";
            }

            if ($pl < 1) {

                return "超过每人限制数量";
            }

            if ($ip < 1) {

                return "奖品已被兑换完毕";
            }

            if ($iscan < 0) {

                return "积分不够，请去赚积分";
            }

            return "success";
        }
    }


    // 兑换奖品
    public function ExchangeItem($sellerId, $memberId, $exchangeId, $source) {

        $points = M('SellerExchange')->where(array('SellerId' => $sellerId, 'ID' => $exchangeId, 'EStatus' => 1))->getField('Points');

        $id = M('MemberExchange')->add(array('SellerId' => $sellerId, 'MemberId' => $memberId, 'Points' => $points, 'ExchangeId' => $exchangeId, 'Created' => time(), 'Source' => $source));

        $sql = "update t_member_info set Points=Points-" . $points . ",memberlevel=memberlevel+1, iconexchange=iconexchange+1 where sellerid=" . $sellerId . " and id=" . $memberId;

        M()->query($sql);

        if ($id) {

            return 1;
        }

    }

    // 收藏店铺或宝贝
    public function FavoriteItem($sellerId, $memberId, $itemId, $source) {

         

        $id = M('MemberFavorite')->where(array('SellerId' => $sellerId, 'MemberId' => $memberId, 'FavoriteId' => $itemId))->getField('ID');

        if (!$id) {

            $point = $this->GetPointByType($sellerId, $_SESSION['pointConf']['POINT_FAVORITE']);

            // 保存收藏店铺的积分
            if ($itemId == -1) {

                $sql = " insert into t_member_favorite (sellerid,memberid,points,favoriteid,created,source) values (" . $sellerId . "," . $memberId . "," . $point . "," . $itemId . ",NOW()," . $source . "); ";
                $sqll = " update t_member_info set Points=Points+" . $point . ",memberlevel=memberlevel+1, iconfav=iconfav+1 where sellerid=" . $sellerId . " and id=" . $memberId;
            } else {
            
                $sql =  " insert into t_member_favorite (sellerid,memberid,points,favoriteid,created,source) values (" . $sellerId . "," . $memberId . ",(select points from t_seller_favorite where sellerid=" . $sellerId . " and itemid=" . $itemId . " and fstatus=1)," . $itemId . ",NOW()," . $source . ");";
                $sqll = " update t_member_info set Points=Points+(select points from t_seller_favorite where sellerid=" . $sellerId . " and itemid=" . $itemId . " and fstatus=1),memberlevel=memberlevel+1, iconfav=iconfav+1 where sellerid=" . $sellerId . " and id=" . $memberId;
            }

            $res = M()->query($sql);
            $ress = M()->query($sqll);

            if ($res) {

                return 1;
            } else {

                return 0;
            }

        } else {
            
            return 0;
        }
    }

    public function GetPointByType($sellerId, $pointType) {

        $point = M('SellerPointInfo')->where(array('SellerId' => $sellerId, 'PointType' => $pointType))->getField('PointValue');

        if ($point) {

            return $point;
        }
    }

    public function GetRegisterInfo($sellerId, $memberId, $points, $source) {

        // 0未签到，1已经签到
        $registerDate = date('Y-m-d', time()) . '%';
        $id = M('MemberRegister')->where(array('SellerId' => $sellerId, 'MemberId' => $memberId, 'RegisterDate' => array('like', $registerDate)))->getField('ID');
       
        if ($id) {

            $data['Status'] = 1;
        } else {

            $sql = " insert into  t_member_register (sellerid,memberid,RegisterDate,points,source) values (" . $sellerId . "," . $memberId . ",'" . date('Y-m-d H:i:s') . "'," . $points . "," . $source . ")";
            
            $update = "update t_member_info set Points=Points+" . $points . ",memberlevel=memberlevel+1  where sellerid=" . $sellerId . " and id=" . $memberId;

            $count =  "select count(id) from t_member_register where sellerid=" . $sellerId . " and memberid=" . $memberId;

            $data['Status'] = 0;

            M()->query($sql);
            M()->query($update);
            $res = M()->query($count);

            if ($res) {

                $data['RegisterTimes'] = $res[0]['count(id)'];
            }
        }

        return $data;
    }

    public function GetSessionKeyById($sellerId) {

        $sessionKey = M('SellerInfo')->where(array('ID' => $sellerId))->getField('SessionKey');

        if ($sessionKey) {

            return $sessionKey;
        }
    }


    // 获取最后一次获得积分的时间
    public function GetPointStartDate($sellerId, $memberId) {

        $created = M('MemberPoint')->where(array('SellerId' => $sellerId, 'MemberId' => $memberId))->order('Created desc')->getField('Created');

        if ($created) {

            return $created;
        }
    }

    // 获得交易类分数配置
    public function GetTradePointSettings($sellerId) {

         

        $res = M('SellerPointInfo')->where(array('SellerId' => $sellerId, 'PointType' => $_SESSION['pointConf']['POINT_TRADE']))->field('PointValue,PointCondition')->find();

        if ($res) {

            $data[] = $res['pointcondition'];
            $data[] = $res['pointvalue'];
        }

        return $data;
    }

    public function GivePoint($sellerId, $memberId, $points, $ruleValue, $source) {

        $sql = " insert into t_member_point (sellerid,memberid,points,ruleType,ruleValue,created,source) values (" . $sellerId . "," . $memberId . "," . $points . "," . $_SESSION['pointConf']['POINT_ACTIVITY'] . "," . $ruleValue . ",NOW()," . $source . ")";
        
        $update = "update t_member_info set Points=Points+" . $points . ",memberlevel=memberlevel+1,icontrade=icontrade+" . $ruleValue . " where sellerid=" . $sellerId . " and id=" . $memberId;
        
        $updateSql = "update t_member_activity set luckystatus=2,fetchdate=NOW() where sellerid=" . $sellerId . " and memberid=" . $memberId . " and awardsign=" . $ruleValue . " and drawdate<=NOW()";
        
        $res = M()->query($sql);
        $update = M()->query($update);
        $updateRes = M()->query($updateSql);
        return 1;
    }


    // 获得评价类分数配置
    public function GetRatePointSettings($sellerId) {

        $points = M('SellerPointInfo')->where(array('SellerId' => $sellerId, 'PointType' => $_SESSION['pointConf']['POINT_RATE']))->getField('PointValue');

        if ($points) {

            return intval($points);
        }
    }

    public function GetMemberPointList($sellerId, $memberId) {

        $sql = "select a.points,a.awardsign,a.drawdate,a.luckystatus,i.AwardTitle from  t_member_activity a left join t_seller_award_info i on a.AwardSign=i.AwardSign and a.sellerid=i.sellerid where a.sellerid=" . $sellerId . " and a.memberid=" . $memberId . " order by a.id desc limit 0,10";

        $res = M()->query($sql);

        if ($res) {

            foreach ($res as $val) {

                $data['AwardTitle'] = is_null($val['AwardTitle']) ? '' : $val['AwardTitle'];
                $data['DrawDate'] = is_null($val['drawdate']) ? '' : $val['drawdate'];

                if ($val['luckystatus'] && intval($val['luckystatus']) > 0) {

                    $data['LuckyStatus'] = '已中奖';
                } else {
                    
                    $data['LuckyStatus'] = '未中奖';
                }

                $data['PointValue'] = is_null($val['points']) ? 0 : $val['points'];

                $datas[] = $data;
            }

            $point['PointActivityList'] = $datas;
        }

        $sql = "select a.points,a.created from  t_member_favorite  a where a.sellerid=" . $sellerId . " and a.memberid=" . $memberId . " order by a.id desc limit 0,10";

        $res = M()->query($sql);

        if ($res && count($res) > 0) {

            foreach ($res as $val) {

                $memberFavorite['PointValue'] = is_null($val['points']) ? 0 : $val['points'];
                $memberFavorite['Created'] = is_null($val['created']) ? '' : $val['created'];
                $memberFavoriteArr[] = $memberFavorite;
            }

            $point['PointFavoriteList'] = $memberFavoriteArr;
        }

        $sql = "select a.points,a.registerdate from  t_member_register  a where a.sellerid=" . $sellerId . " and a.memberid=" . $memberId . " order by a.id desc limit 0,10";

        $res = M()->query($sql);

        if ($res && count($res) > 0) {

            foreach ($res as $val) {

                $memberRegister['PointValue'] = is_null($val['points']) ? 0 : $val['points'];
                $memberRegister['Created'] = is_null($val['registerdate']) ? '' : $val['registerdate'];
                $memberRegisterArr[] = $memberRegister;
            }

            $point['PoinRegList'] = $memberRegisterArr;
        }

        $sql = "select a.points,a.created from  t_member_share  a where a.sellerid=" . $sellerId . " and a.memberid=" . $memberId . " order by a.id desc limit 0,10";

        $res = M()->query($sql);

        if ($res && count($res)) {

            foreach ($res as $val) {

                $memberShare['PointValue'] = is_null($val['points']) ? 0 : $val['points'];
                $memberShare['Created'] = is_null($val['created']) ? '' : $val['created'];
                $memberShareArr[] = $memberShare;
            }

            $point['PointShareList'] = $memberShareArr;
        }

        $sql = "select a.points,a.created from  t_member_exchange  a where a.sellerid=" . $sellerId . " and a.memberid=" . $memberId . " order by a.id desc limit 0,10";

        $res = M()->query($sql);

        if ($res && count($res)) {

            foreach ($res as $val) {

                $memberExchange['PointValue'] = is_null($val['points']) ? 0 : $val['points'];
                $memberExchange['Created'] = is_null($val['created']) ? '' : $val['created'];
                $memberExchangeArr[] = $memberExchange;
            }

            $point['PointExchangeList'] = $memberExchangeArr;
        }

        $sql = "select a.points,a.created from  t_member_point  a where a.sellerid=" . $sellerId . " and a.memberid=" . $memberId . " order by a.id desc limit 0,10";

        $res = M()->query($sql);

        if ($res && count($res) > 0) {

            foreach ($res as $val) {

                $memberPoint['PointValue'] = is_null($val['points']) ? 0 : $val['points'];
                $memberPoint['Created'] = is_null($val['created']) ? '' : $val['created'];
                $memberPointArr[] = $memberPoint;
            }

            $point['PointOtherList'] = $memberPointArr; 
        }

        return $point;
    }

    public function GetPointAwards($sellerId) {

        $res = M('SellerAwardInfo')->field('AwardSign,AwardChance,AwardNum,AwardLimit')->where(array('SellerId' => $sellerId))->select();

        if ($res) {

            return $res;
        }
    }

    public function GetTakenAwards($sellerId, $awardSign, $startDate, $endDate) {

        $sql = "select count(id) from t_member_activity  where sellerId=" . $sellerId . " and awardsign=" . $awardSign . " and drawdate between '" . $startDate . "' and '" . $endDate . "'";

        $res = M()->query($sql);
        
        return intval($res[0]['count(id)']);
    }

    public function GetAwardInfo($sellerId, $awardSign) {

        $res = M('SellerAwardInfo')->field('AwardTitle,AwardGetUrl,AwardPicUrl,AwardUrl,AwardType')->where(array('SellerId' => $sellerId, 'AwardSign' => $awardSign))->find();

        if ($res) {

            $data['AwardTitle'] = $res['AwardTitle'];
            $data['AwardType'] = $res['AwardType'];
            $data['AwardSign'] = $awardSign;
            
            if ($res['AwardUrl']) {

                $data['AwardUrl'] = $res['AwardUrl'];
            }

            if ($res['AwardPicUrl']) {

                $data['AwardPicUrl'] = $res['AwardPicUrl'];
            }

            if ($res['AwardGetUrl']) {

                $data['AwardGetUrl'] = $res['AwardGetUrl'];
            }
        }

        return $data;
    }
}




