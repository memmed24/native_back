<?php

//error types
// 400 - unknown


class Helper {

    static public function error($error){

        switch ($error){
            case 'Unknown':
                $data = [
                    'status' => 400,
                    'messages' => 'Unknown'
                ];
                return json_encode($data);

            default:
                $data = [
                    'status' => 404,
                    'messages' => 'Something went wrong'
                ];
                return json_encode($data);
        }


    }









}