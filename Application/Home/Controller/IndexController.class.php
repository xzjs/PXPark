<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        echo date('Y-m-d H:i:s',mktime(14,18,1,11,16,2015));
    }
}