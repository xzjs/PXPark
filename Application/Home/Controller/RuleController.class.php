<?php
/**
 * 规则控制器
 */
namespace Home\Controller;

use Think\Controller;

class  RuleController extends Controller
{
    /**
     * 停车场规则增加
     * @return int|mixed 增加的id或者0
     */
    public function  add()
    {
        $_SESSION['park_id']=1;
        $rule_info = D('Rule');
        if (!$rule_info->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            return 0;
        } else {
            $rule_info->park_id = $_SESSION['park_id'];
            $result = $rule_info->add();
            if ($result) {
                return $result;
            } else {
                return 0;
            }
        }
    }
}