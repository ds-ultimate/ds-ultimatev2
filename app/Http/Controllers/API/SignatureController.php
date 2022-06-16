<?php

namespace App\Http\Controllers\API;

use App\Player;
use App\PlayerTop;
use App\World;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use App\Util\CacheLogger;
use App\Util\Chart;
use App\Util\Icon;
use App\Util\ImageChart;
use App\Util\TextImageRenderer;
use Carbon\Carbon;

class SignatureController extends Controller
{
    private static $cBlack = [0, 0, 0];
    private static $cPrimary = [49, 32, 6];
    private static $cBG = [247, 235, 194];
    private static $cBGDarker = [242, 221, 152];
        
    public function signature($server, $world, $type, $id){
        $worldData = World::getAndCheckWorld($server, $world);
        
        switch($type) {
            case "player":
                $modelData = Player::player($worldData, $id);
                $createFkt = [static::class, 'createPlayerSignature'];
                break;
            case "playerTop":
                $modelData = PlayerTop::player($worldData, $id);
                $createFkt = [static::class, 'createTopPlayerSignature'];
                break;
            default:
                return false;
        }
        
        if($modelData == null) {
            if(!BasicFunctions::endsWith($type, "Top")) {
                return $this->signature($server, $world, $type . "Top", $id);
            }
            
            $img =  static::createNoDataImg($server . $world, $id);
            ob_start();
            imagepng($img);
            $imagedata = ob_get_clean();
            imagedestroy($img);
            return response($imagedata, 200)
                    ->header('Content-Type', 'image/png');
        }
        
        $signature = $modelData->getSignature($worldData);
        if(!$signature->isCached() || !file_exists($signature->getCacheFile())) {
            if (!file_exists(storage_path(config('tools.signature.cacheDir')))) {
                mkdir(storage_path(config('tools.signature.cacheDir')), 0777, true);
            }
            
            $cFile = $signature->getCacheFile();
            if($createFkt(substr($cFile, 0, strrpos($cFile, ".")), $worldData, $id)) {
                $signature->cached = Carbon::now();
                $signature->save();
            } else {
                return "Error";
            }
            CacheLogger::logMiss(CacheLogger::$SIGNATURE_TYPE, $signature->getCacheFileName());
        } else {
            CacheLogger::logHit(CacheLogger::$SIGNATURE_TYPE, $signature->getCacheFileName());
        }
        
        return response()->file($signature->getCacheFile());
    }
    
    private static function createNoDataImg($serWorld, $id) {
        $image = imagecreatefrompng(public_path('images/default/signature/bg_noData.png'));
        imagettftext($image, 10, 0, 56, 20 - 5, imagecolorallocate($image, 49, 32, 6), public_path('fonts/arial_b.ttf'), 
                "Unable to find player with id " . $id . "\nin our database for world " . $serWorld);
        return $image;
    }
    
