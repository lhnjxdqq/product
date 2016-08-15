<?php
/**
 * ģ�� ���ﳵ��SPU����
 */
class   Cart_Join_Sample_Task {

    use Base_Model;
    /**
     * ���ݿ�����
     */
    const   DATABASE    = 'product';

    /**
     * ����
     */
    const   TABLE_NAME  = 'cart_join_sample_task';

    /**
     * �ֶ�
     */
    const   FIELDS      = 'task_id,user_id,condition_data,run_status,create_time,run_time,finish_time';
    /**
     * ����
     *
     * @param   array   $data   ����
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'task_id',
        );
        
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => date('Y-m-d H:i:s'),
        );
        self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * ����
     *
     * @param   array   $data   ����
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'task_id',
        );
        $condition  = "";
        $condition  = "`task_id` = '" . addslashes($data['task_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     * ����״̬��ȡ����
     *
     * @param  int  $runStatus  ״̬
     * @return array            ����
     */
    static  public function getByRunStatus($runStatus = null){
        
        Validate::testNull($runStatus,'״̬����Ϊ��');
        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `run_status` = ' . $runStatus;
        
        return self::_getStore()->fetchAll($sql);
    }
    
    /**
     * �����û���ѯ����
     * 
     *  @param  $userId     �û�ID
     *  @return array       ����    
     */
    static  public function getByUserIdAndRunStatus($userId){
        
        if(empty($userId)){
            
            throw  new ApplicationException('�û�Id����Ϊ��');
        }
        $sql ='SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `user_id`=' . $userId .' order by `task_id` DESC' ;

        return self::_getStore()->fetchOne($sql);
    }
}
