<?php
/**
 * 模型 菜单配置
 */
class   Menu_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'prod_system';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'menu_info';

    /**
     * 字段
     */
    const   FIELDS      = 'menu_id,menu_name,menu_desc,menu_related,menu_icon,menu_level,menu_url,parent_id,create_time,update_time,delete_status';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'menu_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'menu_id',
        );
        $condition  = "`menu_id` = '" . addslashes($data['menu_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 获取左侧主菜单
     *
     * @return mixed|null
     * @throws Exception
     */
    static public function getMainMenu () {

        $menuData   = self::listAll();
        $menuData   = ArrayUtility::searchBy($menuData, array('delete_status'=>Menu_DeleteStatus::NORMAL));
        if (empty($menuData)) {

            return;
        }
        $menus      = array();
        foreach ($menuData as $menu) {
            if ($menu['menu_level'] == 1) {
                $menus[$menu['menu_id']]['top'] = array(
                    'name'      => $menu['menu_name'],
                    'url'       => $menu['menu_url'],
                    'icon'      => $menu['menu_icon'],
                    'related'   => $menu['menu_related'],
                );
                $menus[$menu['menu_id']]['child'] = array();
            }
            if ($menu['menu_level'] == 2) {
                $menus[$menu['parent_id']]['child'][] = array(
                    'name'      => $menu['menu_name'],
                    'url'       => $menu['menu_url'],
                    'icon'      => $menu['menu_icon'],
                    'related'   => $menu['menu_related'],
                );
            }
        }
        $scriptName = $_SERVER['SCRIPT_NAME'];
        foreach ($menus as &$menu) {
            if ($menu['top']['url'] == $scriptName || in_array($scriptName, explode('|', $menu['related']))) {
                $menu['top']['current'] = true;
            }
            foreach ($menu['child'] as &$childMenu) {
                if ($childMenu['url'] == $scriptName || in_array($scriptName, explode('|', $childMenu['related']))) {
                    $childMenu['current']   = true;
                    $menu['top']['current'] = true;
                }
                unset($childMenu);
            }
            unset($menu);
        }
        return      $menus;
    }
}
