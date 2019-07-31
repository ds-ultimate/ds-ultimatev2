<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 14.05.2019
 * Time: 19:08
 */

namespace App\Util;


class Chart
{
    public static function chartTitel($data){
        switch($data) {
            case 'points':
                return __('chart.titel.points');
            case 'rank':
                return __('chart.titel.rank');
            case 'village':
                return __('chart.titel.village');
            case 'gesBash':
                return __('chart.titel.gesBash');
            case 'offBash':
                return __('chart.titel.offBash');
            case 'defBash':
                return __('chart.titel.defBash');
            case 'utBash':
                return __('chart.titel.utBash');
        }
    }

    public static function chartLabel($data){
        switch($data) {
            case 'points':
                return __('chart.label.points');
            case 'rank':
                return __('chart.label.rank');
            case 'village':
                return __('chart.label.village');
            case 'gesBash':
                return __('chart.label.gesBash');
            case 'offBash':
                return __('chart.label.offBash');
            case 'defBash':
                return __('chart.label.defBash');
            case 'utBash':
                return __('chart.label.utBash');
        }
    }

    public static function displayInvers($data){
        switch($data) {
            case 'points':
            case 'village':
            case 'gesBash':
            case 'offBash':
            case 'defBash':
            case 'utBash':
                return false;
            case 'rank':
                return true;
            default:
                return false;
        }
    }

    public static function vAxisFormat($data){
        switch($data) {
            case 'points':
            case 'village':
            case 'gesBash':
            case 'offBash':
            case 'defBash':
            case 'utBash':
                return 'short';
            case 'rank':
                return '';
            default:
                return 'short';
        }
    }

    public static function validType($data){
        switch($data) {
            case 'points':
            case 'rank':
            case 'village':
            case 'gesBash':
            case 'offBash':
            case 'defBash':
            case 'utBash':
                return true;
            default:
                return false;
        }
    }

    public static function generateChart($rawData, $chartType){
        if (!Chart::validType($chartType)) {
            return;
        }
        
        $population = \Lava::DataTable();

        $population->addDateColumn('Tag')
            ->addNumberColumn(Chart::chartLabel($chartType));

        $oldTimestamp = 0;
        $i = 0;
        foreach ($rawData as $data){
            if (date('Y-m-d', $data->get('timestamp')) != $oldTimestamp){
                $population->addRow([date('Y-m-d', $data->get('timestamp')), $data->get($chartType)]);
                $oldTimestamp =date('Y-m-d', $data->get('timestamp'));
                $i++;
            }
        }

        if ($i == 1){
            $population->addRow([date('Y-m-d', $data->get('timestamp')-60*60*24), 0]);
        }

        \Lava::LineChart($chartType, $population, [
            'title' => Chart::chartTitel($chartType),
            'legend' => 'none',
            'hAxis' => [
                'format' => 'dd/MM'
            ],
            'vAxis' => [
                'direction' => (Chart::displayInvers($chartType)?(-1):(1)),
                'format' => (Chart::vAxisFormat($chartType)),
            ]
        ]);

        return \Lava::render('LineChart', $chartType, 'chart-'.$chartType);
    }
}
