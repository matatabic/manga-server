<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 *​
 * BaseController.php
 *
 * 基础控制器
 *
 * User：YM
 * Date：2019/11/14
 * Time：上午9:53
 */


namespace App\Controller;

use App\Constants\StatusCode;

/**
 * BaseController
 * 基础类的控制器
 * @package App\Controller
 * User：YM
 * Date：2019/11/14
 * Time：上午9:53
 */
class BaseController extends AbstractController
{

    /**
     * __get
     * 隐式注入仓库类
     * User：YM
     * Date：2019/11/21
     * Time：上午9:27
     * @param $key
     * @return \Psr\Container\ContainerInterface|void
     */
    public function __get($key)
    {
        if ($key == 'app') {
            return $this->container;
        } elseif (substr($key, -5) == 'Model') {
            $key = strstr($key,'Model',true);
            return $this->getModelInstance($key);
        } elseif (substr($key, -7) == 'Service') {
            return $this->getServiceInstance($key);
        } else {
            throw new \RuntimeException("服务/模型{$key}不存在，书写错误！", StatusCode::ERR_SERVER);
        }
    }
    
    /**
     * getModelInstance
     * 获取数据模型类实例
     * User：YM
     * Date：2019/11/21
     * Time：上午10:30
     * @param $key
     * @return mixed
     */
    public function getModelInstance($key)
    {
        $key = ucfirst($key);
        $fileName = BASE_PATH."/app/Models/{$key}.php";
        $className = "App\\Models\\{$key}";

        if (file_exists($fileName)) {
            //model一般不要常驻内存
            //return $this->container->get($className);
            return make($className);
        } else {
            throw new \RuntimeException("服务/模型{$key}不存在，文件不存在！", StatusCode::ERR_SERVER);
        }
    }

    /**
     * getServiceInstance
     * 获取服务类实例
     * User：YM
     * Date：2019/11/21
     * Time：上午10:30
     * @param $key
     * @return mixed
     */
    public function getServiceInstance($key)
    {
        $key = ucfirst($key);
        $fileName = BASE_PATH."/app/Core/Services/{$key}.php";
        $className = "Core\\Services\\{$key}";

        if (file_exists($fileName)) {
            return $this->container->get($className);
        } else {
            throw new \RuntimeException("服务/模型{$key}不存在，文件不存在！", StatusCode::ERR_SERVER);
        }
    }
   
    /**
     * success
     * 成功返回请求结果
     * User：YM
     * Date：2019/11/20
     * Time：下午3:56
     * @param array $data
     * @param null $msg
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function success($data = [], $msg = null)
    {
        return $this->response->success($data,$msg);
    }

    /**
     * error
     * 业务相关错误结果返回
     * User：YM
     * Date：2019/11/20
     * Time：下午3:56
     * @param int $code
     * @param null $msg
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function error($code = StatusCode::ERR_EXCEPTION, $msg = null)
    {
        return $this->response->error($code,$msg);
    }

}