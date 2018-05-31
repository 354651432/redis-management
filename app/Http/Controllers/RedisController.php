<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisController extends Controller
{

    private $redisKeyType = [
        "null", "string", "list", "set", "zset", "hash",
    ];

    private $redisEncoding = "gbk";

    public function index()
    {
        return view("tree");
    }

    public function get($key)
    {
        $type = Redis::type($key);
        $data = '';
        if ($type == '0') // null
        {
            echo "null";
            return;
        }
        if ($type == '1') // string
        {
            $data = Redis::get($key);
        }
        if ($type == '2') // list
        {
            $data = Redis::Lrange($key, 0, -1);
        }
        if ($type == '3') // set
        {
            $data = Redis::SMEMBERS($key);
        }
        if ($type == '4') // zset
        {
            $data = Redis::ZRANGE($key, 0, -1);
        }
        if ($type == '5') // hash
        {
            $data = Redis::hgetall($key);
        }

        $data = $this->unserialize($data);
        $data = $this->convertEncode($data);
        // dd($data);
        return response()->json($data, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function unserialize($data)
    {
        if (is_scalar($data))
        {
            $data1 = @unserialize($data);
            return $data1 ? $data1 : $data;
        }
        if (is_array($data))
        {
            $ret = [];
            foreach ($data as $key => $item)
            {
                $ret[$key] = $this->unserialize($item);
            }
            return $ret;
        }
        return null;
    }

    private function convertEncode($data)
    {
        if ($this->redisEncoding == "utf8")
        {
            return $data;
        }
        if (is_scalar($data))
        {
            return mb_convert_encoding($data, "utf-8", $this->redisEncoding);
        }
        if (is_array($data))
        {
            $ret = [];
            foreach ($data as $key => $item)
            {
                $ret[$key] = $this->convertEncode($item);
            }
            return $ret;
        }
        return null;
    }

    public function info()
    {
        dd(Redis::info());
    }

    public function treeKeys($key = '*')
    {
        $data = Redis::keys($key);
        // $data = ["10:20:30", "10:20:40", "ddd"];
        $ret = [];
        foreach ($data as $line)
        {
            $keyArr = explode(":", $line);
            $this->addKey($ret, $keyArr, $line);
        }

        $ret = $this->changeFormat("db0", $ret);
        $this->appendType($ret);
        return response()->json($ret);
    }

    private function addKey(&$arr, &$keyArr, $value)
    {
        if (empty($keyArr))
        {
            $arr = $value;
            return;
        }
        $key = array_shift($keyArr);
        if (!$key)
        {
            return;
        }
        $this->addKey($arr[$key], $keyArr, $value);
    }

    private function changeFormat($key, $value)
    {
        if (is_array($value))
        {
            $children = [];
            foreach ($value as $key1 => $item)
            {
                $children[] = $this->changeFormat($key1, $item);
            }
            return [
                "text" => $key,
                "type" => "b",
                "nodes" => $children,
            ];
        }
        return [
            "text" => $value,
            "type" => "l",
        ];
    }

    private function appendType(&$data)
    {
        foreach ($data["nodes"] as &$node)
        {
            if ($node["type"] == "l")
            {
                $type = $this->getType($node["text"]);
                $node["key"] = $node["text"];
                $node["text"] = "{$node["text"]} [$type]";
            }
            else
            {
                $this->appendType($node);
            }
        }
    }

    private function getType($text)
    {
        $type = Redis::type($text);
        return $this->redisKeyType[$type] ?? "unknown type";
    }

    public function raw($cmd)
    {
        $arr = preg_split('/\s+/', $cmd);
        $ret = call_user_func_array([Redis::class, "rawCommand"], $arr);
        if ($ret === false)
        {
            $ret = Redis::getLastError();
        }
        $ret = $this->convertEncode($ret);
        return response()->json($ret, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
