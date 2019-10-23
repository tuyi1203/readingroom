<?php

/**
 * @name 		clsAPIBaseController
 * @describe 	clsAppController派生类
 * @author 		tuyi
 * @since 		2019/3/4
 * @version		v1.0
 */
class clsAPIBaseController {

    protected $wsdlUrl = 'http://220.166.83.222:18010/DServices/services/InvoiceService?wsdl';
    // protected $feiyongzhuangtai = ['D'=>'呆账','X'=>'销账','Q'=>'欠费','H'=>'划账中','B'=>'部分收费'];
    protected $feiyongzhuangtai = ['D'=>'呆账','X'=>'已缴费','Q'=>'欠费','H'=>'划账中','B'=>'部分收费'];
    protected $jiesuanfangshi = ['BT'=>'手工补托','DK'=>'代扣','DS'=>'代收','HZ'=>'银行划账','TS'=>'托收凭证','XJ'=>'现金','YF'=>'预付款','ZZ'=>'转账支票'];
    protected $feiyongleixing = ['CF'=>'尾数费用','DF'=>'代收费用','MF'=>'换表费','SF'=>'特殊处理费','WF'=>'水费'];
    protected $skip_fields = [
        '费用id',
        '水费id',
        '本期抄表日',
        'pcid',
        '开票情况'
    ];

    protected $invoiceType = ['DE'=> '二次供水费发票','DW'=>'污水处理费发票','DZ'=>'基础水费电子发票'];
    protected $kaipiao = ['0'=>'可开电子发票','1'=>'不允许开票','2'=>'已开票'];
    protected $yongshuizhuangtai = [
        "01" => "正常",
        "02" => "锁门",
        "03" => "掩\压\堆\埋",
        "04" => "偷水",
        "05" => "阀漏",
        "06" => "表污",
        "07" => "倒转",
        "08" => "失准",
        "09" => "表破",
        "10" => "封铅坏",
        "11" => "表前管道漏",
        "12" => "表接头漏",
        "13" => "表前闸阀坏",
        "14" => "表雾"
    ];
    protected $yongshuileibie = [
        "A02" => "加压生活2阶" , 
        "A03" => "加压生活3阶" , 
        "A05" => "合表生活" , 
        "A06" => "合表加压生活" , 
        "A07" => "生活2阶" , 
        "A08" => "生活3阶" , 
        "A1" => "生活用水" , 
        "A11" => "增值加压生活2阶" , 
        "A12" => "增值加压生活3阶" , 
        "A13" => "增值合表生活" , 
        "A14" => "增值合表加压生活" , 
        "A15" => "增值生活2阶" , 
        "A16" => "增值生活3阶" , 
        "A17" => "增值生活" , 
        "A18" => "增值加压消防" , 
        "A19" => "增值加压生活" , 
        "A2" => "生活用水2" , 
        "A20" => "增值消防" , 
        "A3" => "生活用水3" , 
        "A4" => "加压消防" , 
        "A5" => "生活用水5" , 
        "A6" => "消防" , 
        "B1" => "普通工业用水" , 
        "B2" => "增值工业" , 
        "B3" => "优惠工业" , 
        "B4" => "工业用水4" , 
        "B5" => "工业用水5" , 
        "B6" => "增值工业用水6" , 
        "C1" => "普通商业服务" , 
        "C2" => "增值商业服务" , 
        "C3" => "商业用水3" , 
        "C5" => "商业用水5" , 
        "C6" => "增值商业用水" , 
        "D1" => "消防用水" , 
        "D2" => "消防用水2" , 
        "D5" => "消防用水5" , 
        "E1" => "建筑施工用水" , 
        "E2" => "建筑用水2" , 
        "E5" => "建筑用水5" , 
        "F07" => "增值加压特种" , 
        "F08" => "增值加压特种建筑" , 
        "F09" => "增值特种建筑" , 
        "F1" => "特种" , 
        "F2" => "增值特种用水" , 
        "F3" => "特种用水3" , 
        "F4" => "加压特种建筑" , 
        "F5" => "特种用水5" , 
        "F6" => "特种建筑" , 
        "G1" => "非生活" , 
        "G2" => "增值非生活" , 
        "G3" => "优惠非生活" , 
        "G4" => "加压非生活" , 
        "G5" => "增值加压非生活" , 
        "H1" => "检测"
    ];

