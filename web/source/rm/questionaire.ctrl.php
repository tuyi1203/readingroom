<?php
// error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require '../vendor/autoload.php';
// echo "asdfsdf";exit;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


defined('IN_IA') or exit('Access Denied');
uni_user_permission_check('site_rmxx_questionaire');

load()->func('communication');
load()->func('file');
$do = in_array($do, array('display', 'post', 'delete','output_excel','confirm')) ? $do : 'display';
$category = $category_copy = pdo_fetchall("SELECT id,parentid,name FROM ".tablename('site_category')." WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC, id ASC ", array(), 'id');
$parent = array();
$children = array();
$subchildren = [];
$xueli = [
  '1' => '研究生',
  '2' => '本科',
  '3' => '专科',
  '4' => '高中',
  '5' => '其他',
];

$gender = [
  '1' => '男',
  '2' => '女',
];

$relation = [
  '1' => '父亲',
  '2' => '母亲',
  '3' => '其他',
];

if (!empty($category)) {
	$children = '';
	foreach ($category as $cid => $cate) {
		if (!empty($cate['parentid'])) 
		{
			// $children[$cate['parentid']][] = $cate;
			if (!grandparentid_exists($cate['parentid'] , $category_copy))
			{
				$children[$cate['parentid']][] = $cate;
			}
			else
			{
				$subchildren[$cate['parentid']][] = $cate;
			}
		} 
		else 
		{
			$parent[$cate['id']] = $cate;
		}
	}
}

