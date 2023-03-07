<?php

namespace Lsg\AutoScreen\Support;

use Exception;
use Throwable;

class ApiException extends Exception
{
    public function __construct($code, $msg = null, ?Throwable $previous = null)
    {
        if (is_numeric($code)) {
            if (!$msg) {
                parent::__construct(config('jsoncode.code')[(int) $code], $code, $previous);
            } else {
                parent::__construct($msg, $code, $previous);
            }
        } elseif (is_string($code)) {
            parent::__construct($code, 400, $previous);
        } else {
            parent::__construct(config('jsoncode.code')[(int) $code], $code, $previous);
        }
    }

    /**
     * 报告异常
     *
     * @return void
     */
    public function report()
    {
    }

    /**
     * 转换异常为 HTTP 响应
     *
     * @param  \Illuminate\Http\Request
     * @param mixed $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
        ]);
    }
}
