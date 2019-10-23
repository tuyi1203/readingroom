<?php
/**
 * 女神来了模块定义
 *
 * @author 幻月科技
 * @url http://bbs.fmoons.com/
 */
if (!defined('IN_IA')) { exit('Access Denied');}
global $_W, $_GPC;
if(!$_W['isfounder']){
    message('无权访问!');
}

$op = empty($_GPC['op']) ? 'display' : $_GPC['op'];
load()->func('communication');
load()->func('file');
$cfg = $this->module['config'];
$setting = setting_load('site');
$id = isset($setting['site']['key']) ? $setting['site']['key'] : '0';
$onlyoauth = pdo_fetch("SELECT * FROM ".tablename('fm_photosvote_onlyoauth')." WHERE siteid = :siteid", array(':siteid' => $id));
if ($op == 'display') {
    $versionfile = IA_ROOT . '/addons/fm_photosvote/core/version.php';
    $updatedate = date('Y-m-d H:i', filemtime($versionfile));
    $version = FM_PHOTOSVOTE_VERSION;
} else if ($op == 'sys') {

} else if ($op == 'check') {

    set_time_limit(0);

    $version = defined('FM_PHOTOSVOTE_VERSION') ? FM_PHOTOSVOTE_VERSION : '1.0';
    $resp = ihttp_post(FM_PHOTOSVOTE_AUTH_URL, array(
        'type' => 'check',
        'ip' => $_W['clientip'],
        'hostip' => $_SERVER["SERVER_ADDR"],
        'id' => $id,
        'fmauthtoken' => $onlyoauth['fmauthtoken'],
        'oauthurl' => $_SERVER ['HTTP_HOST'],
        'version' => $version,
        'manual'=>1
    ));

    $templatefiles = "";
    $ft = "";
    $ret = @json_decode($resp['content'], true);

    if (is_array($ret)) {
      $templatefiles = "";
      $ft = "";
        if ($ret['result'] == 1) {
            $files = array();

            if (!empty($ret['files'])) {
                foreach ($ret['files'] as $file) {
                    $entry = IA_ROOT . "/addons/fm_photosvote/" . $file['path'];
                    if (!is_file($entry) || md5_file($entry) != $file['hash']) {

                        $files[] = array('path' => $file['path'], 'download' => 0);

                         if( is_file($entry) && strexists($entry, 'template/mobile') && strexists($entry, '.html') ){
                              $templatefiles.= "M   /addons/fm_photosvote/".$file['path']."\r\n";
                         }else{
                            $ft .= "F   /addons/fm_photosvote/".$file['path']."\r\n";
                         }
                    }
                }
            }
           cache_write('cloud:modules:upgrade', array('files'=>$files,'version'=>$ret['version'],'uptime'=>$ret['uptime'],'upgrade'=>$ret['upgrade']));
           $log = base64_decode($ret['log']);
           if(!empty($templatefiles)){
               $upfile ="<br/><b>模板变化:</b><br/>".$templatefiles."\r\n";
           }
           $upfile = "<br/><b>文件变化:</b><br/>".$ft."\r\n".$upfile;
           $fmdata = array(
               'result' => 1,
                'version' => $ret['version'],
                'uptime' => $ret['uptime'],
                'filecount' => count($files),
                'upgrade' => !empty($ret['upgrade']),
                'log' =>  str_replace("\r\n","<br/>", $log),
                'upfile' =>  str_replace("\r\n","<br/>", $upfile),
            );
            echo json_encode($fmdata);
            exit;
        }
    }
    die(json_encode(array('result' => 0, 'message' =>$ret['m'])));
} else if ($op == 'download') {

    $upgrade = cache_load('cloud:modules:upgrade');

    $files = $upgrade['files'];
    $version = $upgrade['version'];
    $uptime = $upgrade['uptime'];
    $path = "";
    foreach ($files as $f) {
        if (empty($f['download'])) {
            $path = $f['path'];
            break;
        }
    }
    if (!empty($path)) {
        $resp = ihttp_post(FM_PHOTOSVOTE_AUTH_URL, array(
        'type' => 'download',
        'ip' => $_W['clientip'],
        'hostip' => $_SERVER["SERVER_ADDR"],
        'id' => $id,
        'fmauthtoken' => $onlyoauth['fmauthtoken'],
        'oauthurl' => $_SERVER ['HTTP_HOST'],
        'path' => $path
    ));

        $ret = @json_decode($resp['content'], true);

        if (is_array($ret)) {
            $path = $ret['path'];
            $dirpath = dirname($path);

            if (!is_dir(IA_ROOT . "/addons/fm_photosvote/" . $dirpath)) {
               mkdirs(IA_ROOT . "/addons/fm_photosvote/" . $dirpath, "0777");
            }

            $content = base64_decode($ret['content']);

            file_put_contents(IA_ROOT . "/addons/fm_photosvote/" . $path, $content);
               if(isset($ret['path1'])) {
                    $path1 = $ret['path1'];
                    $dirpath1 = dirname($path1);
                    if (!is_dir(IA_ROOT . "/addons/fm_photosvote/" . $dirpath1)) {
                        mkdirs(IA_ROOT . "/addons/fm_photosvote/" . $dirpath1, "0777");
                    }
                    $content1 = base64_decode($ret['content1']);
                    file_put_contents(IA_ROOT . "/addons/fm_photosvote/" . $path1, $content1);
               }

            $success = 0;
            foreach ($files as &$f) {
                if ($f['path'] == $path) {
                    $f['download'] = 1;
                    break;
                }
                if ($f['download']) {
                    $success++;
                }
            }

            unset($f);
            cache_write('cloud:modules:upgrade', array('files'=>$files,'version'=>$version,'uptime'=>$ret['uptime'],'upgrade'=>$upgrade['upgrade']));
            $fmdata = array(
               'result' => 1,
                'version' => $ret['version'],
                'total' => count($files),
                'path' => 'F   /addons/fm_photosvote' . $path,
                'success' =>  $success
            );
            echo json_encode($fmdata);
            exit;
        }
    } else {
        if (!empty($upgrade['upgrade'])) {
            $updatefile = IA_ROOT . "/addons/fm_photosvote/upgrade.php";
            file_put_contents($updatefile, base64_decode($upgrade['upgrade']));
            require $updatefile;
            @unlink($updatefile);
        }
        $ifile = IA_ROOT . "/addons/fm_photosvote/install.php";
        $upfile = IA_ROOT . "/addons/fm_photosvote/upgrade.php";
        $mm_1 = IA_ROOT . "/addons/fm_photosvote/core/fmmobile/jifendh.php";
        $mm_2 = IA_ROOT . "/addons/fm_photosvote/core/mtemplate/inde.php";
        $mm_3 = IA_ROOT . "/addons/fm_photosvote/core/plugin/jiyan/lib/index.php";
        $manifest = IA_ROOT . "/addons/fm_photosvote/manifest.xml";
        $tuser = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylebase/tuser.html";
        $tuserphotos = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylebase/tuserphotos.html";
        $reg = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylebase/reg.html";
        $tvote = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylebase/tvote.html";
        $tvote = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylebase/tvote.html";
        $tvote = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylebase/tvote.html";
        $tvote = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylebase/tvote.html";
        $stylemb1 = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylemb1/tvote.html";
        $stylemb3 = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylemb3/tvote.html";
        $stylemb4 = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylemb4/tvote.html";
        $stylemb5 = IA_ROOT . "/addons/fm_photosvote/template/mobile/templates/stylemb5/tvote.html";
        if (is_file($ifile)) {@unlink($ifile);}
        if (is_file($upfile)) {@unlink($upfile);}
        if (is_file($mm_1)) {@unlink($mm_1);}
        if (is_file($mm_2)) {@unlink($mm_2);}
        if (is_file($mm_3)) {@unlink($mm_3);}
        if (is_file($manifest)) {@unlink($manifest);}
        if (is_file($tuser)) {@unlink($tuser);}
        if (is_file($tuserphotos)) {@unlink($tuserphotos);}
        if (is_file($reg)) {@unlink($reg);}
        if (is_file($tvote)) {@unlink($tvote);}
        if (is_file($stylemb1)) {@unlink($stylemb1);}
        if (is_file($stylemb3)) {@unlink($stylemb3);}
        if (is_file($stylemb4)) {@unlink($stylemb4);}
        if (is_file($stylemb5)) {@unlink($stylemb5);}
        load()->func('file');
        @rmdirs(IA_ROOT . "/addons/fm_photosvote/tmp");
        file_put_contents(IA_ROOT . "/addons/fm_photosvote/core/version.php", "<?php if(!defined('IN_IA')) {exit('Access Denied');}if(!defined('FM_PHOTOSVOTE_VERSION')) {define('FM_PHOTOSVOTE_VERSION', '" . $upgrade['version'] . "');}if(!defined('FM_PHOTOSVOTE_TIME')) {define('FM_PHOTOSVOTE_TIME', '" . $upgrade['uptime'] . "');}");
        cache_delete('cloud:modules:upgrade');
        $time = time();

        die(json_encode(array('result' => 2)));
    }
} else if ($op == 'checkversion') {

    file_put_contents(IA_ROOT . "/addons/fm_photosvote/core/version.php", "<?php if(!defined('IN_IA')) {exit('Access Denied');}if(!defined('FM_PHOTOSVOTE_VERSION')) {define('FM_PHOTOSVOTE_VERSION', '1.0');}");
    header('location: '.$this->createWebUrl('upgrade'));
    exit;

}
include $this->template('web/upgrade');
