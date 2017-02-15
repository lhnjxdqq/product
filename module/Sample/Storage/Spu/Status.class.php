<?php
class Sample_Storage_Spu_Status {

    //0����
    const   YES         = 0;
    
    //1������
    const   NO          = 1;
    
    //2�黹
    const   RETURNED    = 2;
    
    //3���ڹ黹
    const   OVERDUE_RETURNED    = 3;

    
    /**
     * ��ȡ״̬
     *
     * @return array
     */
    static public function getSpuStatus () {

        return  array(
            self::YES               => '����',
            self::NO                => '������',
            self::RETURNED          => '�ѹ黹',
            self::OVERDUE_RETURNED  => '���ڹ黹',
        );
    }
}