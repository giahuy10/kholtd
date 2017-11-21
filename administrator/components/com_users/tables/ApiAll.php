<?php
class ApiAll{
    static function jsonDecodeVina($str = ''){
        $arrJson = array();
        if($str!=''){
            $arrJson = json_decode($str,true);
            if($arrJson==NULL){
                $str = substr($str,3,strlen($str));
                $str = preg_replace('/[[:^print:]]/', '', $str);
                $arrJson = json_decode($str,true);
            }
        }
        return $arrJson;
    }

    static function tracuuthehoivien($coupon='',$promotion_id=0,$token = ''){
        $couponsActive = array();
        if($token==''){
            $user = UserMerchant::$current->data;
            $accviplus = json_decode($user['accviplus'],true);
            if(!isset($accviplus['userviplus']) || !isset($accviplus['passviplus'])){
                return false;
            }
            $token = FunctionLib::vinaS();
        }

        if($token!=''){
            if($coupon!='' && $promotion_id>0){
//                http://quantri.vinaphoneplus.com.vn/api/APIForWebApp/API_ThongTinMaUuDai
//                http://quantri.vinaphoneplus.com.vn/api/
                $data = json_encode(array('Token' => $token,'MaUuDai' => $coupon));
                if(is_numeric($coupon)){
                    $data = json_encode(array('Token' => $token,'SoThe' => $coupon));
                    $urlApi = VINA_API_NEW.'APIForAppMobile/GD7/Api_LayThongTinTheHoiVien';
                }
                else{
                    $urlApi = VINA_API_NEW.'APIForWebApp/API_ThongTinMaUuDai';
                }
//                $urlApi = VINA_API.'?json=neon/Tracuuthehoivien&token='.$token.'&cardNumber='.$coupon.'&promotion_id='.$promotion_id;
                #$urlApi= 'http://viplus.vinaphone.com.vn/?json=neon/Tracuuthehoivien&token=nasco|1492570715|D7s23Wgv7G0421z1oqQ8vgCnj7OrMuummTMzjR9GKPw|fe2412a20081c3488e195008c4f0971d04c9706291183c079f898d733071c130&cardNumber=58DEF8327105C&promotion_id=1831';
                $idLog = Coupon::insertLogApi($urlApi.' => '.$data,'Tracuuthe',$coupon,$promotion_id);
                $str = self::postCurl($urlApi,$data);
                Coupon::updateLogApi($idLog,$str);
                if($str!=''){
                    $couponsActive = json_decode($str,true);
                    if(isset($couponsActive['ErrorCode']) && $couponsActive['ErrorCode'] === 200){
                        $couponsActive = isset($couponsActive['Data'][0]) ? $couponsActive['Data'][0] : $couponsActive['Data'];
                        $couponsActive['status'] = 'true';
                    }
                }
            }
        }
        return $couponsActive;
    }
    static function activeCode($promo , $money = 300000){
        $user = UserMerchant::$current->data;
        $accviplus = json_decode($user['accviplus'],true);
        if(!isset($accviplus['userviplus']) || !isset($accviplus['passviplus'])){
            return false;
        }
        if(!FunctionLib::vinaS_active()){
            return false;
        }
//        {"userviplus":"onecard_quocte","passviplus":"quocte123456","price":870000}
//        $urlApi = VINA_API.'?json=neon/checkcodebyshop&username='.$accviplus['userviplus'].'&password='.$accviplus['passviplus'].'&promotioncode='.$promo.'&total='.$accviplus['price'].'&promotion_id='.$user['promotion_id'];
        $urlApi = VINA_API_NEW.'APIForWebApp/API_ApDungMaUuDai';
        $data = array(
            'Token' => FunctionLib::vinaS_active(),
            'SoTheHoiVien' => '',
            'MaUuDai' => '',
            'GoiUuDaiId' => CGlobal::$evVina[$user['promotion_id']]['GoiUuDaiId'],
            'ChuongTrinhUuDaiId' => CGlobal::$evVina[$user['promotion_id']]['ChuongTrinhUuDaiId'],
            'CuaHangApDungId' => CGlobal::$userVina[$accviplus['userviplus']],
            'GiaTriUuDai' => $accviplus['price']
        );
        if(is_numeric($promo)){
            $data['SoTheHoiVien'] = $promo;
        }
        else{
            $data['MaUuDai'] = $promo;
        }
        $idLog = Coupon::insertLogApi($urlApi.' => '.json_encode($data),'active',$promo,$user['promotion_id']);
        $str = self::postCurl($urlApi,json_encode($data));
        Coupon::updateLogApi($idLog,$str);
        if($str!=''){
            return self::jsonDecodeVina($str);
        }
        return false;
    }
    static function reportvinaPlus($time,$promotion_id=0,$user='',$pass=''){
        $token = self::login($user,$pass,'LoginDailyReport');
        if($token!=''){
//            {"Token":"","MaChuongTrinhUuDai":"PRM_1831","TuNgay":"23/09/2017","DenNgay":"24/09/2017","TinhTrang":""}
            //http://viplus.vinaphone.com.vn/?json=neon/shopGetAll_onecard_promotion&token=nasco%7C1493024188%7CEqzcVXK652ydQWFx7jWDGOjSVVPHNlYtVcDLOd0USIJ%7C02cffa067ba69e777fb0cf831dfdc609ca44cdda58f9bccdc8393b06230a6f9a&promotion_id=1831&tungay=2017-04-01&denngay=2017-04-01

            $data = array(
                "Token" =>$token,
                "MaChuongTrinhUuDai"=>"PRM_".$promotion_id,
                "TuNgay"=>date('d/m/Y',$time),
                "DenNgay"=>date('d/m/Y',$time+86400),
                "TinhTrang"=>""
            );
            $urlApi = VINA_API_NEW.'APIForAppMobile/GD7/Api_LayDanhSachUuDai';

            $data = json_encode($data);
            $idLog = Coupon::insertLogApi($urlApi.' => data: '.$data,'dailyReport');

            $response = self::postCurl($urlApi,$data);


//            $str = substr($str,3,strlen($str));
//            $response = preg_replace('/[[:^print:]]/', '', $response);
            Coupon::updateLogApi($idLog,$response);
            $response = self::jsonDecodeVina($response);
            return $response;
        }
        return false;
    }

