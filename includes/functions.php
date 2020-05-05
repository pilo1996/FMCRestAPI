<?php

function generateToken(){
    $str = 'qwertyuiopasdfghjklzxcvbnm,<.-\1234567890^?=)(/&%$£!|QWERTYUIOPçLKJHGFDSAZXCVBNM';
    $str = str_shuffle($str);
    $str = substr($str, 0, 16);
    return $str;
}

function encryptPassword($password){
    return password_hash($password, PASSWORD_BCRYPT);
}
