<?php
/**
 * 规则控制器
 */
namespace Home\Controller;

use Think\Controller;

class  RuleController extends Controller
{
    /**
     * 增加规则信息
     */
    public function  add()
    {
        $rule_info = D('Rule');
        if (!$rule_info->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            return 0;
        } else {
            $rule_info->name = I('post.name');
            $rule_info->park_id = I('post.park_id');
            $rule_info->end_date = strtotime(I('post.ruletype_id'));
            $rule_info->start_date = strtotime(I('post.ruletype_id'));

            $result = $rule_info->add();
            if ($result) {
                $this->success('数据添加成功！');//添加成功
            } else {
                $this->error('数据添加错误！');//添加失败
            }
        }
    }
}