    static function insertReport($arrReport,$promotion_id=0){
        $returnArr = array('numInsert'=>0,'numActice'=>0);
        if($promotion_id>0&&!empty($arrReport)){
            $numInsert = 0;
            $numActive = 0;
            $arrCheckCode = array();
            $insertArr = array();
            $updateArr = array();
            foreach ($arrReport as $k => $val){
                //System::debug($val,1,1);
                if(strlen($val['code'])==9 && is_numeric($val['code'])){
                    #MSP check update hoac insert
                    $arrCheckCode[$val['code']] = array('code'=>$val['code'],'update'=>0);
                }else{
                    #The thi insert bien dong liên luc
                    $insertArr[] = "(".$promotion_id.",'".$val['promotion']."','".$val['code']."','".$val['timeCode']."','".$val['timeDone']."','".$val['phone']."','".$val['userName']."','".$val['email']."','".$val['shopName']."','".$val['partnerName']."')";
                    $numInsert++;
                }
                if($val['timeDone']!=''){
                    $numActive++;
                }
            }

            if(!empty($arrCheckCode)){
                $reCode = DB::query("SELECT * FROM ".T_VINAPLUS_DAILY_REPORT." WHERE code IN('".implode("','",array_keys($arrCheckCode))."')");
                if($reCode){
                    while ( $row = @mysqli_fetch_assoc ( $reCode ) ) {
                        if(isset($arrCheckCode[$row['code']])&&$row['timeDone']==''){
                            $arrCheckCode[$row['code']]['update'] = 1;
                        }
                    }
                }
            }
            foreach ($arrReport as $k => $val){
                if(strlen($val['code'])==9&&is_numeric($val['code'])){
                    #MSP check update hoac insert
                    if(isset($arrCheckCode[$val['code']])){
                        if($arrCheckCode[$val['code']]['update']==1){
                            DB::update(T_VINAPLUS_DAILY_REPORT,array('timeDone'=>$val['timeDone']),"code='".$val['code']."'");
                        }else{
                            $insertArr[] = "(".$promotion_id.",'".$val['promotion']."','".$val['code']."','".$val['timeCode']."','".$val['timeDone']."','".$val['phone']."','".$val['userName']."','".$val['email']."','".$val['shopName']."','".$val['partnerName']."')";
                            $numInsert++;
                        }
                    }
                }
            }
            if(!empty($insertArr)){
                $sql = "INSERT IGNORE INTO " . T_VINAPLUS_DAILY_REPORT . " (promotion_id,promotion_name,code,timeCode,timeDone,phone,userName,email,shopName,partnerName) VALUES " . implode(',', $insertArr);
                DB::query($sql);
            }
            $returnArr = array('numInsert'=>$numInsert,'numActice'=>$numActive);
        }
        return $returnArr;
    }

