<?php

class RSSController extends Controller {

  public function accessRules() {
    return array(
      array(
        'allow',
        'actions' => array('index', 'download'),
        'users' => array('*'),
        'ips' => $this->module->rss_auth_ips,
      ),
      array(
        'deny',
//        'actions' => array('admin', 'upload'),
        'users' => array('*'),
      ),
    );
  }

  public function filters() {
    return array(
      'accessControl',
    );
  }

  public function actionIndex() {
  //Yii::app()->cache->flush();
    header("Content-Type: application/xml; charset=utf-8");
    $nums = Yii::app()->request->getQuery('nums', 30) + 0;
    $this->render('index', array(
      'models' => HackTorrent::model()
          ->findAllBySql('SELECT * FROM hack_torrent WHERE rss=1 and status=1 ORDER BY created DESC LIMIT :nums ', array(':nums' => min($nums, 50)))
    ));
  }

  public function actionDownload() {
    $hash = Yii::app()->request->getQuery('hash');
    if (!$hash or strlen($hash) != 40) {
      throw new CHttpException(404, 'no such file');
    }
    $hash = strtolower($hash);
    try {
      $torrent = Bencode::decode(file_get_contents($this->module->torrent_save_path . '/' . $hash));
    } catch (Exception $e) {
      throw new CHttpException(404);
    }
    $torrent['announce'] = 'http://' . $this->module->hack_tracker_host . ':' . $this->module->hack_tracker_port . '/announce';
    $torrent['announce-list'] = array(
      array('http://' . $this->module->hack_tracker_host . ':' . $this->module->hack_tracker_port . '/announce'),
    );
    $torrent['comment'] = 'ChinaHDTV.ORG';
    $torrent['created by'] = 'CBox/1.1';
    $torrent['creation date'] = time() + 3600 + rand(1800, 3600);
	$torrent['encoding'] = 'UTF-8';
    $torrent = Bencode::encode($torrent);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $hash . '.torrent');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($torrent));
//    readfile();
    echo $torrent;
    Yii::app()->end();
  }

}