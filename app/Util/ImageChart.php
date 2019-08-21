<?php

namespace App\Util;

class ImageChart extends PictureRender {
    /*
    imagecolorallocate ( resource $image , int $red , int $green , int $blue ) : int
    imagestring ( resource $image , int $font , int $x , int $y , string $string , int $color ) : bool
    imagestringup ( resource $image , int $font , int $x , int $y , string $string , int $color ) : bool
    imagettfbbox ( float $size , float $angle , string $fontfile , string $text ) : array
    imagettftext ( resource $image , float $size , float $angle , int $x , int $y , int $color , string $fontfile , string $text ) : array

    imagesetthickness ( resource $image , int $thickness ) : bool
    imagefilledellipse ( resource $image , int $cx , int $cy , int $width , int $height , int $color ) : bool
    imageline ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color ) : bool
     */
    private $font;
    private $width;
    private $height;
    private $show_errs;

    public function __construct($font, $dim=null, $show_errs=false) {
        $std_aspect = 35/14;
        if(isset($dim["height"]) && isset($dim["width"])) {
            $img_height = $dim["height"];
            $img_width = $dim["width"];
        } else if(isset($dim["height"])) {
            $img_height = $dim["height"];
            $img_width = $std_aspect * $img_height;
        } else if(isset($dim["width"])) {
            $img_width = $dim["width"];
            $img_height = $img_width / $std_aspect;
        } else {
            $img_height = 140;
            $img_width = $std_aspect * $img_height;
        }
        
        $image = imagecreate(round($img_width, 0), round($img_height, 0));
        if($image === false) die("Error");

        $this -> font = realpath($font);
        $this -> image = $image;
        $this -> width= $img_width;
        $this -> height = $img_height;
        $this -> show_errs = $show_errs;
    }

    public function render($data, $identification_str, $diagram_type, $display_invers) {
        $thick = 1;
        imagesetthickness($this->image,$thick);

        //preparation & settings specification
        ksort($data);
        if(count($data)<= 1) {
            echo "No / Not enough Data";

            imagecolorallocate($this->image, 255, 255, 255); #Hintergrund
            $this->outputPNG();
            die();
        }

        $data_keys = array_keys($data);
        $max_data = max($data);
        $min_data = min($data);
        $count_data = count($data_keys);
        $data_key_range = $data_keys[$count_data - 1] - $data_keys[0];
        $data_range = $max_data - $min_data;
        if($data_range == 0) {
            //force + 2 / - 1
            $max_data+=2;
            $min_data--;
            $data_range = $max_data - $min_data;
        }

        $color_white = imagecolorallocate($this->image, 255, 255, 255); #Hintergrund
        $color_grey = imagecolorallocate($this->image, 210, 210, 210);
        $color_black = imagecolorallocate($this->image, 0, 0, 0);
        $color_blue = imagecolorallocate($this->image, 0, 0, 255);


        $font_size = $this->height / 20;
        $max_width = 0;
        $y_axis_label = array();
        for($i = 0; $i <= 3; $i++) {
            $y_axis_label[$i] = $this -> prepareAmountForRendering($min_data + ($data_range) * $i / 3);
            $box = imagettfbbox($font_size, 0, $this->font, $y_axis_label[$i]);

            if($max_width < $box[2] - $box[0])
                $max_width = $box[2] - $box[0];
        }

        $main_area_top = $this->height * 0.01;
        $box = imagettfbbox($font_size, 0, $this->font, $identification_str);
        $temp = $main_area_top - $box[5];

        imagettftext($this->image, $font_size, 0, $main_area_top * 2, $temp, $color_black, $this->font, $identification_str);

        $box = imagettfbbox($font_size, 0, $this->font, $diagram_type);
        $temp2 = $this->width - $main_area_top * 2 - $box[2];
        imagettftext($this->image, $font_size, 0, $temp2, $temp, $color_black, $this->font, $diagram_type);


        $draw_area_top = $temp + $this->height * 0.08;
        $draw_area_right = $this->width - $this->height * 0.05;
        $draw_area_left = $this->height * 0.05 + $max_width;
        $draw_area_bottom = $this->height * 0.90 - $font_size;

        $horizontal_line_cnt = 12;

        //grid & label drawing
        $thick = max(round($this->height / 200, 0), 1);
        imagesetthickness($this->image,$thick);
        //draw y Axis label
        for($i = 0; $i <= 3; $i++) {
            if(!$display_invers)
                $y = $draw_area_top + ($draw_area_bottom - $draw_area_top) * (1 - $i / 3);
            else
                $y = $draw_area_bottom - ($draw_area_bottom - $draw_area_top) * (1 - $i / 3);

            $this -> renderAmount($font_size, $y_axis_label[$i], $draw_area_left, $y, $this->width * 0.01, $color_black);
        }

        //draw x Axis label
        $cnt = min(intval($data_key_range / (24 * 60 * 60)), 6);

        if($cnt == 0) $cnt = 1;

        for($i = 0; $i < $cnt + 1; $i++) {
            $x_axis_label = intval($i * $data_key_range / (24 * 60 * 60 * $cnt));
            $percent = $x_axis_label * 24 * 60 * 60 / $data_key_range;
            $x = $draw_area_left + ($draw_area_right - $draw_area_left) * $percent;

            $x_axis_label = intval($data_key_range / (24 * 60 * 60)) - $x_axis_label;
            if($x_axis_label == 0)
                $x_axis_label = "Heute";

            $this -> renderXAxis($font_size, $x_axis_label, $x, $draw_area_bottom, $this->width * 0.01, $color_black);
        }

        //vertical lines
        $thick = max(round($this->height / 1000, 0), 1);
        imagesetthickness($this->image,$thick);
        for($i = 12; $i < $count_data; $i+=12) {
            $left = $draw_area_left + ($draw_area_right - $draw_area_left) * ($data_keys[$i] - $data_keys[0]) / $data_key_range;
            imageline($this->image, $left, $draw_area_top, $left, $draw_area_bottom, $color_grey);
        }

        //horizontal lines
        for($i = 0; $i < $horizontal_line_cnt; $i++) {
            $top = $draw_area_top + ($draw_area_bottom - $draw_area_top) * $i / $horizontal_line_cnt;
            imageline($this->image, $draw_area_left, $top, $draw_area_right, $top, $color_grey);
        }

        $thick = max(round($this->height / 200, 0), 1);
        imagesetthickness($this->image,$thick);
        $this->customDrawLine($draw_area_left, $draw_area_bottom, $draw_area_right, $draw_area_bottom, $color_black, $thick); #unten
        $this->customDrawLine($draw_area_left, $draw_area_top, $draw_area_left, $draw_area_bottom, $color_black, $thick); #links

        //data drawing
        $lastX = -1;
        $lastY = -1;
        for($i = 0; $i < $count_data; $i++) {
            $x = $draw_area_left + ($draw_area_right - $draw_area_left) * ($data_keys[$i] - $data_keys[0]) / $data_key_range;
            $percent = ($data[$data_keys[$i]] - $min_data) / $data_range;

            if(!$display_invers)
                $y = $draw_area_bottom - ($draw_area_bottom - $draw_area_top) * $percent;
            else
                $y = $draw_area_top + ($draw_area_bottom - $draw_area_top) * $percent;

            if($lastX >= 0 && $lastY >= 0) {
                //draw only in second run
                $this->customDrawLine($lastX, $lastY, $x, $y, $color_blue, $thick);
            }
            $lastX = $x;
            $lastY = $y;
        }
    }