    private static function createPlayerSignature($targetFile, World $world, $id) {
        // Content type
        $playerData = Player::player($world, $id);
        
        $img = new TextImageRenderer(font: public_path("/fonts/arial.ttf"), dim: ["width" => 500, "height" => 60]);
        imagefill($img->getRawImage(), 0, 0, $img->colAllocate(static::$cBG));
        
        $img->setFont(public_path("/fonts/arial_b.ttf"));
        if (strpos($world->name, 'p') !== false || strpos($world->name, 'c') !== false) {
            $img->renderAlignedText(TextImageRenderer::$ANCHOR_MID_LEFT, 6, $img->h() / 2, 9, $world->display_name, static::$cBlack, 90);
        } else {
            $img->renderAlignedText(TextImageRenderer::$ANCHOR_MID_LEFT, 6, $img->h() / 2, 10, $world->display_name, static::$cBlack, 90);
        }
        $img->insertPubImage("images/default/signature/{$world->server->flag}.png", 27, 3, 16, 12);
        
        $img->renderAlignedText(TextImageRenderer::$ANCHOR_TOP_LEFT, 50, 3, 10, BasicFunctions::decodeName($playerData->name), static::$cPrimary);
        $img->setFont(public_path("/fonts/arial_i.ttf"));
        $img->renderAlignedText(TextImageRenderer::$ANCHOR_TOP_RIGHT, 400 - 10, 3, 10, 'by DS-Ultimate', static::$cPrimary);
        $img->setFont(public_path("/fonts/arial.ttf"));
        
        $points = [
            Icon::$TROOPS,
            BasicFunctions::thousandsCurrencyFormat($playerData->points, true),
            BasicFunctions::numberConv($playerData->rank) . "."
        ];
        $villages = [
            Icon::$VILLAGE,
            BasicFunctions::thousandsCurrencyFormat($playerData->village_count, true),
            ($playerData->village_count > 0) ? BasicFunctions::numberConv($playerData->points / $playerData->village_count) . " \u{00D8}" : ""
        ];
        $offBash = [
            Icon::$UNIT_AXE,
            BasicFunctions::thousandsCurrencyFormat($playerData->offBash, true),
            isset($playerData->offBashRank) ? $playerData->offBashRank . '.' : '--'
        ];
        $deffBash = [
            Icon::$UNIT_SWORD,
            BasicFunctions::thousandsCurrencyFormat($playerData->defBash, true),
           isset($playerData->defBashRank) ? $playerData->defBashRank . '.' : '--'
        ];
        $gesBash = [
            Icon::$BUILDING_BARRACKS,
            BasicFunctions::thousandsCurrencyFormat($playerData->gesBash, true),
            isset($playerData->gesBashRank) ? $playerData->gesBashRank . '.' : '--'
        ];
        $ally = [
            Icon::$ALLY,
            (isset($playerData->allyLatest))?BasicFunctions::decodeName($playerData->allyLatest->tag ). ' [' . $playerData->allyLatest->rank . '.]':'--'
        ];
        $data = [$points, $villages, $offBash, $deffBash, $gesBash, $ally];
        
        $insets = ['xs' => 27, 'ys' => 19, 'xe' => 110, 'ye' => 2];
        static::renderBoxedSignature($img, $data, $insets, static::$cBGDarker, static::$cPrimary);
        
        //copy from PictureController getPlayerSizedPic
        $rawStatData = Player::playerDataChart($world, $id, 17);
        $statData = array();
        foreach ($rawStatData as $rawData) {
            $statData[$rawData['timestamp']] = $rawData['points'];
        }

        $name = BasicFunctions::decodeName($playerData->name);
        $playerString = __('chart.who.player') . ": $name";

        $chart = new ImageChart(public_path("fonts/NotoMono-Regular.ttf"), [
            'width' => 124, //124
            'height' => 75, //75
        ], false);
        $chart->render($statData, $playerString, Chart::chartTitel('points'), Chart::displayInvers('points'), true);
        imagecopyresampled($img->getRawImage(), $chart->getRawImage(), 402, 7 - 5, 32, 10, 89, 56, 89, 56);//src_x -> 30
        
        // Output
        $img->saveTo($targetFile, "png");
        return true;
    }
    
