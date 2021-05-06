<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * StatisticsService.php
 *
 * User：YM
 * Date：2020/2/18
 * Time：下午5:14
 */


namespace Core\Services;


/**
 * StatisticsService
 * 数据统计服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/18
 * Time：下午5:14
 *
 * @property \App\Models\Log $logModel
 * @property \App\Models\IpRegion $ipRegionModel
 */
class StatisticsService extends BaseService
{
    /**
     * getFlowData
     * 统计流量
     * User：YM
     * Date：2020/2/18
     * Time：下午9:14
     * @param $inputData
     * @return array
     */
    public function getFlowData($inputData)
    {
        if($inputData['start_time'] == $inputData['end_time']){
            $inputData['start_time'] = strtotime($inputData['start_time'].' 00:00:00');
            $inputData['end_time'] = strtotime($inputData['end_time']. '23:59:59');
            $timeRange = $this->getTimeRangeHour($inputData['start_time'], $inputData['end_time']);
            $data = $this->getFlowDataByHour($inputData, $timeRange);
        }else{
            $inputData['start_time'] = strtotime($inputData['start_time'].' 00:00:00');
            $inputData['end_time'] = strtotime($inputData['end_time']. '23:59:59');
            $timeRange = $this->getTimeRangeDay($inputData['start_time'], $inputData['end_time']);
            $data = $this->getFlowDataByDay($inputData, $timeRange);
        }
        return [
            'x_axis' => $timeRange,
            'series' => $data,
            'legend_data' => array_pluck($data,'name')
        ];
    }
    
    /**
     * getFlowDataByDay
     * 请求流量
     * User：YM
     * Date：2020/2/18
     * Time：下午5:59
     * @param $inputData
     * @param $range
     * @return array
     */
    public function getFlowDataByDay($inputData, $range)
    {
        $list = $this->logModel->getFlowData($inputData,'time_day');
        $list = $this->formatFlowData($range, $list, 'time_day');
        return $list;
    }

    /**
     * getFlowHourData
     * 请求流量
     * User：YM
     * Date：2020/2/18
     * Time：下午9:52
     * @param $inputData
     * @param $range
     * @return array
     */
    public function getFlowDataByHour($inputData, $range)
    {
        $list = $this->logModel->getFlowData($inputData,'time_hour');
        $list = $this->formatFlowData($range, $list, 'time_hour');
        return $list;
    }

    /**
     * formatFlowData
     * 格式化流量数据
     * User：YM
     * Date：2020/2/18
     * Time：下午9:12
     * @param $timeRange
     * @param $data
     * @param $column
     * @return array
     */
    public function formatFlowData($timeRange, $data, $column)
    {
        $result = [
            [
                'name' => 'PV',
                'type' => 'line',
                'itemStyle' => ['color' => '#F56C6C'],
                'data' => [],
                'data_field' => 'num',
            ],
            [
                'name' => 'UV',
                'type' => 'line',
                'itemStyle' => ['color' => '#409EFF'],
                'data' => [],
                'data_field' => 'uv',
            ],
            [
                'name' => 'IP',
                'type' => 'line',
                'itemStyle' => ['color' => '#67C23A'],
                'data' => [],
                'data_field' => 'ip',
            ],
        ];
        if($data){
            $data = array_column($data, null, $column);
        }
        foreach($timeRange as $row){
            if(isset($data[$row])){
                foreach($result as $key => $item){
                    $result[$key]['data'][] = $data[$row][$item['data_field']];
                }
            }else{
                foreach($result as $key => $item){
                    $result[$key]['data'][] = 0;
                }
            }
        }
        return $result;
    }

    /**
     * getRegionData
     * 请求地域
     * User：YM
     * Date：2020/2/19
     * Time：下午9:09
     * @param $inputData
     * @return array
     */
    public function getRegionData($inputData)
    {
        $list = $this->logModel->getRegionData($inputData);
        $res = [];
        foreach ($list as $v) {
            $tmp = $this->ipRegionModel->getInfo($v['city_id']);
            if ( !(isset($tmp['lng']) && $tmp['lng'] && isset($tmp['lat']) && $tmp['lat']) ) {
                continue;
            }
            $res[] = [
                'name' => $tmp['name'],
                'value' => [$tmp['lng'],$tmp['lat'],$v['value'],$v['uv'],$v['ip']]
            ];
        }
        return [
            'all_data' => $res
        ];      
    }
    
    /**
     * getTimeRangeDay
     * 获取天日期
     * User：YM
     * Date：2020/2/18
     * Time：下午9:15
     * @param $startTime
     * @param $endTime
     * @return array
     */
    public function getTimeRangeDay($startTime, $endTime)
    {
        $result = [];
        for($time = $startTime; $time<= $endTime; $time+= 86400){
            $result[] = date('Y-m-d', $time);
        }
        return $result;
    }

    /**
     * getTimeRangeHour
     * 获取小时日期
     * User：YM
     * Date：2020/2/18
     * Time：下午9:15
     * @param $startTime
     * @param $endTime
     * @return array
     */
    public function getTimeRangeHour($startTime, $endTime)
    {
        $result = [];
        $endTime = $endTime + 1;
        for($time = $startTime; $time<= $endTime; $time+= 3600){
            $result[] = date('Y-m-d H:00:00', $time);
        }
        return $result;
    }

}