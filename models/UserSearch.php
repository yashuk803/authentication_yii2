<?php

namespace app\models;

use Yii;

class UserSearch
{
    private $users;

    public function findAll()
    {
        $file = fopen(Yii::getAlias('@app'). '/mysql/bd_users.txt', 'r');
        while (!feof($file)) {
            $line = fgets($file);


            $items = \json_decode( $line);

            $user = [
                'id'=>$items->id,
                'username'=>$items->username,
                'password'=>$items->password,
                'accessToken'=>$items->accessToken,
                'authKey'=>$items->authKey,
            ];

            $this->users[] = $user;

        }
        fclose($file);
        return $this->users;
    }
}