if($do == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = '';
	$params = array();
	if (!empty($_GPC['keyword'])) {
		$condition .= " student_name LIKE :keyword";
		$params[':keyword'] = "%{$_GPC['keyword']}%";
  }
  if (!empty($_GPC['daterange'])) {
    $date = explode(' - ', $_GPC['daterange']);
    $date_from = trim($date[0]);
    $date_to = trim($date[1]);
    $condition .= " and ( createtime between :date_from and :date_to )";
    $params[':date_from'] = $date_from;
    $params[':date_to'] = $date_to;
  }
	
	// if (!empty($_GPC['category']['childid'])) {
	// 	$cid = intval($_GPC['category']['childid']);
	// 	$condition .= " AND ccate = '{$cid}'";
	// } elseif (!empty($_GPC['category']['parentid'])) {
	// 	$cid = intval($_GPC['category']['parentid']);
	// 	$condition .= " AND pcate = '{$cid}'";
	// }

	if (!empty($condition)) {
		$condition = " WHERE 1=1 " . $condition;
	}
	
	$list = pdo_fetchall("SELECT * FROM ".tablename('jm_questionaire')."   $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
	// $list = pdo_fetchall("SELECT * FROM ".tablename('site_article')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY id DESC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('jm_questionaire') . $condition);
	$pager = pagination($total, $pindex, $psize);
	template('rm/questionaire');
}
elseif ($do == 'confirm') 
{
  
  $fieldList = [
    'id' => 'id',
    'openid' => 'openid',
    'nickname' => '昵称',
    'avatar' => '头像',
    'student_name' => '学生姓名',
    'gender' => '性别',
    'youeryuan' => '幼儿园',
    'birthday' => '生日',
    'addr' => '家庭住址',
    'p1_biye' => '毕业院校（家长一）',
    'p1_danwei' => '单位（家长一）',
    'p1_name' => '家长姓名（家长一）',
    'p1_relation' => '与学生关系（家长一）',
    'p1_tel' => '电话号码（家长一）',
    'p1_xueli' => '学历（家长一）',
    'p2_biye' => '毕业院校（家长二）',
    'p2_danwei' => '单位（家长二）',
    'p2_name' => '家长姓名（家长二）',
    'p2_relation' => '学生关系（家长二）',
    'p2_tel' => '电话号码（家长二）',
    'p2_xueli' => '学历（家长二）',
    'uniacid' => 'uniacid',
    'createtime' => '提交时间',
  ];
  $id = intval($_GPC['id']);
  $detail = pdo_fetch("SELECT * FROM ".tablename('jm_questionaire')." WHERE id = '{$id}' AND uniacid = '{$_W['uniacid']}'");
  $return = [];
  foreach ($fieldList as $k => $v) {
    $return[$v] = $detail[$k];
    if ($k == 'gender') {
      $return[$v] = $gender[$detail[$k]];
    }
    if ($k == 'p1_xueli' || $k == 'p2_xueli') {
      $return[$v] = $xueli[$detail[$k]];
    }
    if ($k == 'p1_relation' || $k == 'p2_relation') {
      $return[$v] = $relation[$detail[$k]];
    }
    if ($k == 'avatar') {
      $return[$v] = '<a href="javascript:void(0);" class="thumbnail"><img src="'. str_replace('132132','132', $detail[$k]) .'" alt=""></a>';
    }
  }
  echo json_encode($return);
  exit;
}
elseif ($do == 'output_excel')
{
	$spreadsheet = new Spreadsheet();
	$worksheet = $spreadsheet->getActiveSheet();
	$worksheet->setTitle('报名表');

	//表头
	//设置单元格内容
  // $worksheet->setCellValueByColumnAndRow(1, 1, '学生姓名');
  $worksheet->mergeCells('A1:E1');
  $worksheet->setCellValue('A1', '学生信息');
  $worksheet->setCellValue('A2', '学生姓名');
  $worksheet->setCellValue('B2', '学生性别');
  $worksheet->setCellValue('C2', '就读幼儿园');
  $worksheet->setCellValue('D2', '出生日期');
  $worksheet->setCellValue('E2', '家庭住址');
  $worksheet->mergeCells('F1:K1');
  $worksheet->setCellValue('F1', '家长一信息');
  $worksheet->setCellValue('F2', '姓名');
  $worksheet->setCellValue('G2', '与学生关系');
  $worksheet->setCellValue('H2', '学历');
  $worksheet->setCellValue('I2', '毕业院校');
  $worksheet->setCellValue('J2', '电话号码');
  $worksheet->setCellValue('K2', '工作单位');
  $worksheet->mergeCells('L1:Q1');
  $worksheet->setCellValue('L1', '家长二信息');
  $worksheet->setCellValue('L2', '姓名');
  $worksheet->setCellValue('M2', '与学生关系');
  $worksheet->setCellValue('N2', '学历');
  $worksheet->setCellValue('O2', '毕业院校');
  $worksheet->setCellValue('P2', '电话号码');
  $worksheet->setCellValue('Q2', '工作单位');




	$list = pdo_fetchall("SELECT * FROM ".tablename('jm_questionaire')."  ORDER BY id DESC ", null);

	foreach ($list as $key => $value) {
		$worksheet->setCellValueByColumnAndRow(1, $key + 3, $value['student_name']);
	}

	// print_r($list);

	$filename = '报名一览表.xlsx';
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	// header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Cache-Control: max-age=0');

	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');
	exit;
}
elseif($do == 'post') 
{
	load()->func('file');
	$id = intval($_GPC['id']);
  $template = uni_templates();
	$pcate = $_GPC['pcate'];
	$ccate = $_GPC['ccate'];
	$subchildid = $_GPC['subchildid'];
	if (!empty($id)) {
		$item = pdo_fetch("SELECT * FROM ".tablename('site_article')." WHERE id = :id" , array(':id' => $id));
		$item['type'] = explode(',', $item['type']);
		$pcate = $item['pcate'];
		$ccate = $item['ccate'];
		$subchildid = $item['subchildid'];
		if (empty($item)) {
			message('抱歉，文章不存在或是已经删除！', '', 'error');
		}
		$key = pdo_fetchall('SELECT content FROM ' . tablename('rule_keyword') . ' WHERE rid = :rid AND uniacid = :uniacid', array(':rid' => $item['rid'], ':uniacid' => $_W['uniacid']));
		if(!empty($key)) {
			$keywords = array();
			foreach($key as $row) {
				$keywords[] = $row['content'];
			}
			$keywords = implode(',', array_values($keywords));
		}
		$item['credit'] = iunserializer($item['credit']) ? iunserializer($item['credit']) : array();
		if(!empty($item['credit']['limit'])) {
						$credit_num = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('mc_handsel') . ' WHERE uniacid = :uniacid AND module = :module AND sign = :sign', array(':uniacid' => $_W['uniacid'], ':module' => 'article', ':sign' => md5(iserializer(array('id' => $id)))));
			if(is_null($credit_num)) $credit_num = 0;
			$credit_yu = (($item['credit']['limit'] - $credit_num) < 0) ? 0 : $item['credit']['limit'] - $credit_num;
		}
	} else {
		$item['credit'] = array();
	}
	if (checksubmit('submit')) {
		if (empty($_GPC['title'])) {
			message('标题不能为空，请输入标题！');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'iscommend' => intval($_GPC['option']['commend']),
			'ishot' => intval($_GPC['option']['hot']),
			'pcate' => intval($_GPC['category']['parentid']),
			'ccate' => intval($_GPC['category']['childid']),
			'template' => addslashes($_GPC['template']),
			'title' => addslashes($_GPC['title']),
			'description' => addslashes($_GPC['description']),
			'content' => htmlspecialchars_decode($_GPC['content'], ENT_QUOTES),
			'incontent' => intval($_GPC['incontent']),
			'source' => addslashes($_GPC['source']),
			'author' => addslashes($_GPC['author']),
			'displayorder' => intval($_GPC['displayorder']),
			'linkurl' => addslashes($_GPC['linkurl']),
			'createtime' => TIMESTAMP,
			'click' => intval($_GPC['click'])
		);
		// tuyi 2018.7.31 ↓↓↓↓↓↓↓↓↓↓↓↓↓
		if (!empty($_GPC['category']['subchildid']))
		{
			$data['subchildid'] = $_GPC['category']['subchildid'];
		}
		//↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
		if (!empty($_GPC['thumb'])) {
			$data['thumb'] = parse_path($_GPC['thumb']);
		} elseif (!empty($_GPC['autolitpic'])) {
			$match = array();
			$file_name = file_random_name(ATTACHMENT_ROOT.'images/'.$_W['uniacid'].'/'.date('Y/m').'/', 'jpg');
			$path = 'images/'.$_W['uniacid'].'/'.date('Y/m').'/'.$file_name;
			preg_match('/&lt;img.*?src=&quot;(.*?)&quot;/', $_GPC['content'], $match);
			if (!empty($match[1])) {
				$url = $match[1];
				$file = ihttp_request(tomedia($url));
				file_write($path, $file['content']);
				$data['thumb'] = $path;
				file_remote_upload($path);
			}
		} else {
			$data['thumb'] = '';
		}
		$keyword = str_replace('，', ',', trim($_GPC['keyword']));
		$keyword = explode(',', $keyword);
		if(!empty($keyword)) {
			$rule['uniacid'] = $_W['uniacid'];
			$rule['name'] = '文章：' . $_GPC['title'] . ' 触发规则';
			$rule['module'] = 'news';
			$rule['status'] = 1;
			$keywords = array();
			foreach($keyword as $key) {
				$key = trim($key);
				if(empty($key)) continue;
				$keywords[] = array(
					'uniacid' => $_W['uniacid'],
					'module' => 'news',
					'content' => $key,
					'status' => 1,
					'type' => 1,
					'displayorder' => 1,
				);
			}
			$reply['title'] = $_GPC['title'];
			$reply['description'] = $_GPC['description'];
			$reply['thumb'] = $data['thumb'];
			$reply['url'] = murl('site/site/detail', array('id' => $id));
		}
				if(!empty($_GPC['credit']['status'])) {
			$credit['status'] = intval($_GPC['credit']['status']);
			$credit['limit'] = intval($_GPC['credit']['limit']) ? intval($_GPC['credit']['limit']) : message('请设置积分上限');
			$credit['share'] = intval($_GPC['credit']['share']) ? intval($_GPC['credit']['share']) : message('请设置分享时赠送积分多少');
			$credit['click'] = intval($_GPC['credit']['click']) ? intval($_GPC['credit']['click']) : message('请设置阅读时赠送积分多少');
			$data['credit'] = iserializer($credit);
		} else {
			$data['credit'] = iserializer(array('status' => 0, 'limit' => 0, 'share' => 0, 'click' => 0));
		}	
		if (empty($id)) {
			if(!empty($keywords)) {
				pdo_insert('rule', $rule);
				$rid = pdo_insertid();
				foreach($keywords as $li) {
					$li['rid'] = $rid;
					pdo_insert('rule_keyword', $li);
				}
				$reply['rid'] = $rid;
				pdo_insert('news_reply', $reply);
				$data['rid'] = $rid;
			}
			pdo_insert('site_article', $data);
			$aid = pdo_insertid();
			pdo_update('news_reply', array('url' => murl('site/site/detail', array('id' => $aid))), array('rid' => $rid));
		} else {
			unset($data['createtime']);
			pdo_delete('rule', array('id' => $item['rid'], 'uniacid' => $_W['uniacid']));
			pdo_delete('rule_keyword', array('rid' => $item['rid'], 'uniacid' => $_W['uniacid']));
			pdo_delete('news_reply', array('rid' => $item['rid']));
			if(!empty($keywords)) {
				pdo_insert('rule', $rule);
				$rid = pdo_insertid();

				foreach($keywords as $li) {
					$li['rid'] = $rid;
					pdo_insert('rule_keyword', $li);
				}

				$reply['rid'] = $rid;
				pdo_insert('news_reply', $reply);
				$data['rid'] = $rid;
			} else {
				$data['rid'] = 0;
				$data['kid'] = 0;
			}
			pdo_update('site_article', $data, array('id' => $id));
		}
		message('文章更新成功！', url('site/article/display'), 'success');
	} else {
		template('site/article');
	}
} elseif($do == 'delete') {
	load()->func('file');
	$id = intval($_GPC['id']);
	$row = pdo_fetch("SELECT count(*) FROM ".tablename('jm_questionaire')." WHERE id = :id", array(':id' => $id));
	if (empty($row)) {
		message('抱歉，数据不存在或是已经被删除！');
	}
	if (!empty($row['thumb'])) {
		file_delete($row['thumb']);
	}
	if(!empty($row['rid'])) {
		pdo_delete('rule', array('id' => $row['rid'], 'uniacid' => $_W['uniacid']));
		pdo_delete('rule_keyword', array('rid' => $row['rid'], 'uniacid' => $_W['uniacid']));
		pdo_delete('news_reply', array('rid' => $row['rid']));
	}
	pdo_delete('jm_questionaire', array('id' => $id));
	message('删除成功！', referer(), 'success');
}


