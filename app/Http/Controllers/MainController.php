<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class mainController extends Controller
{

    //
    public function index()
    {
        return redirect("/decode");
    }

    public function info()
    {
        phpinfo();
    }

    public function decode()
    {
        $data = '';
        if (request()->has("data"))
        {
            include 'D:\code\tianqi\inc\funcs/encrypt.class.php';

            $srcData = request()->input("data");
            $method = request()->input("method");
            $data = with(new \MCrypt())->{$method}(trim($srcData));

            if (request()->input("isJosonPretty"))
            {
                $data = json_decode($data);
                $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }

        return view("decode", ["data" => $data]);
    }
}
