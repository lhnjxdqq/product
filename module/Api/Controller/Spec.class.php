<?php

class Api_Controller_Spec {

    static public function getAll () {

        $listSpecInfo   = ArrayUtility::searchBy(Spec_Info::listAll(), array('delete_status'=>Spec_DeleteStatus::NORMAL));
        return array(
          'listSpecInfo' => $listSpecInfo,
        );
    }

}