// 2018.7.31 tuyi
function grandparentid_exists($id , $loop)
{
	foreach ($loop as $k => $v) 
	{
		$parentid = $v['parentid'];
		if (($v['id'] == $id ) && !empty($parentid))
		{
			return true;
		}
	}
	return false;
}

// function exportExcel(array $datas, string $fileName = '', array $options = [])
// {
//     try {
//         if (empty($datas)) {
//             return false;
//         }

//         set_time_limit(0);
//         /** @var Spreadsheet $objSpreadsheet */
//         $objSpreadsheet = app(Spreadsheet::class);
//         /* 设置默认文字居左，上下居中 */
//         $styleArray = [
//             'alignment' => [
//                 'horizontal' => Alignment::HORIZONTAL_LEFT,
//                 'vertical'   => Alignment::VERTICAL_CENTER,
//             ],
//         ];
//         $objSpreadsheet->getDefaultStyle()->applyFromArray($styleArray);
//         /* 设置Excel Sheet */
//         $activeSheet = $objSpreadsheet->setActiveSheetIndex(0);

//         /* 打印设置 */
//         if (isset($options['print']) && $options['print']) {
//             /* 设置打印为A4效果 */
//             $activeSheet->getPageSetup()->setPaperSize(PageSetup:: PAPERSIZE_A4);
//             /* 设置打印时边距 */
//             $pValue = 1 / 2.54;
//             $activeSheet->getPageMargins()->setTop($pValue / 2);
//             $activeSheet->getPageMargins()->setBottom($pValue * 2);
//             $activeSheet->getPageMargins()->setLeft($pValue / 2);
//             $activeSheet->getPageMargins()->setRight($pValue / 2);
//         }

