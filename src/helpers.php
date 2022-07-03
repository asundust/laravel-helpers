<?php

if (!function_exists('da')) {
    /**
     * dd打印封装 不断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $args
     */
    function da(...$args)
    {
        $varDumper = new Symfony\Component\VarDumper\VarDumper();
        foreach ($args as $x) {
            if (method_exists($x, 'toArray')) {
                $x = $x->toArray();
            }
            $varDumper->dump($x);
        }
    }
}

if (!function_exists('dad')) {
    /**
     * dd打印封装 并断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $args
     */
    function dad(...$args)
    {
        da(...$args);
        exit(1);
    }
}

if (!function_exists('ma')) {
    /**
     * 移动版dd打印封装 不断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $args
     */
    function ma(...$args)
    {
        echo '<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">';
        da(...$args);
    }
}

if (!function_exists('mad')) {
    /**
     * 移动版dd打印封装 并断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $args
     */
    function mad(...$args)
    {
        ma(...$args);
        exit(1);
    }
}

if (!function_exists('money_show')) {
    /**
     * 金额格式化.
     *
     * @param mixed $money  金额数
     * @param int   $number 小数位数
     */
    function money_show($money, $number = 2): string
    {
        if (null == $money || '' == $money) {
            return '0.00';
        }

        return sprintf('%01.'.$number.'f', $money);
    }
}

if (!function_exists('pluck_to_array')) {
    /**
     * [$id$ => $value$, ...] 转成 [['id' => $id$, 'value' => $value$], ...] 方法.
     *
     * @param        $array
     * @param string $value
     * @param string $key
     */
    function pluck_to_array($array, $value = 'value', $key = 'id'): array
    {
        if (method_exists($array, 'toArray')) {
            $array = $array->toArray();
        }
        $data = [];
        foreach ($array as $k => $v) {
            $data[] = [
                $key => $k,
                $value => $v,
            ];
        }

        return $data;
    }
}

if (!function_exists('ql')) {
    /**
     * @param        $message
     * @param string $path
     * @param string $name
     */
    function ql($message, $path = '', $name = 'log')
    {
        if ($path) {
            $path = trim($path, '/').'/';
            create_dir(storage_path('logs/'.$path));
        }
        $handle = fopen(storage_path('logs/'.$path.$name.'-'.date('Y-m-d').'.log'), 'a');
        fwrite($handle, $message."\n");
        fclose($handle);
    }
}

if (!function_exists('create_dir')) {
    /**
     * 功能：循环检测并创建文件夹.
     *
     * @param string $path 文件夹路径
     */
    function create_dir(string $path)
    {
        if (!file_exists($path)) {
            create_dir(dirname($path));
            mkdir($path);
        }
    }
}

if (!function_exists('console_line')) {
    /**
     * 命令行模式中, 打印需要的数据.
     *
     * @param $text
     * @param string $type
     */
    function console_line($text, $type = 'line')
    {
        if (app()->runningInConsole()) {
            $types = [
                'info' => 32, 'comment' => 33, 'warn' => 33, 'line' => 37, 'error' => '41;37', 'question' => '46;30',
            ];
            $code = $types[$type] ?? '37';
            // 30黑色，31红色，32绿色，33黄色，34蓝色，35洋红，36青色，37白色，
            echo chr(27).'['.$code.'m'."$text".chr(27).'[0m'.PHP_EOL;
        }
    }
}

if (!function_exists('console_info')) {
    function console_info($text)
    {
        console_line($text, 'info');
    }
}

if (!function_exists('console_comment')) {
    function console_comment($text)
    {
        console_line($text, 'comment');
    }
}

if (!function_exists('console_warn')) {
    function console_warn($text)
    {
        console_line($text, 'warn');
    }
}

if (!function_exists('console_error')) {
    function console_error($text)
    {
        console_line($text, 'error');
    }
}

if (!function_exists('console_question')) {
    function console_question($text)
    {
        console_line($text, 'question');
    }
}
