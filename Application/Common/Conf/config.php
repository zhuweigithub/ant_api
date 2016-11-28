<?php
return array(
	//'配置项'=>'配置值'
    "LOAD_EXT_FILE"=>"fb",
    'APP_GROUP_LIST'=>'Home,Admin',
    'DEFAULT_GROUP'=>'Home',//默认分组

    'TMPL_L_DELIM'=>'<{',
    'TMPL_R_DELIM'=>'}>',

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
	'LOAD_EXT_CONFIG'	=>'db,code',

	'SSS' => '在线状态才能发送消息',


    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符
    /* 模板相关配置 */
	'TMPL_PARSE_STRING'=> array(
		'__JS__' => '/Public/js',
		'__CSS__' =>'/Public/css',
		'__IMG__' =>'/Public/img',
		'__LIB__' =>'/Public/lib'
		//'__TEMPLATE__' => 'Public/Template',
	),

    /*加密字符串*/
    "PASSWORD_SUFFIX" => 'zw_info',
    "APP_ID" => 'wxd892d9377ca9cabc',
    "APP_SECRET" => '99c8d09540dfc387aa4c599fd92819d5',

);
