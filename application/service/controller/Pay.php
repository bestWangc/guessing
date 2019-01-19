<?php
namespace app\service\controller;

use think\Db;
use think\facade\Log;
use think\facade\Request;

class Pay extends Base
{
    public function payBack(Request $request)
    {
        $allParams = $request::get();
        /*var_dump('11111111111');
        $allParams = array (
            'busicd' => 'PAUT',
            'channelOrderNum' => '752019011922001483061012833247',
            'charset' => 'utf-8',
            'chcd' => 'ALP',
            'chcdDiscount' => '0.00',
            'consumerAccount' => '186****2143',
            'errorDetail' => 'SUCCESS',
            'inscd' => '92501888',
            'mchntid' => '250451653110005',
            'merDiscount' => '0.00',
            'orderNum' => '2019011910010050',
            'respcd' => '00',
            'signType' => 'SHA256',
            'terminalid' => 'ygcs11',
            'transTime' => '2019-01-19T11:19:57 08:00',
            'txamt' => '000000000100',
            'txndir' => 'A',
            'version' => '2.1',
            'sign' => '5d2b0c5adc6e4247a12cb3c4dd8c0fdad09aa1a47b99cf177d19b89602137901',
        );*/
        if (!empty($allParams)) {
            if(array_key_exists('orderNum',$allParams) && array_key_exists('errorDetail',$allParams)){
                if($allParams['errorDetail'] === 'SUCCESS'){
                    $orderInfo = Db::name('recharge')
                        ->where('order_no',$allParams['orderNum'])
                        ->where('status',2)
                        ->find();
                    if(!empty($orderInfo)){
                        $respose = [
                            'channelOrderNum' => $allParams['channelOrderNum'],
                            'chcd' => $allParams['chcd'],
                            'consumerAccount' => $allParams['consumerAccount'],
                            'errorDetail' => $allParams['errorDetail'],
                            'orderNum' => $allParams['orderNum'],
                            'txamt' => $allParams['txamt']
                        ];
                        Db::startTrans();
                        try {
                            $uData = [
                                'status' => 1,
                                'updated_date' => time(),
                                'response' => json_encode($respose)
                            ];
                            $res = Db::name('recharge')
                                ->where('id',$orderInfo['id'])
                                ->update($uData);
                            if($res){
                                Db::name('users')
                                    ->where('id',$orderInfo['user_id'])
                                    ->setInc('money',$orderInfo['amount']);
                            }
                            // 提交事务
                            Db::commit();
                            return 'SUCCESS';
                        } catch (\Exception $e) {
                            // 回滚事务
                            Db::rollback();
                        }
                    }
                }
            }
        }

        return 'FAIL';
    }

    public function payFront(Request $request)
    {
        $allParams = $request::get();

        if (!empty($allParams)) {
            if(array_key_exists('orderNum',$allParams) && array_key_exists('errorDetail',$allParams)){
                if($allParams['errorDetail'] === 'SUCCESS'){
                    $orderInfo = Db::name('recharge')
                        ->where('order_no',$allParams['orderNum'])
                        ->where('status',2)
                        ->find();
                    if(!empty($orderInfo)){
                        $respose = [
                            'channelOrderNum' => $allParams['channelOrderNum'],
                            'chcd' => $allParams['chcd'],
                            'consumerAccount' => $allParams['consumerAccount'],
                            'errorDetail' => $allParams['errorDetail'],
                            'orderNum' => $allParams['orderNum'],
                            'txamt' => $allParams['txamt']
                        ];
                        Db::startTrans();
                        try {
                            $uData = [
                                'status' => 1,
                                'updated_date' => time(),
                                'response' => json_encode($respose)
                            ];
                            $res = Db::name('recharge')
                                ->where('id',$orderInfo['id'])
                                ->update($uData);
                            if($res){
                                Db::name('users')
                                    ->where('id',$orderInfo['user_id'])
                                    ->setInc('money',$orderInfo['amount']);
                            }
                            // 提交事务
                            Db::commit();
                            return 'SUCCESS';
                        } catch (\Exception $e) {
                            // 回滚事务
                            Db::rollback();
                        }
                    }
                }
            }
        }

        return 'FAIL';
    }
}