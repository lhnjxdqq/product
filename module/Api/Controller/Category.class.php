<?php

class Api_Controller_Category {

    static public function getAll () {

        $listCategoryInfo   = ArrayUtility::searchBy(Category_Info::listAll(), array('delete_status'=>Category_DeleteStatus::NORMAL));
        return array(
          'listCategoryInfo' => $listCategoryInfo,
        );
    }

}