    protected $feeListMock = '{
        "success": true,
        "msg": "查询成功",
        "data": [{
            "户号": 10000148,
            "费用id": 4901831,
            "金额": 33.10,
            "费用类型": "WF",
            "滞纳金": 0.00,
            "滞纳金起算日": "2013-01-25 00:00:00",
            "户名": "温体伟",
            "费用日期": "2013-01-06 00:00:00",
            "地址": "朝阳巷25#    右",
            "费用状态": "X",
            "水费id": 4901869,
            "备注": "销帐操作:工号--1608 时间--01 16 2013  1:32",
            "本期抄表日": "2013-01-06 00:00:00",
            "已收金额": 33.10,
            "结算方式": "XJ",
            "联系电话": "13568046666",
            "用水状态": "01",
            "本期行至": 1007,
            "上期行至": 1007,
            "表位号": "03030648",
            "打印id": 9168373,
            "用水类别": "A1",
            "票据代码": "SJ2008",
            "发票号码": "1608155877",
            "文件链接": null
        }, {
            "户号": 10000148,
            "费用id": 4973627,
            "金额": 36.41,
            "费用类型": "WF",
            "滞纳金": 0.00,
            "滞纳金起算日": "2013-02-25 00:00:00",
            "户名": "温体伟",
            "费用日期": "2013-02-03 00:00:00",
            "地址": "朝阳巷25#    右",
            "费用状态": "X",
            "水费id": 4973665,
            "备注": "销帐操作:工号--1606 时间--02 25 2013  2:38",
            "本期抄表日": "2013-02-03 00:00:00",
            "已收金额": 36.41,
            "结算方式": "XJ",
            "联系电话": "13568046666",
            "用水状态": "01",
            "本期行至": 1007,
            "上期行至": 1007,
            "表位号": "03030648",
            "打印id": 9222586,
            "用水类别": "A1",
            "票据代码": "SJ201105",
            "发票号码": "160621276",
            "文件链接": null
        }, {
            "户号": 10000148,
            "费用id": 5123592,
            "金额": 49.65,
            "费用类型": "WF",
            "滞纳金": 0.00,
            "滞纳金起算日": "2013-03-25 00:00:00",
            "户名": "温体伟",
            "费用日期": "2013-03-11 00:00:00",
            "地址": "朝阳巷25#    右",
            "费用状态": "X",
            "水费id": 5123630,
            "备注": "预收款自动销帐:工号--1505 时间--03 24 2013  8:59",
            "本期抄表日": "2013-03-11 00:00:00",
            "已收金额": 49.65,
            "结算方式": "XJ",
            "联系电话": "13568046666",
            "用水状态": "01",
            "本期行至": 1007,
            "上期行至": 1007,
            "表位号": "03030648",
            "打印id": null,
            "用水类别": null,
            "票据代码": null,
            "发票号码": null,
            "文件链接": null
        }]
    }';

    ###########################################################################
    # 名称			：__construct
    # 功能概要		            ：构造函数
    # 参数			：无
    # 返回值			：无
    # 初版作成日		：2019/3/4
    ###########################################################################
    public function __construct()
    {
        // parent::__construct();
    }

             /**
     * 去掉空格和回车
     */
    protected function trimSpaceAndLineFeed($str)
    {
        return str_replace("\n" , "" , str_replace(' ','',$str));
    }

    /*开票接口*/
    public function getInvoiceAPI($input)
    {
        $client=new SoapClient($this->wsdlUrl);
        $params = [
            'cardno' => $input->cardno,
            'phone' => $input->phone,
            'feeid' => $input->feeid,
            'optionid' => $input->optionid,
        ];

        if (isset($input->monthsum)) {
            $params['monthsum'] = $input->monthsum;
        }

        foreach ($this->invoiceType as $key => $value) 
        {
            $params['type'] = $key;
            $xmlRes = $client->invoiceDzNew(['param'=>json_encode($params)]);
            $arr = json_decode($xmlRes->return , true);
            $arr['type'] = $key;
            $arr['type_name'] = $this->invoiceType[$key];
            $invoiceData[] = $arr;
        }
        return $invoiceData;

    }

    /*开票接口*/
    public function getInvoiceListAPI($input)
    {
        /*测试环境* -start*/
        // $params = '{"cardno":"40054868","phone":"15908354206","feedateq":"2013-01-01","feedatez":"2019-3-30","feestatus":"1"}';
        $params = [
            'cardno' => $input->cardno,
            'companyno'=>$input->companyno,
            'phone' => $input->tel,
            'feedateq' => $input->feestartdate,
            'feedatez' => $input->feeenddate,
            'feestatus' => $input->feestatus
        ];
        $client=new SoapClient($this->wsdlUrl);
        $xmlRes = $client->getFeeListNew(['param'=>json_encode($params)]);
        $arrRes = json_decode($xmlRes->return , true);
        //查询数据无法查询到对应的数据
        if ($arrRes['success'] == false)
        {
            $data = ['result'=>'fail' , 'msg'=>$arrRes['msg']];
            return $data;
        }
        $data = $arrRes['data'];
        /*测试环境* -end*/

        //mock数据
        /*
        $mockData = json_decode($this->trimSpaceAndLineFeed($this->feeListMock) , true);
        $data = $mockData['data'];
        */
        $invoiceData = $param_invoice = $feeid = [];
        foreach ($data as $key => &$value)
        {
            $feeid[] = $value['费用id'];
        }
        $param_invoice['feeid'] = implode(',' , $feeid);
        $param_invoice['phone'] = $input->tel;
        if ($input->type == 'type_cardno')
        {
            $param_invoice['cardno'] = $input->cardno;
            $param_invoice['optionid'] = '1';//1:按户号汇总开票
        } 
        elseif ($input->type == 'type_companyno')
        {
            $param_invoice['optionid'] = '2';//2:按单位号汇总开票
        }

        foreach ($this->invoiceType as $key => $value) 
        {
            $param_invoice['type'] = $key;
            $xmlRes = $client->invoiceDzNew(['param'=>json_encode($param_invoice)]);
            $arr = json_decode($xmlRes->return , true);
            $arr['type'] = $key;
            $arr['type_name'] = $this->invoiceType[$key];
            $invoiceData[] = $arr;
        }
        // return $invoiceData;
        return ['result'=>'success','data'=>$invoiceData];
    }

    /*水费查询接口*/
    public function getFeeListAPI($input)
    {
        /*测试环境* -start*/
        // $params = '{"cardno":"40054868","phone":"15908354206","feedateq":"2013-01-01","feedatez":"2019-3-30","feestatus":"1"}';
        $params = [
            'cardno' => $input->cardno,
            'companyno'=>$input->companyno,
            'phone' => $input->tel,
            'feedateq' => $input->feestartdate,
            'feedatez' => $input->feeenddate,
            'feestatus' => $input->feestatus
        ];
        $client=new SoapClient($this->wsdlUrl);
        $xmlRes = $client->getFeeListNew(['param'=>json_encode($params)]);
        $arrRes = json_decode($xmlRes->return , true);
        //查询数据无法查询到对应的数据
        if ($arrRes['success'] == false)
        {
            $data = ['result'=>'fail' , 'msg'=>$arrRes['msg']];
            return $data;
        }
        $data = $arrRes['data'];
        /*测试环境* -end*/

        //mock数据
        /*
        $mockData = json_decode($this->trimSpaceAndLineFeed($this->feeListMock) , true);
        $data = $mockData['data'];
        */
        foreach ($data as $key => &$value)
        {

            /*修改逻辑为不判断是否开过票*/
            // $value['开票'] = $this->checkKaipiaoStatus($value);

            
            if (array_key_exists($value['费用类型'] , $this->feiyongleixing))
            {
                $data[$key]['费用类型'] = $this->feiyongleixing[$value['费用类型']];
            }
            if (array_key_exists($value['费用状态'] , $this->feiyongzhuangtai))
            {
                $data[$key]['费用状态'] = $this->feiyongzhuangtai[$value['费用状态']];
            }
            if (array_key_exists($value['结算方式'] , $this->jiesuanfangshi))
            {
                $data[$key]['结算方式'] = $this->jiesuanfangshi[$value['结算方式']];
            }
            // if (array_key_exists($value['用水类别'] , $this->yongshuileibie))
            // {
            //     $data[$key]['用水类别'] = $this->yongshuileibie[$value['用水类别']];
            // }
            if (array_key_exists($value['用水状态'] , $this->yongshuizhuangtai))
            {
                $data[$key]['用水状态'] = $this->yongshuizhuangtai[$value['用水状态']];
            }

            $data[$key]['kaipiao_feeid']  = $value['费用id'];
            $data[$key]['kaipiao_phone']  = $input->tel;
            if ($input->type == 'type_cardno'){
                $data[$key]['kaipiao_cardno']  = $input->cardno;
                $data[$key]['kaipiao_optionid']  = '1';
            } else {
                $data[$key]['kaipiao_cardno']  = $input->companyno;
                $data[$key]['kaipiao_optionid']  = '2';
            }

            if (!$this->arrEmpty($value['开票情况']))
            {
                $data[$key]['invoices'] = [];
                if (array_key_exists('开票情况' , $value) && $value['开票情况'] != '')
                {
                    foreach ($value['开票情况'] as $i => $v)
                    {
                        if (!empty($v))
                        {
                            $invoice = [
                                'invoice_type' => $v['票据类型'],
                                'invoice_name' => $this->invoiceType[$v['票据类型']],
                                'invoice_url' => $v['PDFURL']
                            ];
                            array_push($data[$key]['invoices'] , $invoice);
                        }
                    }
                }
            }

            array_walk($value , function(&$item , $index) use (&$value)
            {
                if (strpos($index , '金') !== false && is_numeric($item))
                {
                    $item .= '元';
                }
                if (empty($item))
                {
                    // $item = "";
                     //该字段为空的时候，隐藏该字段
                     unset($value[$index]);
                }
                if (in_array($index , $this->skip_fields))/*不显示需要忽略的字段*/
                {
                    unset($value[$index]);
                }
            });
           
        }

        return ['result'=>'success','data'=>$data];
    }

    /*检查该条数据的开票状态*/
    private function checkKaipiaoStatus($rowData)
    {
        $invoiceStatus = $this->kaipiao[0];

        if (($rowData['费用状态'] != 'X') || ($rowData['已收金额'] == '0') || !$this->arrEmpty($rowData['开票情况']))
        {
            $invoiceStatus = $this->kaipiao[1];
        }
        else
        {
            $invoiceStatus = $this->kaipiao[2];
        }

        return $invoiceStatus;
    }

    /*检条数组是否为空*/
    private function arrEmpty($arr)
    {
        if (empty($arr)) return true;

        if (count($arr) > 0)
        {
            foreach ($arr as $key => $value) 
            {
                if (!empty($value))
                {
                    return false;
                }
            }
        }
        return true;
    }
}
?>