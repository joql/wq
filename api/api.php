<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/26
 * Time: 9:35
 */

require '../framework/bootstrap.inc.php';
load()->model('reply');
load()->model('attachment');
load()->app('common');
load()->classs('wesession');
load()->func('logging');
$query = $_GET;
if(is_array($query)){

    //hash=lKoKHZmu&act=test
    $get = $post = array();
    //检测请求合法性
    parse_str(authcode($query['code'], 'DECODE', 'key'), $get);
    if(empty($get)) {
        returnAjax(0,'非法请求');
    }
    //匹配公众号
    $hash = $get['hash'];
    if(!empty($hash)) {
        $id = pdo_fetchcolumn("SELECT acid FROM " . tablename('account') . " WHERE hash = :hash", array(':hash' => $hash));
        if(empty($id)){
            returnAjax(0,'公众号匹配失败');
        }
    }else{
        returnAjax(0,'公众号匹配失败');
    }
    if (!empty($id)) {
        $uniacid = pdo_getcolumn('account', array('acid' => $id), 'uniacid');
        $_W['account'] = uni_fetch($uniacid);
    }
    if(empty($_W['account'])) {
        returnAjax(0,'公众号匹配失败');
    }
    if(empty($_W['account']['token'])) {
        returnAjax(0,'公众号缺少token');
    }

    //初始化信息
    $post = $_POST;
    $_W['debug'] = intval($_GPC['debug']);
    $_W['acid'] = $_W['account']['acid'];
    $_W['uniacid'] = $_W['account']['uniacid'];
    $_W['uniaccount'] = uni_fetch($_W['uniacid']);
    $_W['account']['groupid'] = $_W['uniaccount']['groupid'];
    $_W['account']['qrcode'] = $_W['attachurl'].'qrcode_'.$_W['acid'].'.jpg?time='.$_W['timestamp'];
    $_W['account']['avatar'] = $_W['attachurl'].'headimg_'.$_W['acid'].'.jpg?time='.$_W['timestamp'];
    $_W['attachurl'] = attachment_set_attach_url();

    if(in_array($get['act'], array('test',
        'getAccessToken',
        'refreshAccessToken',
        'sendMassNotice'))) {
        $api = new api();
        $api->$get['act']($get, $post);
    } else {
        returnAjax(0,'非法请求');
    }
}

class api {
    function __construct(){
        global $_W;
        $this->account = WeAccount::create($_W['account']);
    }
    //test
    function test($get,$post){
        returnAjax(1,'success');
    }
    //获取token
    function getAccessToken($get,$post){
        $token = $this->account->getAccessToken();
        if(is_array($token)){
            returnAjax(0,'token获取失败');
        }else{
            returnAjax(1,'success',['token'=>$token]);
        }
    }
    //刷新token
    function refreshAccessToken($get,$post){
        logging_run($this->account->clearAccessToken(), 'trace', 'accessToken');
        $token = $this->account->getAccessToken();
        if(is_array($token)){
            returnAjax(0,'token刷新失败');
        }else{
            returnAjax(1,'success',['token'=>$token]);
        }
    }
    //群发模版消息
    function sendMassNotice($get,$post){
//        switch ($post['type']){
//            case 'all':
//                $user_info = pdo_fetchall("select openid from".tablename('mc_mapping_fans'));
//            case 'one':
//                $user_info[]['openid'] = $post['oid'];
//        }

        $count_ok =0;
        $count_fail =0;
        $fail_arr = [];

//        $post_data = array();
//        $post_data['first'] = array('value'=>$post['first'],'color'=>'#000000');
//        $post_data['keyword1'] = array('value'=>$post['keyword1'],'color'=>'#000000');
//        $post_data['keyword2'] = array('value'=>$post['keyword2'],'color'=>'#000000');
//        $post_data['remark'] = array('value'=>$post['remark'],'color'=>'#21a6df');
//        foreach (json_decode($post['oids'], true) as $v){
//            $result = $this->account->sendTplNotice($v,'-3neYnbrcgj1VSiuxVFfU5GtF866qr1dZfAMzmuK13c',$post_data,$post['url']);
//            if(is_error($result)){
//                $count_fail ++;
//                $fail_arr[] = [
//                    'openid' => $v,
//                    'result' => $result,
//                ];
//            }else{
//                $count_ok ++;
//            }
//        }
//
//        returnAjax(1,'success',[
//            'ok'=>$count_ok,
//            'fail'=>$count_fail,
//            'fail_detail'=>$fail_arr,
//        ]);
        //测试
        $text = "维达特惠装卷纸 700克/提 10卷装\n\n特价9.3元，仅剩八提，支持预定，支持配送至宿舍楼下（女生可配送至宿舍门口）";
        $post_data = array();
        $post_data['first'] = array('value'=>'同学，打扰你一会时间哦','color'=>'#000000');
        $post_data['keyword1'] = array('value'=>'今日促销卫生纸活动','color'=>'#000000');
        $post_data['keyword2'] = array('value'=>date('Y-m-d H:i:s', time()),'color'=>'#000000');
        $post_data['remark'] = array('value'=>$text,'color'=>'#21a6df');
        $user_info['openid'] = 'otdSc5n4uPzsU9Rd8LpaI6RDUMAg'; //debugger
        $result = $this->account->sendTplNotice(
            $user_info['openid']
            ,'-3neYnbrcgj1VSiuxVFfU5GtF866qr1dZfAMzmuK13c'
            ,$post_data
            ,$post['url']
        );

        if(is_error($result)){
            returnAjax(0,$result['message']);
        }else{
            returnAjax(1,'success');
        }
    }
}

// ajax返回
function returnAjax($code, $msg = '', $data = array()){
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode(array('code' => $code, 'data' => $data, 'msg' => $msg)));
}
