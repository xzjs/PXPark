<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->show('helloworld,测试钩子是否可以使用','utf-8');
    }
}