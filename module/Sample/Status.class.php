<?php
class Sample_Status {

    //0上传成功
    const   IMPORT_SUCCESS = 0;
    
    //1处理中
    const   RUNNING      = 1;
    
    //2待审核
    const   WAIT_AUDIT   = 2;
        
    //3待生成
    const   WAIT_UPDATE  = 3;
    
    //3生成中
    const   UPDATE       = 4;
    
    //4已完成
    const   FINISHED     = 5;
    
    //5已删除
    const   DELETED      = 6;
    
    /**
     * 获取状态
     *
     * @return array
     */
    static public function getSampleStatus () {

        return  array(
            self::IMPORT_SUCCESS   => '上传成功',
            self::RUNNING          => '运行中',
            self::WAIT_AUDIT       => '待审核',
            self::WAIT_UPDATE      => '待生成',
            self::UPDATE           => '生成中',
            self::FINISHED         => '已完成',
            self::DELETED          => '已删除',
        );
    }
}