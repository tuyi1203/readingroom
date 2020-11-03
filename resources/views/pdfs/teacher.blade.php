<html>
<header>
  <meta charset="utf-8"/>
  <meta Content-Type="application/pdf"/>
  <style>
    th {
      background-color: #eeeeee;
      /*border: 1px;*/
    }

    td {
      text-align: center;
      /*border: 1px;*/
    }

    .text-left {
      text-align: left;
    }
  </style>
</header>
<body>
<table width="1000" border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse">
  <tr>
    <td colspan="7" style="text-align:center;">
      <h1>基本资格</h1>
    </td>
  </tr>
  <tr>
    <th rowspan="2">姓名</th>
    <th>现 名</th>
    <td>{{$teacher['name']}}</td>
    <th>性 别</th>
    <td>{{$gender_list[$teacher['gender']]}}</td>
    <th>民 族</th>
    <td>{{$minzu_list[$teacher['min_zu']]}}</td>
  </tr>
  <tr>
    <th>曾 用 名</th>
    <td>{{$teacher['old_name']}}</td>
    <th>出生日期</th>
    <td colspan="3">{{$teacher['birthday']}}</td>
  </tr>
  <tr>
    <th colspan="2">参加工作时间</th>
    <td>{{$teacher['work_time']}}</td>
    <th colspan="2">教龄</th>
    <td colspan="2">{{$teacher['teach_years']}}年</td>
  </tr>
  <tr>
    <th rowspan="2">最高学历</th>
    <th>毕（肄、结业）时间</th>
    <th colspan="2">毕业院校</th>
    <th colspan="2">专业</th>
    <th>学位</th>
  </tr>
  <tr>
    <td>{{$teacher['graduate_time']}}</td>
    <td colspan="2">{{$teacher['graduate_school']}}</td>
    <td colspan="2">{{$teacher['subject']}}</td>
    <td>{{$education_list[$teacher['education']]}}</td>
  </tr>
  <tr>
    <th colspan="2">学历证书号</th>
    <td>{{$teacher['education_no']}}</td>
    <th colspan="2">学位证书号</th>
    <td>{{$teacher['degree_no']}}</td>
    <td></td>
  </tr>
</table>
<table width="1000" border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse;border-top:0px;">
  <tr>
    <th rowspan="2">年度考核</th>
    <th>{{$teacher['kaohe']['niandu1']}}年度</th>
    <th>{{$teacher['kaohe']['niandu2']}}年度</th>
    <th>{{$teacher['kaohe']['niandu3']}}年度</th>
    <th>{{$teacher['kaohe']['niandu4']}}年度</th>
    <th>{{$teacher['kaohe']['niandu5']}}年度</th>
  </tr>
  <tr>
    <td>{{$kaohe_list[$teacher['kaohe']['niandu1_kaohe']]}}</td>
    <td>{{$kaohe_list[$teacher['kaohe']['niandu2_kaohe']]}}</td>
    <td>{{$kaohe_list[$teacher['kaohe']['niandu3_kaohe']]}}</td>
    <td>{{$kaohe_list[$teacher['kaohe']['niandu4_kaohe']]}}</td>
    <td>{{$kaohe_list[$teacher['kaohe']['niandu5_kaohe']]}}</td>
  </tr>
</table>
<table width="1000" border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse;border-top:0px;">
  <tr>
    <th style="width:400px;">是否校级管理人员</th>
    <td>{{$yesno_list[$teacher['school_manager']]}}</td>
    <th style="width:300px;"></th>
    <td></td>
  </tr>
  {{--  <tr>--}}
  {{--    <th>教师资格种类</th>--}}
  {{--    <td></td>--}}
  {{--    <th>教学水平考评等级</th>--}}
  {{--    <td></td>--}}
  {{--  </tr>--}}
  <tr>
    <th>申报学科/从事专业</th>
    <td>{{$course_list[$teacher['apply_course']]}}</td>
    {{--    <th>是否满足与申报学科相关的任教年限规定</th>--}}
    <th></th>
    <td></td>
  </tr>
  <tr>
    <th>现任专业技术职务</th>
    <td>{{$teacher['title']}}</td>
    <th>现任专业技术职务取得资格时间</th>
    <td>{{$teacher['qualification_time']}}</td>
  </tr>
  <tr>
    <th>现任专业技术职务首聘时间</th>
    <td>{{$teacher['work_first_time']}}</td>
    <th>参加何社会、学术团体、任何职务</th>
    <td>{{$teacher['remark']}}</td>
  </tr>
  <tr>
    <th>乡村学校或者薄弱学校任教经历年限</th>
    <td>{{$teacher['work_first_time']}}</td>
    <th>学校教育管理经历年限</th>
    <td>{{$teacher['work_first_time']}}</td>
  </tr>
  {{--  <tr>--}}
  {{--    <th>任现职以来继续教育学时年平均量及总量</th>--}}
  {{--    <td></td>--}}
  {{--    <th>是否合格</th>--}}
  {{--    <td></td>--}}
  {{--  </tr>--}}
  {{--  <tr>--}}
  {{--    <th>任现职以来是否完成规定的基本工作量</th>--}}
  {{--    <td></td>--}}
  {{--    <th></th>--}}
  {{--    <td></td>--}}
  {{--  </tr>--}}
