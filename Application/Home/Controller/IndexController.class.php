<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $str=date('Y-m-d H-i-sa',time());
        $this->show($str,'utf-8');
    }
}