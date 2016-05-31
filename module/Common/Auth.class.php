<?php
/**
 * 权限控制器
 */
class   Common_Auth {

    /**
     * 密码编码
     *
     * @param   string  $password   密码
     * @param   string  $salt       盐
     * @return  string              编码密码
     */
    static  public  function passwordEncoding ($password, $salt) {

        return  sha1($password . $salt);
    }

    /**
     * 生成盐
     *
     * @return  string  盐
     */
    static  public  function generateSalt () {

        return  sha1(mt_rand(0, 999999999) . microtime(true));
    }

    /**
     * 校验
     */
    static  public  function validate () {

        if (!self::_validateLogin()) {

            return  false;
        }

        return  true;
    }

    /**
     * 登录
     *
     * @return  bool    登录结果
     */
    static  public  function login ($username, $password) {

        $userInfo   = User_Info::getByName($username);

        if ($userInfo['enable_status'] == User_EnableStatus::FORBIDDEN) {

            return false;
        }

        if ($userInfo['password_encode'] == self::passwordEncoding($password, $userInfo['password_salt'])) {

            $_SESSION['user_id']    = $userInfo['user_id'];
            Log_Info::logRecord($_SESSION['user_id']);
            return  true;
        }

        return  false;
    }

    /**
     * 登出
     */
    static  public  function logout () {

        unset($_SESSION['user_id']);
    }

    /**
     * 登录校验
     */
    static  private function _validateLogin () {

        $whiteList  = Config::get('auth|PHP', 'white_list');

        if (in_array($_SERVER['PHP_SELF'], $whiteList)) {

            return  true;
        }

        if (isset($_SESSION['user_id'])) {

            return  true;
        }

        return  false;
    }

    /**
     * 权限验证
     */
    static public function authority() {
        // 白名单不验证
        $whiteList  = Config::get('auth|PHP', 'white_list');
        if (in_array($_SERVER['SCRIPT_NAME'], $whiteList)) {

            return;
        }

        // 根据当前访问脚本URL去权限表查询, 如果查询不到则不验证
        $authrityInfo   = Authority_Info::getByUrl($_SERVER['SCRIPT_NAME']);
        if (empty($authrityInfo) || $authrityInfo['delete_status'] == Authority_DeleteStatus::DELETED) {

            return;
        }
        // 如果该URL需要验证, 列出所有拥有访问该脚本的所有角色
        $listAuthorityRole      = Role_AuthorityRelationship::getByAuthorityId($authrityInfo['authority_id']);
        if (empty($listAuthorityRole)) {

            Utility::notice('该权限尚未授权任何用户组');
        }
        $listAuthorityRoleIds   = ArrayUtility::listField($listAuthorityRole, 'role_id');

        // 获取当前用户的角色
        $listUserRoles      = User_RoleRelationship::getByUserId($_SESSION['user_id']);
        $listUserRoleIds    = ArrayUtility::listField($listUserRoles, 'role_id');

        // 有权限访问的角色 和 当前用户的角色 是否有交集)
        if (!array_intersect($listAuthorityRoleIds, $listUserRoleIds)) {

            Utility::notice('您没有该权限');
        }
    }
}