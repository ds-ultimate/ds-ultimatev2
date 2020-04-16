<?php

namespace App\Http\Controllers\API;

use App\Player;
use App\Http\Controllers\Controller;
use App\Util\Chart;
use App\Util\ImageChart;

class SignatureController extends Controller
{
    public function signature($server, $world, $type, $id){
        // Content type
        $worldData = \App\World::getWorld($server, $world);
        $playerData = \App\Player::player($server, $world, $id);
        if ($playerData != false && $type == 'player') {
            $image = imagecreatefrompng('images/default/signature/bg.png');
            if ($image === false) die("Error");
            if (strpos($worldData->name, 'p') !== false || strpos($worldData->name, 'c') !== false) {
                imagettftext($image, 9, 90, 15, 70 - 8 - 4, imagecolorallocate($image, 000, 000, 000), 'fonts/arial_b.ttf', $worldData->displayName());
            } else {
                imagettftext($image, 10, 90, 18, 70 - 8 - 5, imagecolorallocate($image, 000, 000, 000), 'fonts/arial_b.ttf', $worldData->displayName());
            }
            $flag = imagecreatetruecolor(16, 12);
            imagecopyresampled($flag, imagecreatefrompng('images/default/signature/' . $server . '.png'), 0, 0, 0, 0, 16, 12, 640, 480);
            imagecopyresampled($image, $flag, 27, 8 - 5, 0, 0, 16, 12, 16, 12);
            imagettftext($image, 10, 0, 56, 20 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial_b.ttf', \App\Util\BasicFunctions::decodeName($playerData->name));
            imagettftext($image, 10, 0, 300, 20 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial_i.ttf', 'by DS-Ultimate');
            imagettftext($image, 10, 0, 56, 38 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial.ttf', \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->points, true) . ' [' . \App\Util\BasicFunctions::numberConv($playerData->rank) . '.]');
            imagettftext($image, 10, 0, 56, 59 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial.ttf', ($playerData->village_count != 0) ? \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->village_count, true) . '[' . \App\Util\BasicFunctions::numberConv($playerData->points / $playerData->village_count) . '&Oslash;]' : \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->village_count, true) . ' [- &Oslash;]');
            imagettftext($image, 10, 0, 56 + 4 + 18 + 3 + 100, 38 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial.ttf', \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->offBash, true) . ' [' . (isset($playerData->offBashRank) ? $playerData->offBashRank . '.]' : '--]'));
            imagettftext($image, 10, 0, 56 + 4 + 18 + 3 + 100, 59 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial.ttf', \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->defBash, true) . ' [' . (isset($playerData->defBashRank) ? $playerData->defBashRank . '.]' : '--]'));
            imagettftext($image, 10, 0, 56 + 4 + 18 + 3 + 100 + 4 + 18 + 3 + 100, 38 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial.ttf', \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->gesBash, true) . ' [' . (isset($playerData->gesBashRank) ? $playerData->gesBashRank . '.]' : '--]'));
            imagettftext($image, 10, 0, 56 + 4 + 18 + 3 + 100 + 4 + 18 + 3 + 100, 59 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial_b.ttf', ((isset($playerData->allyLatest))?\App\Util\BasicFunctions::decodeName($playerData->allyLatest->tag ). ' [' . $playerData->allyLatest->rank . '.]':'--'));

            //copy from PictureController getPlayerSizedPic
            $rawStatData = Player::playerDataChart($server, $world, $id, 17);
            $statData = array();
            foreach ($rawStatData as $rawData) {
                $statData[$rawData->get('timestamp')] = $rawData->get('points');
            }

            $name = \App\Util\BasicFunctions::decodeName($playerData->name);
            $playerString = __('chart.who.player') . ": $name";

            $chart = new ImageChart("fonts/NotoMono-Regular.ttf", [
                'width' => 124, //124
                'height' => 75, //75
            ], false);
            $chart->render($statData, $playerString, Chart::chartTitel('points'), Chart::displayInvers('points'), true);
            imagecopyresampled($image, $chart->getRawImage(), 402, 7 - 5, 32, 10, 89, 56, 89, 56);//src_x -> 30
        }else{
            $image = imagecreatefrompng('images/default/signature/bg_noData.png');
            if ($image === false) die("Error");
        }
        // Output
        ob_start();
        imagepng($image);
        $imagedata = ob_get_clean();
        imagedestroy($image);
        return response($imagedata, 200)
            ->header('Content-Type', 'image/png');
    }
}
