<?php
// 客户端断开连接时不中断脚本的执行
ignore_user_abort();

require_once dirname(__FILE__) . '/../init.inc.php';

class Search_Spu_Cart{

    const searchOffset = 10;
    
    /**
     * 修改脚本状态
     */
    static public function updateStatus(array $listTaskId , $statusId ){
        
        Validate::testNull($listTaskId,'任务ID不能为空');
        Validate::testNull($statusId,'状态ID不能为空');
        
        foreach($listTaskId as $taskId){
            
            Cart_Join_Sample_Task::update(array(
                'task_id'       => $taskId,
                'run_status'    => $statusId,
                'run_time'      => date('Y-m-d H:i:s', time()),
            ));
        }
    }
    
    /**
     * 搜索加入购物车
     */
    static public function serachBorrow (array $condition) {
        
        $countSpuTotal                  = Search_BorrowSample::countByCondition($condition);
        for($row = 0 ;$row < $countSpuTotal; $row += self::searchOffset){

            $listSpuInfo                    = Search_BorrowSample::listByCondition($condition, array(), $row, self::searchOffset);
            
            foreach($listSpuInfo as $spuInfo){
                
                $spuId      = $spuInfo['spu_id'];
                $spuCost    = Spu_Info::getMaxCostBySpuId($spuId);
                Borrow_Spu_Info::create(array(
                    'spu_id'            => $spuId,
                    'borrow_id'         => $condition['borrow_id'],
                    'sample_storage_id' => $spuInfo['sample_storage_id'],
                    'estimate_time'     => $condition['end_time'],
                    'shipment_cost'     => $spuCost,
                    'start_time'        => $condition['start_time'],
                ));
            }   
        }
    }
    static public function main(){
                
        // 获取未处理的记录
        $standby = Cart_Join_Sample_Task::getByRunStatus(Cart_Join_Spu_RunStatus::STANDBY);

        if(empty($standby)){
            return ;
        }
        
        $listTaskId = ArrayUtility::listField($standby,'task_id');
        self::updateStatus($listTaskId,Cart_Join_Spu_RunStatus::RUNNING);

        foreach($standby as $key=>$info){

            echo "正在执行借版ID为" . $info['borrow_id'] . "的任务\n";
            $condition                      = json_decode($info['condition_data'],true);

            if(!empty($condition['search_value_list'])){
                
                $explodeKeyword = array_filter(array_values(explode(" ",$condition['search_value_list']))); 

                foreach(array_chunk($explodeKeyword,self::searchOffset) as $searchValuelist){
             
                    $condition['search_value_list'] = implode(" " , array_unique(array_filter($searchValuelist)));
                    self::serachBorrow($condition);
                }
            }else{
                self::serachBorrow($condition);
            }
            
            $countSpu = Borrow_Spu_Info::countByBorrowQuantity($condition['borrow_id']);

            Borrow_Info::update(array(
                'borrow_id'         => $condition['borrow_id'],
                'sample_quantity'   => $countSpu,
            ));
            
            Cart_Join_Sample_Task::update(array(
                'task_id'       => $info['task_id'],
                'run_status'    => Cart_Join_Spu_RunStatus::FINISH,
                'finish_time'   => date('Y-m-d H:i:s', time()),
            ));
        }
    }
}
Search_Spu_Cart::main();
echo "执行完成\n";