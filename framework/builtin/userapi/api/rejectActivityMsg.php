<?php


if(empty($this->message['from'])){
    return $this->respText('服务器异常，屏蔽失败，要不过一会再试试?');
}
$exist = pdo_fetch('SELECT id FROM ' . tablename('activity_msg_reject') . ' WHERE `openid`=:openid'
    , array(':openid' => $this->message['from'])
);
if(empty($exist)){
    pdo_insert('activity_msg_reject',array(
        'openid' => $this->message['from']
    ,'time' => time()
    ));
} else{
    pdo_update('activity_msg_reject', array(
        'count' => 0
        ,'time' => time()
    ), array('id' => $exist['id']));
}
return $this->respText('拒收成功，未来一段时间内店小二不会再打扰您喽。(　˙灬˙　)');

