<?php

class DefaultController extends Controller {

  public $defaultAction = 'admin';

  public function accessRules() {
    return array(
      array(
        'allow',
        'actions' => array('admin', 'upload', 'download', 'import', 'list'),
        'roles' => array('administrator', 'editor'),
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

//  public function init() {
//    $allowed_ip = array('/192\.168\.[0-9.]*/', '/127\.0\.[0-9.]*/');
//    foreach ($allowed_ip as $allow) {
//      if (preg_match($allow, Yii::app()->request->getUserHostAddress())) {
//        return;
//      }
//    }
//    throw new CHttpException(404);
//  }

  public function actionAdmin() {
    //Yii::app()->db->createCommand('DELETE FROM `snatch` WHERE `last_access` < '.(time()-7200))->execute();
    $result = Yii::app()->db->createCommand()->select('count(`created`) as nums')->from('snatch')->queryRow();
    $this->render('admin', array('online' => $result));
  }

  public function actionUpload() {
    if (!Yii::app()->request->getIsPostRequest()) {
      throw new CHttpException(400);
    }
    $upload = CUploadedFile::getInstanceByName('torrent');
    if (!$upload) {
      throw new CHttpException(403, 'server reject');
    }
    if (!$upload->getHasError()) {
      $ctx = file_get_contents($upload->tempName, FILE_BINARY);
      try {
        $bdc = Bencode::decode($ctx);
        if (!isset($bdc['info']['private']) or $bdc['info']['private'] != 1) {
          throw new CHttpException(403, 'server reject');
        }
        $new_torrent = array('info' => $bdc['info']);
        $hash = sha1(Bencode::encode($bdc['info']), TRUE);
        file_put_contents($this->module->torrent_save_path . '/' . bin2hex($hash), Bencode::encode($new_torrent), LOCK_EX);
        unset($bdc);
        $site = -1;
        if (isset($new_torrent['info']['source'])) {
          $source = $new_torrent['info']['source'];
          if (strpos(strtolower($source), 'chdbits')) {
            $site = 0;
          }
          elseif (strpos(strtolower($source), 'hdwing')) {
            $site = 1;
          }
          elseif (strpos(strtolower($source), 'ttg')) {
            $site = 2;
          }
          else {
            throw new CHttpException(403, 'server reject');
          }
        }
        elseif (isset($new_torrent['info']['ttg_tag'])) {
          $site = 2;
        }
        else {
          throw new CHttpException(403, 'server reject');
        }
        unset($new_torrent);
        $hack_torrent = HackTorrent::model()->findByPk($hash);
        if (!$hack_torrent) {
          $hack_torrent = new HackTorrent();
          $hack_torrent->author = Yii::app()->user->getName();
          $hack_torrent->info_hash = $hash;
          $hack_torrent->created = time();
        }
        $hack_torrent->name = $upload->getName();
        $hack_torrent->site = $site;
        $hack_torrent->last_active = time();
        $hack_torrent->rss = Yii::app()->request->getPost('rss') ? 1 : 0;
        $hack_torrent->status = 1;
        $hack_torrent->save();
        $this->redirect(Yii::app()->createAbsoluteUrl('pt2bt/default/download', array('hash' => bin2hex($hash))));
      } catch (Exception $e) {
        throw new CHttpException(500, $e->getMessage());
      }
    }
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
    $filename = "[ChinaHDTV.ORG] " . rawurlencode($torrent['info']['name']);
    if (strpos(Yii::app()->request->userAgent, 'IE') !== FALSE) {
      $filename = rawurlencode("[ChinaHDTV.ORG] " . $torrent['info']['name']);
    }
    $torrent['announce'] = 'http://' . $this->module->hack_tracker_host . ':' . $this->module->hack_tracker_port . '/announce';
    $torrent['announce-list'] = array(
      array('http://121.14.98.151:9090/announce'),
      array('http://' . $this->module->hack_tracker_host . ':' . $this->module->hack_tracker_port . '/announce'),
      array('http://94.228.192.98/announce'),
      array('http://86.136.98.127:6969/announce'),
      array('http://85.17.189.130/announce'),
      array('http://85.17.42.17:7001/announce'),
      array('http://79.134.170.65:6969/announce'),
      array('http://98.126.87.194/announce'),
      array('http://bt.careland.com.cn:6969/announce'),
      array('http://bt2.careland.com.cn:6969/announce'),
      array('http://bt3.careland.com.cn:6969/announce'),
      array('http://ipv6.torrent.ubuntu.com:6969/announce'),
      array('http://ipv6.torrent.centos.org:6969/announce'),
      array('http://ipv6.tracker.finnix.org:6969/announce'),
      array('http://tracker.ipv6tracker.org/announce'),
      array('http://ipv6.tracker.harry.lu/announce'),
      array('http://tracker.ipv6tracker.ru/announce'),
      array('http://ipv6.54new.com:8080/announce'),
      array('http://wasabi-btte.shuim.net:23333/announce'),
      array('http://tracker1.wasabii.com.tw:6969/announce'),
      array('http://tracker2.wasabii.com.tw:6969/announce'),
      array('udp://9.rarbg.me:2710/announce'),
    );
    $torrent['comment'] = 'ChinaHDTV.ORG';
    $torrent['created by'] = 'CBox/1.1';
    $torrent['creation date'] = time() + 3600 + rand(1800, 3600);
    $torrent['encoding'] = 'UTF-8';
    $torrent = Bencode::encode($torrent);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '.torrent"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($torrent));
    echo $torrent;
    Yii::app()->end();
  }

  public function actionList() {
    $criteria = new CDbCriteria(array(
      'select' => array(
        'HEX(info_hash) as info_hash',
        'name',
        'created',
        'last_active',
        'site',
        'status',
        'completed',
        'seeder',
        'leacher',
        'uploaded',
        'downloaded',
      ),
//      'with'=>''
//      'condition' => 'status=:status',
//      'params' => array(':status' => 1),
    ));
    $dataProvider = new CActiveDataProvider('HackTorrent', array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => min(Yii::app()->request->getQuery('size', 50), 50),
        'pageVar' => 'page',
      ),
      'sort' => array(
        'defaultOrder' => array(
          'created' => CSort::SORT_DESC,
        )
      ),
    ));
    $this->render('list', array(
      'dataProvider' => $dataProvider,
    ));
  }
}