    private function prepareAmountForRendering($amount) {
        $suffix = "";

        if($amount >= 10000) {
            $suffix = "k";
            $amount /= 1000;
        }
        if($amount >= 10000) {
            $suffix = "M";
            $amount /= 1000;
        }
        if($amount >= 10000) {
            $suffix = "Mrd";
            $amount /= 1000;
        }

        $round_nums = 0;
        if($amount - intval($amount) > 0.01) {
            if($amount >= 1000) {
            } else if($amount >= 100) {
                $round_nums = 1;
            } else if($amount >= 10) {
                $round_nums = 2;
            } else {
                $round_nums = 3;
            }
        }

        return number_format($amount, $round_nums, ",", "") . $suffix;
    }

    private function renderAmount($font_size, $amount, $x, $y, $line_lenght, $colour) {
        $box = imagettfbbox($font_size, 0, $this->font, $amount);

        $txtX = $x - $line_lenght - $box[2];
        $txtY = $y - ($box[5] + $box[3]) / 2;

        imageline($this->image, $x - $line_lenght, $y, $x, $y, $colour);
        imagettftext($this->image, $font_size, 0, $txtX, $txtY, $colour, $this->font, $amount);
    }

    private function renderXAxis($font_size, $amount, $x, $y, $line_lenght, $colour) {
        $box = imagettfbbox($font_size, 0, $this->font, $amount);

        $txtX = $x - ($box[0] + $box[2]) / 2;
        $txtY = $y + $line_lenght + 2 - $box[5];

        if($txtX + $box[2] + $this->width * 0.01 > $this->width) {
            //txt would be rendered outside of sceen --> ignore center rule
            $txtX = $this->width * 0.99 - $box[2];
        }

        /*
        imageline($this->image, $txtX + $box[0], $txtY + $box[1], $txtX + $box[2], $txtY + $box[3], $colour);
        imageline($this->image, $txtX + $box[2], $txtY + $box[3], $txtX + $box[4], $txtY + $box[5], $colour);
        imageline($this->image, $txtX + $box[4], $txtY + $box[5], $txtX + $box[6], $txtY + $box[7], $colour);
        imageline($this->image, $txtX + $box[6], $txtY + $box[7], $txtX + $box[0], $txtY + $box[1], $colour);
        */

        imageline($this->image, $x, $y, $x, $y+$line_lenght, $colour);
        imagettftext($this->image, $font_size, 0, $txtX, $txtY, $colour, $this->font, $amount);
    }

    private function customDrawLine($x1, $y1, $x2, $y2, $colour, $thickness) {
        imagefilledellipse ($this->image, $x1, $y1, $thickness, $thickness, $colour);
        imagefilledellipse ($this->image, $x2, $y2, $thickness, $thickness, $colour);
        imageline($this->image, $x1, $y1, $x2, $y2, $colour);
    }
}