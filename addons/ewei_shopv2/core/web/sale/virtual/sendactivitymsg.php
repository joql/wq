<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Sendactivitymsg_EweiShopV2Page extends ComWebPage
{
	//构造函数用于检测权限 暂时忽略使用优惠券权限验证
	public function __construct($_com = 'coupon')
	{
		parent::__construct($_com);
	}

	public function main()
	{
		global $_W;
		global $_GPC;
//		$couponid = intval($_GPC['couponid']);
//		$coupon = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_coupon') . ' WHERE id=:id and uniacid=:uniacid and merchid=0', array(':id' => $couponid, ':uniacid' => $_W['uniacid']));
//		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member_level') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY level asc');
//		$list2 = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member_group') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY id asc');
//		$coupons = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_coupon') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY id asc');
//		$hascommission = false;
//		$plugin_com = p('commission');
//
//		if ($plugin_com) {
//			$plugin_com_set = $plugin_com->getSet();
//			$hascommission = !empty($plugin_com_set['level']);
//		}
//
//		$hasglobonus = false;
//		$plugin_globonus = p('globonus');
//
//		if ($plugin_globonus) {
//			$plugin_globonus_set = $plugin_globonus->getSet();
//			$hasglobonus = !empty($plugin_globonus_set['open']);
//		}
//
//		$hasabonus = false;
//		$plugin_abonus = p('abonus');
//
//		if ($plugin_abonus) {
//			$plugin_abonus_set = $plugin_abonus->getSet();
//			$hasabonus = !empty($plugin_abonus_set['open']);
//		}
//
//		if ($hascommission) {
//			$list3 = $plugin_com->getLevels();
//		}
//
//		if ($hasglobonus) {
//			$list4 = $plugin_globonus->getLevels();
//		}
//
//		if ($hasabonus) {
//			$list5 = $plugin_abonus->getLevels();
//		}

		$data = m('common')->getPluginset('activitymsg');
		m('common')->updatePluginset(array('activitymsg' => $data));

		load()->func('tpl');
		include $this->template();
	}

    /**
     * use for:保存
     * auth: Joql
     * date:2018-12-06 12:06
     */
	public function fetch()
	{
		global $_W;
		global $_GPC;
		$reject_count = 3; //活动推送拒收次数

		$class1 = $_GPC['send1'];
		if ($class1 == 1) {
            //指定用户推送
			$send_openid = $_GPC['send_openid'];
			$openids = explode(',', $send_openid);
			$plog = '活动消息推送方式: 指定 OPENID 人数: ' . count($openids);
		}
		else if ($class1 == 2) {
			//客服聊天方式推送
			$where = '';

			if (!empty($_GPC['send_level'])) {
				$where .= ' and level =' . intval($_GPC['send_level']);
			}

			$members = pdo_fetchall('SELECT openid FROM ' . tablename('ewei_shop_member') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\'' . $where, array(), 'openid');

			if (!empty($_GPC['send_level'])) {
				$levelname = pdo_fetchcolumn('select levelname from ' . tablename('ewei_shop_member_level') . ' where id=:id limit 1', array(':id' => $_GPC['send_level']));
			}
			else {
				$levelname = '全部';
			}

			$openids = array_keys($members);
			$plog = '发放优惠券 ID: ' . $couponid . ' 方式: 等级-' . $levelname . ' 人数: ' . count($members);
		}
		else if ($class1 == 3) {
			$where = '';

			if (!empty($_GPC['send_group'])) {
				$where .= ' and groupid =' . intval($_GPC['send_group']);
			}

			$members = pdo_fetchall('SELECT openid FROM ' . tablename('ewei_shop_member') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\'' . $where, array(), 'openid');

			if (!empty($_GPC['send_group'])) {
				$groupname = pdo_fetchcolumn('select groupname from ' . tablename('ewei_shop_member_group') . ' where id=:id limit 1', array(':id' => $_GPC['send_group']));
			}
			else {
				$groupname = '全部分组';
			}

			$openids = array_keys($members);
			$plog = '发放优惠券 ID: ' . $couponid . '  方式: 分组-' . $groupname . ' 人数: ' . count($members);
		}
		else if ($class1 == 4) {
			//全部推送
			$where = '';
			$members = pdo_fetchall(
				'SELECT m.openid FROM '
				. tablename('mc_mapping_fans') . 'm '
				.'left join '.tablename('activity_msg_reject').' amr on amr.openid=m.openid'
				.' WHERE ( amr.count is null or amr.count >='.$reject_count.') and m.follow=1 and m.uniacid = \'' . $_W['uniacid'] . '\'', array(), 'openid');
			pdo_update('activity_msg_reject', 'count=count+1','count<'.$reject_count);
			$openids = array_keys($members);
			$plog = '活动消息推送方式: 全部会员 人数: ' . count($members);
		}
		else if ($class1 == 5) {
			$where = '';
			if (!empty($_GPC['send_agentlevel']) || ($_GPC['send_partnerlevels'] === '0')) {
				$where .= ' and agentlevel =' . intval($_GPC['send_agentlevel']);
			}

			$members = pdo_fetchall('SELECT openid FROM ' . tablename('ewei_shop_member') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' and isagent=1 and status=1 ' . $where, array(), 'openid');

			if ($_GPC['send_agentlevel'] != '') {
				$levelname = pdo_fetchcolumn('select levelname from ' . tablename('ewei_shop_commission_level') . ' where id=:id limit 1', array(':id' => $_GPC['send_agentlevel']));
			}
			else {
				$levelname = '全部';
			}

			$openids = array_keys($members);
			$plog = '发放优惠券 ID: ' . $couponid . '  方式: 分销商-' . $levelname . ' 人数: ' . count($members);
		}
		else if ($class1 == 6) {
			$where = '';
			if (!empty($_GPC['send_partnerlevels']) || ($_GPC['send_partnerlevels'] === '0')) {
				$where .= ' and partnerlevel =' . intval($_GPC['send_partnerlevels']);
			}

			$members = pdo_fetchall('SELECT openid FROM ' . tablename('ewei_shop_member') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' and ispartner=1 and partnerstatus=1 ' . $where, array(), 'openid');

			if ($_GPC['send_partnerlevels'] != '') {
				$levelname = pdo_fetchcolumn('select levelname from ' . tablename('ewei_shop_globonus_level') . ' where id=:id limit 1', array(':id' => $_GPC['send_partnerlevels']));
			}
			else {
				$levelname = '全部';
			}

			$openids = array_keys($members);
			$plog = '发放优惠券 ID: ' . $couponid . '  方式: 股东-' . $levelname . ' 人数: ' . count($members);
		}
		else {
			if ($class1 == 7) {
				$where = '';
				if (!empty($_GPC['send_aagentlevels']) || ($_GPC['send_aagentlevels'] === '0')) {
					$where .= ' and aagentlevel =' . intval($_GPC['send_aagentlevels']);
				}

				$members = pdo_fetchall('SELECT openid FROM ' . tablename('ewei_shop_member') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' and isaagent=1 and aagentstatus=1 ' . $where, array(), 'openid');

				if ($_GPC['send_aagentlevels'] != '') {
					$levelname = pdo_fetchcolumn('select levelname from ' . tablename('ewei_shop_abonus_level') . ' where id=:id limit 1', array(':id' => $_GPC['send_aagentlevels']));
				}
				else {
					$levelname = '全部';
				}

				$openids = array_keys($members);
				$plog = '发放优惠券 ID: ' . $couponid . '  方式: 区域代理-' . $levelname . ' 人数: ' . count($members);
			}
		}

		$mopenids = array();

		foreach ($openids as $openid) {
			$mopenids[] = '\'' . str_replace('\'', '\'\'', $openid) . '\'';
		}

		if (empty($mopenids)) {
			show_json(0, '未找到发送的会员!');
		}

		$members = pdo_fetchall('select id,openid,nickname from ' . tablename('ewei_shop_member') . ' where openid in (' . implode(',', $mopenids) . ') and uniacid=' . $_W['uniacid']);

		if (empty($members)) {
			show_json(0, '未找到发送的会员!');
		}


		$data = array('sendtemplateid' => $_GPC['sendtemplateid'], 'frist' => $_GPC['frist'], 'fristcolor' => $_GPC['fristcolor'], 'keyword1' => $_GPC['keyword1'], 'keyword1color' => $_GPC['keyword1color'], 'keyword2' => $_GPC['keyword2'], 'keyword2color' => $_GPC['keyword2color'], 'remark' => $_GPC['remark'], 'remarkcolor' => $_GPC['remarkcolor'], 'templateurl' => $_GPC['templateurl'], 'custitle' => $_GPC['custitle'], 'custhumb' => $_GPC['custhumb'], 'cusdesc' => $_GPC['cusdesc'], 'cusurl' => $_GPC['cusurl']);
		m('common')->updatePluginset(array('activitymsg' => $data));

		show_json(1, array('openids' => $openids));
	}

	public function sendmessage()
	{
		global $_GPC;
		global $_W;
		$openid = $_GPC['openid'];
		$messagetype = intval($_GPC['messagetype']);

		$data = m('common')->getPluginset('activitymsg');

		if (empty($messagetype)) {
			exit(json_encode(array('result' => 0)));
		}
		else if ($messagetype == 1) {
			if (empty($data['sendtemplateid'])) {
				exit(json_encode(array('result' => 0, 'mesage' => '未指设定发送模板!', 'openid' => $openid)));
			}

			if (empty($openid)) {
				exit(json_encode(array('result' => 0, 'mesage' => '未指定openid!', 'openid' => $openid)));
			}

			$msg = array(
				'first'  => array('value' => $data['frist'], 'color' => $data['fristcolor']),
				'remark' => array('value' => $data['remark'], 'color' => $data['remarkcolor'])
				);
			$msg['keyword1'] = array('value' => $data['keyword1'], 'color' => $data['keyword1color']);
			$msg['keyword2'] = array('value' => date('Y-m-d H:i:s', TIMESTAMP), 'color' => $data['keyword2color']);

			if (empty($data['templateurl'])) {

				//$data['templateurl'] = mobileUrl('sale/coupon/my', NULL, true);
				$data['templateurl'] = '';
			}

			$result = m('message')->sendTplNotice($openid, $data['sendtemplateid'], $msg, $data['templateurl']);

			if (is_error($result)) {
				exit(json_encode(array('result' => 0, 'message' => $result['message'], 'openid' => $openid)));
			}

			exit(json_encode(array('result' => 1)));
		}
		else if ($messagetype == 2) {
			if (empty($openid)) {
				exit(json_encode(array('result' => 0, 'mesage' => '未指定openid!', 'openid' => $openid)));
			}

			if (empty($data['cusurl'])) {
				//$data['cusurl'] = mobileUrl('sale/coupon/my', NULL, true);
				$data['cusurl'] = '';
			}

			$resp = $this->sendNews($openid, $data['custitle'], $data['cusdesc'], $data['cusurl'], $data['custhumb']);

			if (is_error($resp)) {
				exit(json_encode(array('result' => 0, 'message' => $resp['message'], 'openid' => $openid)));
			}

			exit(json_encode(array('result' => 1)));
		}
		else {
			exit(json_encode(array('result' => 0)));
		}
	}

	public function sendNews($openid, $title, $desc, $url, $picurl, $account = NULL)
	{
		global $_W;
		$result = false;
		$articles[] = array('title' => urlencode($title), 'description' => urlencode($desc), 'url' => $url, 'picurl' => tomedia($picurl));
		$result = m('message')->sendNews($openid, $articles, $account);
		return $result;
	}
}

?>
