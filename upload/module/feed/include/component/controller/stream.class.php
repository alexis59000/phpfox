<?php

class Feed_Component_Controller_Stream extends Phpfox_Component {
	public function process() {
		if (($val = $this->request()->get('val'))) {
			Phpfox::isUser(true);

			// if (isset($aVals['user_status']) && ($iId = Phpfox::getService('user.process')->updateStatus($aVals)))


			$val['user_status'] = $val['content'];
			$id = Phpfox::getService('user.process')->updateStatus($val);

			Feed_Service_Feed::instance()->processAjax($id);

			echo Phpfox::getLib('ajax')->getData();
			exit;
		}

		$aFeed = Feed_Service_Feed::instance()->get(null, $this->request()->get('id'));

		header('Content-type: application/javascript');

		if (!isset($aFeed[0])) {
			echo ';__(' . json_encode([
					'url' => $this->url()->makeUrl('feed.stream', ['id' => $this->request()->get('id')]),
					'content' => false
				]) . ');';
			exit;
		}

		$this->template()->assign('aGlobalUser', (Phpfox::isUser() ? Phpfox::getUserBy(null) : array()));
		$this->template()->assign('aFeed', $aFeed[0]);
		$this->template()->getTemplate('feed.block.entry');

		echo ';__(' . json_encode([
				'url' => $this->url()->makeUrl('feed.stream', ['id' => $this->request()->get('id')]),
				'content' => ob_get_clean()
		]) . ');';
		exit;
	}
}