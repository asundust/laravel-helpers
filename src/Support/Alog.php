<?php

namespace Asundust\Helpers\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Alog
{
    protected mixed $title = ''; // 标题
    protected bool $titleSet = false; // 标题是否设置

    /**
     * Alog constructor
     *
     * @param string $name
     * @param string $path
     * @param int $days
     * @param string $driver
     * @param array $configs
     * @param string $channel
     */
    public function __construct(
        protected string $name = 'custom',
        protected string $path = 'custom',
        protected int $days = 14,
        protected string $driver = 'daily',
        protected array $configs = [],
        protected ?string $channel = null
    ) {
        if (!$this->channel) {
            $this->channel = md5($this->name . $this->path);
        }
    }

    /**
     * 设置标题
     *
     * @param mixed|null $title
     * @return $this
     */
    public function title(mixed $title = null)
    {
        $this->title = $title;
        $this->titleSet = true;
        return $this;
    }

    /**
     * 紧急
     *
     * @param ...$message
     * @return void
     */
    public function emergency(...$message): void
    {
        $this->log('emergency', $message);
    }

    /**
     * 警告
     *
     * @param ...$message
     * @return void
     */
    public function alert(...$message): void
    {
        $this->log('alert', $message);
    }

    /**
     * 关键
     *
     * @param ...$message
     * @return void
     */
    public function critical(...$message): void
    {
        $this->log('critical', $message);
    }

    /**
     * 错误
     *
     * @param ...$message
     * @return void
     */
    public function error(...$message): void
    {
        $this->log('error', $message);
    }

    /**
     * 警告
     *
     * @param ...$message
     * @return void
     */
    public function warning(...$message): void
    {
        $this->log('warning', $message);
    }

    /**
     * 通知
     *
     * @param ...$message
     * @return void
     */
    public function notice(...$message): void
    {
        $this->log('notice', $message);
    }

    /**
     * 信息
     *
     * @param ...$message
     * @return void
     */
    public function info(...$message): void
    {
        $this->log('info', $message);
    }

    /**
     * 调试
     *
     * @param ...$message
     * @return void
     */
    public function debug(...$message): void
    {
        $this->log('debug', $message);
    }

    /**
     * 记录日志
     *
     * @param string $level
     * @param $message
     * @return void
     */
    protected function log(string $level, $message): void
    {
        // 配置处理
        $channelConfig = config('logging.channels.daily', []);
        config([
            "logging.channels.$this->channel" => array_merge($channelConfig, [
                'driver' => $this->driver,
                'path' => storage_path('logs/' . $this->path . '/' . $this->name . '.log'),
                'days' => $this->days,
            ], $this->configs)
        ]);
        // 文件信息
        $fileInfo = '';
        if (function_exists('debug_backtrace')) {
            $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            if (isset($traces[1]) && $trace = $traces[1]) {
                if (is_array($trace) && isset($trace['file']) && isset($trace['line'])) {
                    $file = substr(str_replace(base_path(), '', $trace['file']), 1);
                    $fileInfo = "{$file}:{$trace['line']}";
                }
            }
        }
        // 标题处理
        $title = '';
        if ($this->titleSet) {
            if ($this->title) {
                $title = "[$this->title] => ";
            } else {
                $title = '[无] => ';
            }
            $this->title = '';
            $this->titleSet = false;
        }
        // 消息处理
        if (is_array($message) && count($message) == 1) {
            $message = $message[0];
        }
        if (is_array($message)) {
            $message = var_export($message, true);
        } elseif ($message instanceof Jsonable) {
            $message = $message->toJson();
        } elseif ($message instanceof Arrayable) {
            $message = var_export($message->toArray(), true);
        }
        // 日志记录
        logger()->channel($this->channel)->$level($fileInfo . PHP_EOL . $title . (string)$message);
    }
}