    static function insertReportV2($arrReport,$promotion_id=0,$rp = 0){
        $returnArr = array('numInsert'=>0,'numActice'=>0);
        if($promotion_id>0&&!empty($arrReport)){
            $numInsert = 0;
            $numActive = 0;
            $arrCheckCode = array();
            $insertArr = array();
            $ud = 1;
            if($rp == 0){
                $ud = 0;
            }
//            [0] => Array
//            (
//                [NgayNhanUuDai] => 2017-09-28T10:05:44.000Z
//                [TenHoiVien] => Trng Th Hong Hi
//                [SoDienThoai] => 0915758668
//                [NgaySuDungUuDai] => 2017-10-02T09:16:30.000Z
//                [TenCuaHang] => Tn Sn Nht - Phng khch hng Thng gia Quc ni
//                [DiaChiSuDung] => Khu cch ly Ga i Quc ni  Sn bay Quc t Tn Sn Nht, Qun Tn Bnh, Tp.H Ch Minh
//                [TinhTrang] => 1
//                [MaUuDai] => IB_RVQMO0347
//                [GlobalId] => 2597274
//            )
//            $timeMore = 3600*7;
            $timeMore = 0;
            foreach ($arrReport as $k => $val){
//                neu truyen tham so rp thi mac dinh la update het
                 $val['update'] = $rp;
//                 convert time
                $val['NgayNhanUuDai'] = date('Y-m-d H:i:s',strtotime($val['NgayNhanUuDai'] )+ $timeMore);
                $val['NgaySuDungUuDai'] = ($val['NgaySuDungUuDai']) ? date('Y-m-d H:i:s',strtotime($val['NgaySuDungUuDai'])+ $timeMore) : '';

                if(is_numeric($val['MaUuDai'])){
                    $arrCheckCode['the'][$val['GlobalId']] = $val;
                }
                else{
                    $val['update'] = 0;
                    $arrCheckCode['msp'][$val['MaUuDai']] = $val;
                }
            }

//            if(!empty($arrCheckCode) && $rp == 0){
            if(!empty($arrCheckCode)){

//                check rieng the va ma so phieu
                if(isset($arrCheckCode['msp'])){
                    $reCode = DB::query("SELECT * FROM ".T_VINAPLUS_DAILY_REPORT." WHERE code IN('".implode("','",array_keys($arrCheckCode['msp']))."')");
                    if($reCode){
                        while ( $row = @mysqli_fetch_assoc ( $reCode ) ) {
//                            System::debug($row,0,1);
                            if(isset($arrCheckCode['msp'][$row['code']])){
                                if($arrCheckCode['msp'][$row['code']]['NgayNhanUuDai'] == $row['timeCode']){
                                    /*if((($row['shopName']=='null'||$row['shopName']=='')&&$arrCheckCode[$row['code']]['shopName']!='null')||(($row['partnerName']=='null'||$row['partnerName']=='')&&$arrCheckCode[$row['code']]['partnerName']!='null')){
                                        DB::update(T_VINAPLUS_DAILY_REPORT,array('shopName'=>$arrCheckCode[$row['code']]['shopName'],'partnerName'=>$arrCheckCode[$row['code']]['partnerName'],'userName'=>$arrCheckCode[$row['code']]['userName'],'email'=>$arrCheckCode[$row['code']]['email']),"code='".$row['code']."'");
                                    }*/
                                    if ($rp == 0 && (($arrCheckCode['msp'][$row['code']]['NgaySuDungUuDai'] == '' && $row['timeDone'] == '0000-00-00 00:00:00') || ($arrCheckCode['msp'][$row['code']]['NgaySuDungUuDai'] == $row['timeDone']))) {
                                        unset($arrCheckCode['msp'][$row['code']]);
                                    } else {
                                        $arrCheckCode['msp'][$row['code']]['update'] = 1;
                                    }
                                }
                            }
                        }
                    }
                }
                if(isset($arrCheckCode['the']) && $rp == 0){
                    $reCode = DB::query("SELECT * FROM ".T_VINAPLUS_DAILY_REPORT." WHERE idVina IN('".implode("','",array_keys($arrCheckCode['the']))."')");
                    if($reCode){
                        while ( $row = @mysqli_fetch_assoc ( $reCode ) ) {
                            if (isset($arrCheckCode['the'][$row['idVina']])) {
                                unset($arrCheckCode['the'][$row['idVina']]);
                            }
                        }
                    }
                }
            }
//            System::debug($arrCheckCode,1,1);
            foreach ($arrCheckCode as $type=>$values){
                foreach ($values as $k=>$val){
                    if($val['update']==0){
                        $insertArr[] = "(".$promotion_id.",'".$val['TenCuaHang']."','".$val['MaUuDai']."','".$val['NgayNhanUuDai']."','".$val['NgaySuDungUuDai']."','".$val['SoDienThoai']."','".$val['TenHoiVien']."','".$val['DiaChiSuDung']."','".$val['TenCuaHang']."',".($val['NgaySuDungUuDai']==''?1:2).",'".$val['GlobalId']."')";
                        $numInsert++;
                        if($val['NgaySuDungUuDai']!=''){
                            $numActive++;
                        }
                    }else{
//                        $id = strtoupper($val['GlobalID']);
                        $dataUpdate = array(
                            'timeDone'=>$val['NgaySuDungUuDai'],
                            'shopName'=>$val['DiaChiSuDung'],
                            'partnerName'=>$val['TenCuaHang'],
                            'userName'=>$val['TenHoiVien'],
                            'idVina'=>$val['GlobalId'],
                            'status'=>2
                        );
                        if($type == 'the'){
                            DB::update(T_VINAPLUS_DAILY_REPORT,$dataUpdate,"idVina='".$val['GlobalId']."'");
                        }
                        else{
                            DB::update(T_VINAPLUS_DAILY_REPORT,$dataUpdate,"code='".$val['MaUuDai']."'");
                        }
                        $numActive++;
                    }
                }
            }
            if(!empty($insertArr)){
                $sql = "INSERT IGNORE INTO " . T_VINAPLUS_DAILY_REPORT . " (promotion_id,promotion_name,code,timeCode,timeDone,phone,userName,shopName,partnerName,status,idVina) VALUES " . implode(',', $insertArr);
                DB::query($sql);
            }
            $returnArr = array('numInsert'=>$numInsert,'numActice'=>$numActive);
        }
        return $returnArr;
    }


