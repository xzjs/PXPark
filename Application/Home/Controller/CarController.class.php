<?php
/**
 * 车辆控制器
 */
namespace Home\Controller;

use Think\Controller;
use Org\Util\CodeSwitch;

class CarController extends Controller
{

    public function car_manage()
    {
        $this->display();
    }

    /**
     * 我的车辆查询
     *
     * @param $id 用户id
     */
    public function get_mycar_info($id)
    {
        $mycar_infor = A('Car');

        $caridfind = M('user_car');

        $list = M()->table(array('px_user_car' => 'uc', 'px_car' => 'car'))->field('car.id,car.no,car.type')->where('car.id=uc.car_id' . ' and status=2' . ' and uc.user_id=' . $id)->select();
        //$list = $caridfind->where ( 'status=2'.' and2 px_user_id='.$id )->getField('px_car_id',true);// 查找用户id下的车辆id
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['type'] = $list[$i]['type'] == 1 ? '大车' : '小车';
        }
        if ($list) {
            $code = 0;// 0：成功
        } else {
            if ($list == NULL) {
                //$code = 9; // 结果为空
                return $list;
            } else {
                $code = 4;//出错
            }
        }
        return $list;
    }

    /**
     * 获得使用条款
     */
    public function getprivacy()
    {
        $result = M()->query("SELECT url FROM px_pricay WHERE TIME=(SELECT MAX(TIME) FROM px_pricay)");

        $feed;
        $code;
        $url = 0;
        if ($result) {
            $code = 0;

            $url = $result[0]['url'];

        } else  $code = 4;
        $list = '{"code":0,"url":"' . $url . '"}';

        return $list;
    }

    /**
     * 删除car表中car
     * @param id :车辆id;
     * @return Code:0：成功   7：id未找到     4：内部错误
     */
    public function delete_car($id)
    {
        $car = D("Car");
        $data['id'] = $id;
        $code;
        //echo "ids:".$id;
        $result1 = M()->execute("select *from px_car WHERE id=$id");
        if ($result1) {
            // echo "进入create";
            $sql = "DELETE FROM px_user_car WHERE car_id=$id";
            $result2 = M()->execute($sql);
            $result = M()->execute("DELETE FROM px_car WHERE id=$id");

            if ($result && $result2) {
                $code = 0;

            } else {
                $code = 4;;
            }
        } else {

            $code = 7;;
        }
        return $code;


    }

    /**
     * 在car-user表中增加我的车辆
     * @param $id uid
     * @param $type 车的类型（1：大车，2：小车）
     * @param $no 车牌号
     * @return int 7:车牌号已存在;4:内部错误;0:成功
     */
    public function  add_car_in_usrcar($id, $type, $no)
    {

        $flag = $this->add_car_incar($no, $type);

        $code = 4;
        if ($flag > 0) {
            //$c_u_add = M ( 'user_car' );
            $sql = "INSERT INTO px_user_car(user_id,car_id ,STATUS)VALUES($id,$flag,2)";
            $result = M()->execute($sql);
            if ($result) {
                $code = 0;

            }

        }
        if ($flag == -1) {

            $code = 7;
        }


        //	echo "最后code".$code."<P>";
        return $code;


    }

    /**
     * 在car表中增加我的车辆
     *
     * @param $type 车的类型（1：大车，2：小车）
     * @param $no 车牌号
     * @return int -1:车牌号已存在;-2:内部错误;正数:返回的插入id
     */
    public function add_car_incar($no, $type)
    {

        $car = D("Car");
        $data['no'] = $no;
        $data['type'] = $type;
        if ($car->create($data)) {

            $result = $car->add();
            if ($result) {

                return $result;
            } else {

                return -2;
            }
        } else {


            return -1;
        }


    }
}