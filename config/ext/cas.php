<?php
$filter->cas->tokenlogin->get['ticket'] = 'reg::any';

// cas 的登录界面
$config->cas->loginUrl      = 'https://casdoor.xxx.com/cas/casbin/cas-php-app/login';

// cas 的 ticket 认证地址
$config->cas->authUrl       = 'https://casdoor.xxx.com/cas/casbin/cas-php-app/p3/serviceValidate'; 

// cas 的登录回调地址，修改其中的 www.xxx.com 为具体地址。
$config->cas->serviceUrl    = 'http://zentao.xxx.com/zentao/cas-tokenlogin.html'; 