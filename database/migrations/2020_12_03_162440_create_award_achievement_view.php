<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAwardAchievementView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::statement('
      CREATE OR REPLACE VIEW award_achievement_view AS
select func_inc_var_session(0) as id , a.* from (
-- 教师获奖情况
(SELECT
	\'teach\' AS \'object_category\',	-- 分类标识
	a.id AS \'object_id\',
\'award\' as \'award_or_achievement\',	-- 分类ID
	CASE a.type
WHEN \'1\' THEN
	\'指导参赛奖\'
WHEN \'2\' THEN
	\'交流管理经验情况\'
ELSE
	\'\'
END AS \'type\',
 c.campus, -- 校区
a.user_id, -- 用户ID
c. NAME AS \'user_name\', -- 用户名
a.award_type as \'award_achievement_type\', -- 获奖类别ID
b.dict_name AS \'award_name\', -- 类别
a.award_title as \'title\',
 -- 项目内容/发表题目/得奖内容
a.award_authoriry_organization as \'organization\',
 -- 颁发单位/刊物名称及期数/指导对象
\'\' AS \'kan_hao_deng\',
 a.award_level,
 -- 奖项级别/发表范围
a.award_position,
 -- 获奖等级
a.award_date as \'the_date\',
 -- 获奖/发表 时间
\'\' AS \'remark\' -- 备注
FROM
	progress_teach_achievement a
LEFT JOIN (
	SELECT
		*
	FROM
		progress_dicts
	WHERE
		dict_category = \'15\'
) b ON a.award_type = b.dict_value
INNER JOIN progress_baseinfos c ON a.user_id = c.user_id
WHERE
	a.award_type <> \'\'
AND a.achievement_type IS NULL
ORDER BY a.id)
UNION
	(SELECT
		\'educate\' AS \'object_category\',
		-- 分类标识
		a.id AS \'object_id\',
\'award\' as \'award_or_achievement\',
		CASE a.type
	WHEN \'1\' THEN
		\'现场课/录像课/微课/课件/基本功\'
	WHEN \'2\' THEN
		\'讲座/示范课\'
	WHEN \'3\' THEN
		\'指导参赛奖\'
	ELSE
		\'\'
	END AS \'type\',
	-- 分类ID
	c.campus,
	-- 校区
	a.user_id,
	-- 用户ID
	c. NAME AS \'user_name\',
	-- 用户名
a.award_type as \'award_achievement_type\', -- 获奖类别ID
	b.dict_name AS \'award_name\',
	-- 类别
	a.award_title as \'title\',
	-- 项目内容/发表题目/得奖内容
	a.award_authoriry_organization as \'organization\',
	-- 颁发单位/刊物名称及期数/指导对象
	\'\' AS \'kan_hao_deng\',
	a.award_level,
	-- 奖项级别/发表范围
	a.award_position,
	-- 获奖等级
	a.award_date as \'the_date\',
	-- 获奖/发表 时间
	\'\' AS \'remark\' -- 备注
FROM
	progress_educate_achievement a
LEFT JOIN (
	SELECT
		*
	FROM
		progress_dicts
	WHERE
		dict_category = \'15\'
) b ON a.award_type = b.dict_value
INNER JOIN progress_baseinfos c ON a.user_id = c.user_id
WHERE
	a.award_type <> \'\'
AND a.achievement_type IS NULL
ORDER BY a.id)
UNION
(
	SELECT
		\'research\' AS \'object_category\',
		-- 分类标识
		a.id AS \'object_id\',
\'award\' as \'award_or_achievement\',
		CASE a.type
	WHEN \'1\' THEN
		\'著述\'
	WHEN \'2\' THEN
		\'课题\'
	WHEN \'3\' THEN
		\'著作\'
	WHEN \'4\' THEN
		\'专利或著作权\'
	ELSE
		\'\'
	END AS \'type\',
	-- 分类ID
	c.campus,
	-- 校区
	a.user_id,
	-- 用户ID
	c. NAME AS \'user_name\',
	-- 用户名
a.award_type as \'award_achievement_type\', -- 获奖类别ID
	b.dict_name AS \'award_name\',
	-- 类别
	a.award_title as \'title\',
	-- 项目内容/发表题目/得奖内容
	a.award_authoriry_organization as \'organization\',
	-- 颁发单位/刊物名称及期数/指导对象
	\'\' AS \'kan_hao_deng\',
	a.award_level,
	-- 奖项级别/发表范围
	a.award_position,
	-- 获奖等级
	a.award_date as \'the_date\',
	-- 获奖/发表 时间
	\'\' AS \'remark\' -- 备注
FROM
	progress_research_achievement a
LEFT JOIN (
	SELECT
		*
	FROM
		progress_dicts
	WHERE
		dict_category = \'15\'
) b ON a.award_type = b.dict_value
INNER JOIN progress_baseinfos c ON a.user_id = c.user_id
WHERE
	a.award_type IS NOT NULL
ORDER BY a.id)
UNION
	(SELECT
		\'award\' AS \'object_category\',
		-- 分类标识
		a.id AS \'object_id\',
\'award\' as \'award_or_achievement\',
		CASE a.type
	WHEN \'1\' THEN
		\'荣誉及其他奖项\'
	ELSE
		\'\'
	END AS \'type\',
	-- 分类ID
	c.campus,
	-- 校区
	a.user_id,
	-- 用户ID
	c. NAME AS \'user_name\',
	-- 用户名
a.award_type as \'award_achievement_type\', -- 获奖类别ID
	b.dict_name AS \'award_name\',
	-- 类别
	a.award_title as \'title\',
	-- 项目内容/发表题目/得奖内容
	a.award_authoriry_organization as \'organization\',
	-- 颁发单位/刊物名称及期数/指导对象
	\'\' AS \'kan_hao_deng\',
	a.award_level,
	-- 奖项级别/发表范围
	a.award_position,
	-- 获奖等级
	a.award_date as \'the_date\',
	-- 获奖/发表 时间
	\'\' AS \'remark\' -- 备注
FROM
	progress_award_achievement a
LEFT JOIN (
	SELECT
		*
	FROM
		progress_dicts
	WHERE
		dict_category = \'15\'
) b ON a.award_type = b.dict_value
INNER JOIN progress_baseinfos c ON a.user_id = c.user_id
WHERE
	a.award_type IS NOT NULL
ORDER BY a.id)
UNION
-- 教师成果情况
(SELECT
	\'teach\' AS \'object_category\',
	-- 分类标识
	a.id AS \'object_id\',
	\'achievement\' AS \'award_or_achievement\',
	-- 分类ID
	CASE a.type
WHEN \'1\' THEN
	\'指导参赛奖\'
WHEN \'2\' THEN
	\'交流管理经验情况\'
ELSE
	\'\'
END AS \'type\',
 c.campus,
 -- 校区
a.user_id,
 -- 用户ID
c. NAME AS \'user_name\',
 -- 用户名
a.achievement_type AS \'award_achievement_type\',
 -- 获奖类别ID
b.dict_name AS \'achievement_type_name\',
 -- 类别
a.manage_exp_communicate_content AS \'title\',
 -- 项目内容/发表题目/得奖内容
\'\' AS \'organization\',
 -- 颁发单位/刊物名称及期数/指导对象
\'\' AS \'kan_hao_deng\',
 \'\' AS \'award_level\',
 -- 奖项级别/发表范围
\'\' AS \'award_position\',
 -- 获奖等级
a.manage_exp_communicate_date AS \'the_date\',
 -- 获奖/发表 时间
\'\' AS \'remark\' -- 备注
FROM
	progress_teach_achievement a
LEFT JOIN (
	SELECT
		*
	FROM
		progress_dicts
	WHERE
		dict_category = \'11\'
) b ON a.achievement_type = b.dict_value
INNER JOIN progress_baseinfos c ON a.user_id = c.user_id
WHERE
	a.achievement_type <> \'\'
ORDER BY a.id)
UNION
	(SELECT
		\'educate\' AS \'object_category\',
		-- 分类标识
		a.id AS \'object_id\',
		\'achievement\' AS \'award_or_achievement\',
		-- 分类ID
		CASE a.type
	WHEN \'1\' THEN
		\'现场课/录像课/微课/课件/基本功\'
	WHEN \'2\' THEN
		\'讲座/示范课\'
	WHEN \'3\' THEN
		\'指导参赛奖\'
	ELSE
		\'\'
	END AS \'type\',
	c.campus,
	-- 校区
	a.user_id,
	-- 用户ID
	c. NAME AS \'user_name\',
	-- 用户名
	a.achievement_type AS \'award_achievement_type\',
	-- 获奖类别ID
	b.dict_name AS \'achievement_type_name\',
	-- 类别
	CASE a.type
WHEN \'2\' THEN
	a.lecture_content
ELSE
	\'\'
END AS \'title\',
 -- 项目内容/发表题目/得奖内容
a.lecture_organization AS \'organization\',
 -- 颁发单位/刊物名称及期数/指导对象/主办单位
\'\' AS \'kan_hao_deng\',
 a.lecture_scope AS \'award_level\',
 -- 奖项级别/发表范围
\'\' AS \'award_position\',
 -- 获奖等级
a.lecture_date AS \'the_date\',
 -- 获奖/发表 时间
\'\' AS \'remark\' -- 备注
FROM
	progress_educate_achievement a
LEFT JOIN (
	SELECT
		*
	FROM
		progress_dicts
	WHERE
		dict_category = \'11\'
) b ON a.achievement_type = b.dict_value
INNER JOIN progress_baseinfos c ON a.user_id = c.user_id
WHERE
	a.achievement_type <> \'\'
ORDER BY a.id)
UNION
	(SELECT
		\'educate\' AS \'object_category\',
		-- 分类标识
		a.id AS \'object_id\',
		\'achievement\' AS \'award_or_achievement\',
		-- 分类ID
		CASE a.type
	WHEN \'1\' THEN
		\'著述\'
	WHEN \'2\' THEN
		\'课题\'
	WHEN \'3\' THEN
		\'著作\'
	WHEN \'4\' THEN
		\'专利或著作权\'
	ELSE
		\'\'
	END AS \'type\',
	c.campus,
	-- 校区
	a.user_id,
	-- 用户ID
	c. NAME AS \'user_name\',
	-- 用户名
	a.achievement_type AS \'award_achievement_type\',
	-- 获奖类别ID
	b.dict_name AS \'achievement_type_name\',
	-- 类别
	CASE a.type
WHEN \'1\' THEN
	a.paper_title
WHEN \'2\' THEN
	a.subject_title
WHEN \'3\' THEN
	a.book_title
WHEN \'4\' THEN
	\'\'
ELSE
	\'\'
END AS \'title\',
 -- 项目内容/发表题目/得奖内容
CASE a.type
WHEN \'1\' THEN
	a.paper_book_title
WHEN \'2\' THEN
	a.subject_exec -- 课题委托单位
WHEN \'3\' THEN
	a.book_publish_company_name
WHEN \'4\' THEN
	\'\'
ELSE
	\'\'
END AS \'organization\',
 -- 颁发单位/刊物名称及期数/指导对象/主办单位
CASE type
WHEN \'1\' THEN
	concat(
		a.paper_book_kanhao,
		\' \',
		a.paper_book_juanhao,
		\'/\',
		a.paper_book_title
	)
WHEN \'2\' THEN
	\'\' -- 课题委托单位
WHEN \'3\' THEN
	concat(
		a.book_publish_no,
		\'/\',
		a.book_publish_company_name
	)
WHEN \'4\' THEN
	\'\'
ELSE
	\'\'
END AS \'kan_hao_deng\',
 \'\' AS \'award_level\',
 -- 奖项级别/发表范围
\'\' AS \'award_position\',
 -- 获奖等级
CASE a.type
WHEN \'1\' THEN
	a.paper_date
WHEN \'2\' THEN
	a.subject_end_date -- 课题委托单位
WHEN \'3\' THEN
	a.book_publish_date
WHEN \'4\' THEN
	a.copyright_ratification
ELSE
	\'\'
END AS \'the_date\',
 -- 获奖/发表 时间
\'\' AS \'remark\' -- 备注
FROM
	progress_research_achievement a
LEFT JOIN (
	SELECT
		*
	FROM
		progress_dicts
	WHERE
		dict_category = \'11\'
) b ON a.achievement_type = b.dict_value
INNER JOIN progress_baseinfos c ON a.user_id = c.user_id
WHERE
	a.achievement_type <> \'\'
order by a.id)
) a join (SELECT func_inc_var_session(1)) b
        '
      );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('award_achievement_view');
    }
}