</table>
<table width="1000" border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse;border-top:0px;">
  <tr>
    <td colspan="4"><h1>学历教育经历</h1></td>
  </tr>
  <tr>
    <th>起止时间</th>
    <th>毕业院校名称（以毕业证为准）</th>
    <th>学历</th>
    <th>证明人</th>
  </tr>
  @foreach($teacher['educate_experiences'] as $experience)
    <tr>
      <td>{{$experience['start_year']}}.{{$experience['start_month']}}</td>
      <td>{{$experience['school_name']}}</td>
      <td>{{$education_list[$experience['education']]}}</td>
      <td>{{$experience['prove_person']}}</td>
    </tr>
  @endforeach
</table>
<table width="1000" border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse;border-top:0px;">
  <tr>
    <td colspan="4"><h1>工作经历（含支教、乡村学校）</h1></td>
  </tr>
  <tr>
    <th>起止时间</th>
    <th>单位</th>
    <th>从事何技术专业工作</th>
    <th>证明人</th>
  </tr>
  @foreach($teacher['work_experiences'] as $experience)
    <tr>
      <td>{{$experience['start_year']}}.{{$experience['start_month']}}</td>
      <td>{{$experience['company']}}</td>
      <td>{{$experience['affairs']}}</td>
      <td>{{$experience['prove_person']}}</td>
    </tr>
  @endforeach
</table>
<table width="1000" border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse;border-top:0px;">
  <tr>
    <th>起止时间</th>
    <th>担任何种学生管理工作</th>
    <th>证明人</th>
  </tr>
  @foreach($teacher['manage_experiences'] as $experience)
    <tr>
      <td>{{$experience['start_year']}}.{{$experience['start_month']}}</td>
      <td>{{$experience['affairs']}}</td>
      <td>{{$experience['prove_person']}}</td>
    </tr>
  @endforeach
</table>
{{--<table width="1000" border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse">--}}
{{--  <tr>--}}
{{--    <td colspan="4"><h1>学习培训经历</h1></td>--}}
{{--  </tr>--}}
{{--  <tr>--}}
{{--    <th>起止时间</th>--}}
{{--    <th>主要内容</th>--}}
{{--    <th>学习地点</th>--}}
{{--    <th>证明人</th>--}}
{{--  </tr>--}}
{{--</table>--}}
<table width="1000" border="1" cellspacing="0" cellpadding="0" style="border-collapse: collapse">
  <tr>
    <td colspan="2"><h1>取得现任资格以来教学业绩情况登记</h1></td>
  </tr>
  <tr>
    <th style="width:200px;">业绩项目（条件）</th>
    <th>业绩情况</th>
  </tr>
  <tr>
    <th>教学效果</th>
    <td class="text-left">{{$teacher['educate_baseinfo']['effect']}}</td>
  </tr>
  <tr>
    <th>命题与监测</th>
    <td class="text-left">{{$teacher['educate_baseinfo']['observe']}}</td>
  </tr>
  <tr>
    <th>教研交流</th>
    <td class="text-left">{{$teacher['educate_baseinfo']['communicate']}}</td>
  </tr>
  <tr>
    <th>指导教师</th>
    <td class="text-left">{{$teacher['educate_baseinfo']['guide']}}</td>
  </tr>
  <tr>
    <th>开设选修课或综合实践活动课</th>
    <td class="text-left">{{$teacher['educate_baseinfo']['elective']}}</td>
  </tr>
</table>
</body>
</html>

