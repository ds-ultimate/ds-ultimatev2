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
                return __('chart.titel_points');
            case 'rank':
                return __('chart.titel_rank');
            case 'village':
                return __('chart.titel_village');
            case 'gesBash':
                return __('chart.titel_gesBash');
            case 'offBash':
                return __('chart.titel_offBash');
            case 'defBash':
                return __('chart.titel_defBash');
            case 'utBash':
                return __('chart.titel_utBash');
        }
    }

    public static function chartLabel($data){
        switch($data) {
            case 'points':
                return __('chart.label_points');
            case 'rank':
                return __('chart.label_rank');
            case 'village':
                return __('chart.label_village');
            case 'gesBash':
                return __('chart.label_gesBash');
            case 'offBash':
                return __('chart.label_offBash');
            case 'defBash':
                return __('chart.label_defBash');
            case 'utBash':
                return __('chart.label_utBash');
        }
    }

    public static function displayInvers($data){
        switch($data) {
            case 'points':
            case 'village':
            case 'gesBash':
            case 'offBash':
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

    public static function validType($data){
        switch($data) {
            case 'points':
            case 'rank':
            case 'village':
            case 'gesBash':
            case 'offBash':
            case 'offBash':
            case 'defBash':
            case 'utBash':
                return true;
            default:
                return false;
        }
    }
}