//         /* 行数据处理 */
//         foreach ($datas as $sKey => $sItem) {
//             /* 默认文本格式 */
//             $pDataType = DataType::TYPE_STRING;

//             /* 设置单元格格式 */
//             if (isset($options['format']) && !empty($options['format'])) {
//                 $colRow = Coordinate::coordinateFromString($sKey);

//                 /* 存在该列格式并且有特殊格式 */
//                 if (isset($options['format'][$colRow[0]]) &&
//                     NumberFormat::FORMAT_GENERAL != $options['format'][$colRow[0]]) {
//                     $activeSheet->getStyle($sKey)->getNumberFormat()
//                         ->setFormatCode($options['format'][$colRow[0]]);

//                     if (false !== strpos($options['format'][$colRow[0]], '0.00') &&
//                         is_numeric(str_replace(['￥', ','], '', $sItem))) {
//                         /* 数字格式转换为数字单元格 */
//                         $pDataType = DataType::TYPE_NUMERIC;
//                         $sItem     = str_replace(['￥', ','], '', $sItem);
//                     }
//                 } elseif (is_int($sItem)) {
//                     $pDataType = DataType::TYPE_NUMERIC;
//                 }
//             }

//             $activeSheet->setCellValueExplicit($sKey, $sItem, $pDataType);

