<?php

namespace app\common\exceptions;

use app\common\traits\JsonTrait;
use app\common\traits\MessageTrait;
use EasyWeChat\Core\Exceptions\HttpException;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\DB;

class Handler extends ExceptionHandler
{
    use JsonTrait;
    use MessageTrait;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        AppException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        if (DB::logging()) {
            \Log::debug('错误sql记录', array_map(function ($query) {
                $result = str_replace(array('%', '?'), array('%%', '%s'), $query['query']);
                $result = vsprintf($result, $query['bindings']);
                return $result;
            }, DB::getQueryLog()));
        }

        // 商城异常
        if ($exception instanceof ShopException) {
            return $this->renderShopException($exception);
        }
        // 404
        if ($exception instanceof NotFoundException) {
            return $this->renderNotFoundException($exception);

        }

        //开发模式异常
        if (config('app.debug')) {
            return $this->renderExceptionWithWhoops($exception);
        }
        //api异常
        if (\YunShop::isApi()) {
            \Log::error('api exception',json_decode(json_encode($exception),true));
            return $this->errorJson($exception->getMessage());
        }
        //默认异常
        if ($this->isHttpException($exception)) {
            \Log::error('http exception',json_decode(json_encode($exception),true));
            return $this->renderHttpException($exception);
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    protected function renderShopException(ShopException $exception)
    {
        if (\Yunshop::isApi() || request()->ajax()) {
            \Log::error('api exception', $exception);
            return $this->errorJson($exception->getMessage(), ['code' => $exception->getCode()]);
        }
        $redirect = $exception->redirect ?: '';
        exit($this->message($exception->getMessage(), $redirect, 'error'));
    }

    /**
     * Render an exception using Whoops.
     *
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    protected function renderExceptionWithWhoops(Exception $e)
    {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());

        return new \Illuminate\Http\Response(
            $whoops->handleException($e),
            $e->getStatusCode(),
            $e->getHeaders()
        );
    }

    protected function renderNotFoundException(NotFoundException $exception)
    {
        if (\Yunshop::isPHPUnit()) {

            exit($exception->getMessage());
        }
        if (\Yunshop::isApi() || request()->ajax()) {
            return $this->errorJson($exception->getMessage());
        }

        abort(404, $exception->getMessage());

    }
}
