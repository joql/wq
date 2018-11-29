<?php
class Sale_EweiShopV2Model
{
	/**
     * 全返的自定文字
     * @param bool $echo 是否直接输出
     * @return string|void 不直接输出时返回文字
     * @author cunx
     */
	public function getFullBackText($echo = false)
	{
		$text = '全返';
		$set = m('common')->getSysset('fullback');

		if (!empty($set['text'])) {
			$text = $set['text'];
		}

		if ($echo) {
			echo $text;
			return NULL;
		}

		return $text;
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