    static function login_vina_api($force = false){

        //login vao he thong cua vinaphone
        if(!isset($_SESSION['SESSION_VINA_NEW']) || !$_SESSION['SESSION_VINA_NEW'] || $force){
            $ss = self::login(VINA_U,VINA_P);
            if($ss){
                $_SESSION['SESSION_VINA_NEW'] = $ss;
            }
            else{
                return false;
            }
        }
    }
    static function login_vina_active($force = false){
        if(!isset($_SESSION['SESSION_VINA_ACTIVE_NEW']) || !$_SESSION['SESSION_VINA_ACTIVE_NEW'] || $force){
            $user = UserMerchant::$current->data;
            $accviplus = @json_decode($user['accviplus'],true);
            if(!isset($accviplus['userviplus']) || !isset($accviplus['passviplus'])){
                return false;
            }
            $ss = self::login($accviplus['userviplus'],$accviplus['passviplus'],'login_active');
            if($ss){
                $_SESSION['SESSION_VINA_ACTIVE_NEW'] = $ss;
            }
            else{
                return false;
            }
        }
    }
    static function login($u,$p,$act = 'login'){
        $urlApi = VINA_API_NEW.'APIForAppMobile/API_Xacthucnguoidung';
        $data = json_encode(array('Username' => $u,'Password'=> $p));
        $idLog = Coupon::insertLogApi($urlApi.' => '.$data,$act);

        $str = self::postCurl($urlApi,$data);


//            $str = substr($str,3,strlen($str));
        $response = self::jsonDecodeVina($str);
        Coupon::updateLogApi($idLog,$str);
//        $response = json_decode($response,true);
        if(isset($response['errorCode']) && $response['errorCode'] === 200){
            return isset($response['token']) ? $response['token'] : '';
        }
        else{
            return '';
        }
    }
    static function postCurl($url, $var){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $var,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

}