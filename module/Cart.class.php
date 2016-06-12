<?php
/**
 * 购物车控制器
 */
class   Cart {

    /**
     * 购物车最大数量
     */
    const   COUNT_MAX   = 10;

    /**
     * 按用户缓冲
     */
    static  private $_bufferByUser  = array();

    /**
     * 根据用户缓冲获取
     *
     * @param   int $userId 用户id
     * @return  array       购物车列表
     */
    static  public  function listByUser ($userId) {

        if (is_array(self::$_bufferByUser[$userId])) {

            return  self::$_bufferByUser[$userId];
        }

        $condition  = array('user_id'=>$userId);
        $order      = array('cart_id'=>'ASC');
        self::$_bufferByUser[$userId]   = Cart_Info::listByCondition($condition, $order, 0, self::COUNT_MAX);

        return  self::$_bufferByUser[$userId];
    }

    /**
     * 根据用户实体查询购物车关系数据
     *
     * @param   int $userId 用户id
     * @return  array       购物车关系
     */
    static  public  function getRelationshipByUserEntityMulti ($userId, $type, array $listEntityId) {

        if (empty($listEntityId)) {

            return  array();
        }

        $listByUser     = self::listByUser($userId);
        $listByUserType = ArrayUtility::searchBy($listByUser, array('entity_type'=>$type));
        $listCartId     = ArrayUtility::listField($listByUserType, 'cart_id');

        return          = Cart_Entity_Relationship::listByCartAndEntityMulti($listCartId, $listEntityId);
    }

    /**
     * 添加购物车
     *
     * @param   int     $userId 用户id
     * @param   int     $type   实体类型
     * @param   string  $name   购物车名称
     * @throws  ApplicationExcetpion
     */
    static  public  function add ($userId, $type, $name) {

        $listByUser = self::listByUser($userId);

        if (count($listByUser) >= self::COUNT_MAX) {

            throw   new ApplicationException('购物车总数不能超过' . self::COUNT_MAX);
        }

        Cart_Info::create(array(
            'user_id'   => $userId,
            'type'      => $type,
            'cart_name' => $name,
        ));
    }

    /**
     * 添加实体
     *
     * @param   int $cartId     购物车id
     * @param   int $entityId   实体id
     */
    static  public  function addEntity ($cartId, $entityId) {

        Cart_Entity_Relationship::create(array(
            'cart_id'   => $cartId,
            'entity_id' => $entityId,
        ));
    }

    /**
     * 删除实体
     *
     * @param   int $cartId     购物车id
     * @param   int $entityId   实体id
     */
    static  public  function removeEntity ($cartId, $entityId) {

        Cart_Entity_Relationship::delete(array(
            'cart_id'   => $cartId,
            'entity_id' => $entityId,
        ));
    }
}
