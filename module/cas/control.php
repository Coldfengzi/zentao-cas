<?php

class cas extends control
{
    public function login(){
	$tid = $_GET['tid'];
        $gotoUrl = $this->config->cas->loginUrl . "?service=" . urlencode($this->config->cas->serviceUrl.'?tid='.$tid);
        $this->locate($gotoUrl);
    }

    public function tokenlogin(){
        $ticket = $_GET['ticket'];
	$tid = $_GET['tid'];
        if (!$ticket) {
            echo "缺少参数!";
            return;
        }

        $gotoUrl = $this->config->cas->authUrl . "?service=" . urlencode($this->config->cas->serviceUrl.'?tid='.$tid) . "&ticket=" . $ticket. "&format=json";
		//echo $gotoUrl;
		//return;		
        $output = $this->cas->curl($gotoUrl, [], 'GET');
        $validateXML = json_decode($output,true);
        if ($validateXML["authenticationFailure"]) {
            echo "验证失败！";
            return;
        }

		//echo '<pre>';
		//print_r($validateXML);
		//return;
		/* 构建信息数据 */        	
		$data = [];
		
        $account = $validateXML['Success']['User'];
        $data['account'] = $account;
		
        $arrAttributes = $validateXML['Success']['Attributes']['UserAttributes']['Attributes'];            
        foreach ($arrAttributes as $key => $value ){
            // displayName
            if($value['Name'] == 'displayName'){
                $data['realname'] = $value['Value'];
            }
            elseif($value['Name'] == 'name'){
                $data['name'] = $value['Value'];
            }
            elseif($value['Name'] == 'phone'){
                $data['mobile'] = $value['Value'];
            }
            elseif($value['Name'] == 'email'){
                $data['email'] = $value['Value'];
            }
        }

			


        //print_r($data);    
        //return;
        
        if($this->cas->checkUser($account)) {
         } else {
             $this->cas->regUser($data);

         }

        $user = $this->cas->checkUser($account);
        //print_r($user);    
        //return;		
        if($user){
            $account = $user->account;
            $this->user = $this->loadModel('user');

            $this->user->cleanLocked($account);
            $user->rights = $this->user->authorize($account);
            $user->groups = $this->user->getGroups($account);
            $this->session->set('user', $user);
            $this->app->user = $this->session->user;
            $this->loadModel('action')->create('user', $user->id, 'login');
            $this->loadModel('score')->create('user', 'login');
            /* Keep login. */
            if($this->post->keepLogin) $this->user->keepLogin($user);
	    echo js::locate($this->createLink($this->config->default->module), 'parent');
	    return;
            //die(js::locate($this->createLink($this->config->default->module), 'parent'));
        }else{
            echo "登录失败,用户不存在!";
        }
    }


    public function logout(){
        echo "logout";
    }

}
