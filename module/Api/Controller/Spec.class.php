<?php

class Api_Controller_Spec {

    static public function getAll () {

        $listSpecInfo   = ArrayUtility::searchBy(Spec_Info::listAll(), array('delete_status'=>Spec_DeleteStatus::NORMAL));
        return array(
          'listSpecInfo' => $listSpecInfo,
        );
    }
    
    static public function getSpecValueAll () {

        $listSpecInfo   = Spec_Value_Info::listAll();
        return array(
          'listSpecValueInfo' => $listSpecInfo,
        );
    }

}
