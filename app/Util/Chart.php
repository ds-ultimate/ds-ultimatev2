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
            case 'supBash':
                return __('chart.titel.supBash');
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
            case 'supBash':
                return __('chart.label.supBash');
        }
    }

    public static function displayInvers($data){
        switch($data) {
            case 'points':
            case 'village':
            case 'gesBash':
            case 'offBash':
            case 'defBash':
            case 'supBash':
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
            case 'supBash':
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
            case 'supBash':
                return true;
            default:
                return false;
        }
    }

    public static function generateChart($rawData, $chartType, $gapFill=false, $annotations=[]){
        if (!Chart::validType($chartType)) {
            return;
        }
        $entryDiff = 4*60*60;

        $format = \Lava::DateFormat([
            'pattern' => 'dd.MM.yyyy HH:mm'
        ]);

        $annotationIndex = 0;
        $chart = \Lava::DataTable();
        $chart->addDateTimeColumn(label: 'Tag', format: $format)
            ->addNumberColumn(Chart::chartLabel($chartType))
            ->addRoleColumn('string', 'annotation')
            ->addRoleColumn('string', 'annotationText');

        $old = [
            't' => null, 'd' => null, 'l' => -1,
        ];

        foreach ($rawData as $data){
            if($old['t'] != null && $old['t'] != $old['l']) {
                $oldDiff = abs($old['t'] - $old['l'] - $entryDiff);
                $newDiff = abs($data['timestamp'] - $old['l'] - $entryDiff);
                if($oldDiff < $newDiff) {
                    $annotationIndex = static::customAdd($chart, $old['t'], $old['d'], $annotations, $annotationIndex);
                    $old['l'] = $old['t'];
                }
            }

            if($gapFill && $old['t'] != null) {
                while($old['t'] + $entryDiff + 300 < $data['timestamp']) {
                    $old['t'] += $entryDiff;
                    $annotationIndex = static::customAdd($chart, $old['t'], $old['d'], $annotations, $annotationIndex);
                }
            }

            $old['t'] = $data['timestamp'];
            $old['d'] = $data[$chartType];

            if($old['l'] + $entryDiff - 300 < $data['timestamp']) {
                $annotationIndex = static::customAdd($chart, $data['timestamp'], $data[$chartType], $annotations, $annotationIndex);
                $old['l'] = $data['timestamp'];
            }
        }

        if ($chart->getRowCount() < 2){
            $annotationIndex = static::customAdd($chart, $data['timestamp'] - $entryDiff, $data[$chartType], $annotations, $annotationIndex);
        }

        \Lava::LineChart($chartType, $chart, [
            'title' => Chart::chartTitel($chartType),
            'backgroundColor' => [
                'fill' => (session('darkmode', false))?('#212529'):('#FFFFFF'),
            ],
            'titleTextStyle' => [
                'color' => (session('darkmode', false))?('#d3d3d3'):('#000000'),
            ],
            'legend' => 'none',
            'hAxis' => [
                'format' => 'dd/MM',
                'textStyle' => [
                    'color' => (session('darkmode', false))?('#d3d3d3'):('#000000'),
                ],
            ],
            'vAxis' => [
                'direction' => (Chart::displayInvers($chartType)?(-1):(1)),
                'format' => (Chart::vAxisFormat($chartType)),
                'textStyle' => [
                    'color' => (session('darkmode', false))?('#d3d3d3'):('#000000'),
                ],
            ],
        ]);

        return \Lava::render('LineChart', $chartType, 'chart-'.$chartType);
    }

    private static function customAdd($chart, $date, $val, $annotations, $annotationIndex) {
        if(isset($annotations[$annotationIndex]) && $annotations[$annotationIndex][0] < $date) {
            $curAnnotation = $annotations[$annotationIndex];
            $chart->addRow([date('Y-m-d H:i:s', $date), $val, $curAnnotation[1], $curAnnotation[2]]);
            return $annotationIndex + 1;
        } else {
            $chart->addRow([date('Y-m-d H:i:s', $date), $val, null, null]);
            return $annotationIndex;
        }
    }
}