    private static function createTopPlayerSignature($targetFile, World $world, $id){
        // Content type
        $playerData = PlayerTop::player($world, $id);
        
        $img = new TextImageRenderer(font: public_path("/fonts/arial_b.ttf"), dim: ["width" => 500, "height" => 60]);
        imagefill($img->getRawImage(), 0, 0, $img->colAllocate(static::$cBG));
        
        if (strpos($world->name, 'p') !== false || strpos($world->name, 'c') !== false) {
            $img->renderAlignedText(TextImageRenderer::$ANCHOR_MID_LEFT, 6, $img->h() / 2, 9, $world->display_name, static::$cBlack, 90);
        } else {
            $img->renderAlignedText(TextImageRenderer::$ANCHOR_MID_LEFT, 6, $img->h() / 2, 10, $world->display_name, static::$cBlack, 90);
        }
        $img->insertPubImage("images/default/signature/{$world->server->flag}.png", 27, 3, 16, 12);
        
        $img->renderAlignedText(TextImageRenderer::$ANCHOR_TOP_LEFT, 50, 3, 10, BasicFunctions::decodeName($playerData->name), static::$cPrimary);
        $img->setFont(public_path("/fonts/arial_i.ttf"));
        $img->renderAlignedText(TextImageRenderer::$ANCHOR_TOP_RIGHT, $img->w() - 10, 3, 10, 'by DS-Ultimate', static::$cPrimary);
        $img->setFont(public_path("/fonts/arial.ttf"));
        
        $points = [
            Icon::$TROOPS,
            BasicFunctions::thousandsCurrencyFormat($playerData->points_top, true),
            BasicFunctions::numberConv($playerData->rank_top) . "."
        ];
        $villages = [
            Icon::$VILLAGE,
            BasicFunctions::thousandsCurrencyFormat($playerData->village_count_top, true),
            ($playerData->village_count_top > 0) ? BasicFunctions::numberConv($playerData->points_top / $playerData->village_count_top) . " \u{00D8}" : ""
        ];
        $offBash = [
            Icon::$UNIT_AXE,
            BasicFunctions::thousandsCurrencyFormat($playerData->offBash_top, true),
            isset($playerData->offBashRank_top) ? $playerData->offBashRank_top . '.' : '--'
        ];
        $deffBash = [
            Icon::$UNIT_SWORD,
            BasicFunctions::thousandsCurrencyFormat($playerData->defBash_top, true),
           isset($playerData->defBashRank_top) ? $playerData->defBashRank_top . '.' : '--'
        ];
        $gesBash = [
            Icon::$BUILDING_BARRACKS,
            BasicFunctions::thousandsCurrencyFormat($playerData->gesBash_top, true),
            isset($playerData->gesBashRank_top) ? $playerData->gesBashRank_top . '.' : '--'
        ];
        $supBash = [
            Icon::$DEF_SWORD,
            BasicFunctions::thousandsCurrencyFormat($playerData->supBash_top, true),
            isset($playerData->supBashRank_top) ? $playerData->supBashRank_top . '.' : '--'
        ];
        
        $data = [$points, $villages, $offBash, $deffBash, $gesBash, $supBash];
        
        $insets = ['xs' => 27, 'ys' => 19, 'xe' => 10, 'ye' => 2];
        static::renderBoxedSignature($img, $data, $insets, static::$cBGDarker, static::$cPrimary);
        
        $img->saveTo($targetFile, "png");
        return true;
    }
    
    private static function renderBoxedSignature(TextImageRenderer $img, $data, $insets, $cBox, $cText) {
        $l['x'] = $insets['xs'];
        $l['y'] = $insets['ys'];
        $l['w'] = $img->w() - $insets['xs'] - $insets['xe'];
        $l['h'] = $img->h() - $insets['ys'] - $insets['ye'];
        $s = ['w' => 10, 'h' => 4];
        
        $rows = 2;
        $columns = ceil(count($data) / $rows);
        $partH = ($l['h'] - $s['h'] * ($rows - 1)) / $rows;
        $partW = ($l['w'] - $s['w'] * ($columns - 1)) / $columns;
        
        for($i = 0; $i < count($data); $i++) {
            $iC = intval($i / $rows);
            $iR = $i % $rows;
            $x = $l['x'] + $iC * ($s['w'] + $partW);
            $y = $l['y'] + $iR * ($s['h'] + $partH);
            $tx = $data[$i][1];
            if(isset($data[$i][2]) && strlen($data[$i][2]) > 0) {
                $tx .= " [" . $data[$i][2] . "]";
            }
            
            static::renderBoxedSignatureData($img, $data[$i][0], $tx, $x, $y, $partW, $partH, $cBox, $cText);
        }
    }
    
    private static function renderBoxedSignatureData(TextImageRenderer $img, $descImg, $text, $x, $y, $w, $h, $cBox, $cText) {
        $desc = imagecreatefrompng(public_path($descImg));
        $descW = imagesx($desc);
        $descH = imagesy($desc);
        imagecopy($img->getRawImage(), $desc, $x, $y + ($h - $descH) / 2, 0, 0, $descW, $descH);
        
        $x += $descW + 2;
        $w -= $descW + 2;
        $ccBox = $img->colAllocate($cBox);
        imagefilledarc($img->getRawImage(), $x + $h / 2, $y + $h / 2, $h, $h, 90, 270, $ccBox, IMG_ARC_PIE);
        imagefilledrectangle($img->getRawImage(), $x + $h / 2, $y, $x + $w - $h / 2, $y + $h, $ccBox);
        imagefilledarc($img->getRawImage(), $x + $w - $h / 2, $y + $h / 2, $h, $h, 270, 90, $ccBox, IMG_ARC_PIE);
        $img->renderAlignedText(TextImageRenderer::$ANCHOR_MID_LEFT, $x + $h / 2 + 4, $y + $h / 2, $h / 2, $text, $cText);
    }
}
