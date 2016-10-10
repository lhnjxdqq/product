<?php

class Api_Controller_Style {

    static public function getAll () {

        $listStyleInfo   = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
        return array(
          'listStyleInfo' => $listStyleInfo,
        );
    }

}