//             /* 存在:形式的合并行列，列入A1:B2，则对应合并 */
//             if (false !== strstr($sKey, ":")) {
//                 $options['mergeCells'][$sKey] = $sKey;
//             }
//         }

//         unset($datas);

//         /* 设置锁定行 */
//         if (isset($options['freezePane']) && !empty($options['freezePane'])) {
//             $activeSheet->freezePane($options['freezePane']);
//             unset($options['freezePane']);
//         }

//         /* 设置宽度 */
//         if (isset($options['setWidth']) && !empty($options['setWidth'])) {
//             foreach ($options['setWidth'] as $swKey => $swItem) {
//                 $activeSheet->getColumnDimension($swKey)->setWidth($swItem);
//             }

//             unset($options['setWidth']);
//         }

//         /* 设置背景色 */
//         if (isset($options['setARGB']) && !empty($options['setARGB'])) {
//             foreach ($options['setARGB'] as $sItem) {
//                 $activeSheet->getStyle($sItem)
//                     ->getFill()->setFillType(Fill::FILL_SOLID)
//                     ->getStartColor()->setARGB(Color::COLOR_YELLOW);
//             }

//             unset($options['setARGB']);
//         }

//         /* 设置公式 */
//         if (isset($options['formula']) && !empty($options['formula'])) {
//             foreach ($options['formula'] as $fKey => $fItem) {
//                 $activeSheet->setCellValue($fKey, $fItem);
//             }

//             unset($options['formula']);
//         }

//         /* 合并行列处理 */
//         if (isset($options['mergeCells']) && !empty($options['mergeCells'])) {
//             $activeSheet->setMergeCells($options['mergeCells']);
//             unset($options['mergeCells']);
//         }

//         /* 设置居中 */
//         if (isset($options['alignCenter']) && !empty($options['alignCenter'])) {
//             $styleArray = [
//                 'alignment' => [
//                     'horizontal' => Alignment::HORIZONTAL_CENTER,
//                     'vertical'   => Alignment::VERTICAL_CENTER,
//                 ],
//             ];

//             foreach ($options['alignCenter'] as $acItem) {
//                 $activeSheet->getStyle($acItem)->applyFromArray($styleArray);
//             }

//             unset($options['alignCenter']);
//         }

//         /* 设置加粗 */
//         if (isset($options['bold']) && !empty($options['bold'])) {
//             foreach ($options['bold'] as $bItem) {
//                 $activeSheet->getStyle($bItem)->getFont()->setBold(true);
//             }

//             unset($options['bold']);
//         }

//         /* 设置单元格边框，整个表格设置即可，必须在数据填充后才可以获取到最大行列 */
//         if (isset($options['setBorder']) && $options['setBorder']) {
//             $border    = [
//                 'borders' => [
//                     'allBorders' => [
//                         'borderStyle' => Border::BORDER_THIN, // 设置border样式
//                         'color'       => ['argb' => 'FF000000'], // 设置border颜色
//                     ],
//                 ],
//             ];
//             $setBorder = 'A1:' . $activeSheet->getHighestColumn() . $activeSheet->getHighestRow();
//             $activeSheet->getStyle($setBorder)->applyFromArray($border);
//             unset($options['setBorder']);
//         }

//         $fileName = !empty($fileName) ? $fileName : (date('YmdHis') . '.xlsx');

//         if (!isset($options['savePath'])) {
//             /* 直接导出Excel，无需保存到本地，输出07Excel文件 */
//             header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//             header(
//                 "Content-Disposition:attachment;filename=" . iconv(
//                     "utf-8", "GB2312//TRANSLIT", $fileName
//                 )
//             );
//             header('Cache-Control: max-age=0');//禁止缓存
//             $savePath = 'php://output';
//         } else {
//             $savePath = $options['savePath'];
//         }

//         ob_clean();
//         ob_start();
//         $objWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
//         $objWriter->save($savePath);
//         /* 释放内存 */
//         $objSpreadsheet->disconnectWorksheets();
//         unset($objSpreadsheet);
//         ob_end_flush();

//         return true;
//     } catch (Exception $e) {
//         return false;
//     }
// }

