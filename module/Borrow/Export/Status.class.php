<?php
class Borrow_Export_Status extends SplEnum {


    const   STANDBY = 1;

    const   RUNNING = 2;

    const   FINISH  = 3;

    const   ERROR   = 4;

    /**
     * ��ȡ����ִ��״̬����
     *
     * @return array    ִ��״̬
     */
    static public function getStatusCode () {

        return  array(
            self::STANDBY        => 'δ����',
            self::RUNNING        => '������',
            self::FINISH         => '������',
            self::ERROR          => '����ʧ��',
        );
    }
}