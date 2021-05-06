<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * SettingService.php
 *
 * 文件描述
 *
 * User：YM
 * Date：2020/2/5
 * Time：下午5:54
 */


namespace Core\Services;


/**
 * SettingService
 * 类的介绍
 * @package Core\Services
 * User：YM
 * Date：2020/2/5
 * Time：下午5:54
 *
 * @property \Core\Services\AttachmentService attachmentService
 * @property \App\Models\Setting $settingModel
 */
class SettingService extends BaseService
{
    /**
     * getSiteInfo
     * 获取站点信息
     * User：YM
     * Date：2020/2/5
     * Time：下午5:57
     * @return mixed
     */
    public function getSiteInfo()
    {
        $siteConfig = config('dictionary.site_set');
        $info = $this->getListByNames($siteConfig);

        if ( isset($info['web_logo']) && $info['web_logo'] ) {
            $tmp = $this->attachmentService->getInfo($info['web_logo']);
            $info['web_logo_info'] = $tmp;
        }

        return $info;
    }
    
    /**
     * getListByNames
     * 通过name值取数据列表
     * User：YM
     * Date：2020/2/5
     * Time：下午8:52
     * @param array $nameArr
     * @return array
     */
    public function getListByNames($nameArr = [])
    {
        $list = $this->settingModel->getList($nameArr);
        $temp = [];
        foreach ($list as $v) {
            $temp[$v['name']] = $v['value'];
        }
        if (!$temp) {
            foreach ($nameArr as $v) {
                $temp[$v] = '';
            }
        }

        return $temp;
    }

    /**
     * getInfoByName
     * 获取设置信息通过name
     * User：YM
     * Date：2020/2/5
     * Time：下午10:43
     * @param $name
     * @return array
     */
    public function getInfoByName($name)
    {
        $info = $this->settingModel->getInfoByName($name);

        return $info;
    }
    
    /**
     * saveSettingInfo
     * 保存站点设置信息
     * User：YM
     * Date：2020/2/5
     * Time：下午5:57
     * @param array $inputData
     * @return bool
     */
    public function saveSettingInfo($inputData = [])
    {
        $siteConfig = config('dictionary.site_set');
        foreach ($inputData as $k => $v) {
            if (!in_array($k,$siteConfig)) {
                continue;
            }
            $data = [
                'name' => $k,
                'value' => $v
            ];
            $temInfo = $this->getInfoByName($k);
            if ($temInfo) {
                $data['id'] = $temInfo['id'];
            }
            $this->saveSetting($data);
        }

        return true;
    }
    
    /**
     * saveSetting
     * 保存网站设置
     * User：YM
     * Date：2020/2/5
     * Time：下午10:43
     * @param $inputData
     * @return null
     */
    public function saveSetting($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['name']) && $inputData['name']){
            $saveData['name'] = $inputData['name'];
        }
        if (isset($inputData['value'])){
            $saveData['value'] = $inputData['value'];
        }

        $id = $this->settingModel->saveInfo($saveData);

        return $id;
    }

}