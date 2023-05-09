<?php
class casModel extends model
{

    /*
     * CURL请求封装
     * $url 请求地址字符串
     * $params POST请求参数
     * $method 请求方式,默认为post
     * $header 请求头参数
     * $ssl 请求协议是否https,默认是
     * @return 返回请求结果字符串
     */
    public function curl($url='',$params=[],$method='POST',$header=[],$ssl=true){
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$method);
        if(strtoupper($method)=='POST'){
            if(!empty($params)){
                curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
            }
        }
        if(!empty($header)){
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }
        if($ssl){
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        }
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /*
     * 检查用户是否注册过,若注册过则直接查询返回
     * */
    public function checkUser($account){
        $record = $this->dao->select('*')->from(TABLE_USER)
            ->where('account')->eq($account)
            ->limit('0,1')
            ->fetch();
        return $record;
    }

    /*
     *生成随机密码 ,pw_length 密码长度
     */
    function create_password($pw_length = 8)
    {
        $randpwd = '';
        for ($i = 0; $i < $pw_length; $i++) 
        {
            $randpwd .= chr(mt_rand(33, 126));
        }
        return $randpwd;
    }    

    /*
     * 插入用户数据
     * */
    public function regUser($data){
        $duser = new stdclass();
        $duser->realname = $data['realname'];
        $duser->nickname = $data['name'];
        $duser->email = $data['email'];
        $duser->mobile = $data['mobile'];
        $duser->account  = $data['account'];
        $duser->role = 'others';
        $duser->dept = $data['dept'];
        $duser->password = md5($this->create_password(12)); //随机生成12位密码长度
        $duser->gender   = 'm';

        $dusergroup = new stdclass();
        $dusergroup->account = $duser->account;
        $dusergroup->group = 10;
        $this->dao->insert(TABLE_USERGROUP)->data($dusergroup)->exec();
        return $this->dao->insert(TABLE_USER)->data($duser)->exec();
    }
}