@extends('layouts.temp')

@section('content')
    @php
        $t1 = time();
        $xStart = 620;
                $yStart = 620;
                $xEnde = 380;
                $yEnde = 380;
                $villagesModel = new \App\Village();
                $villagesModel->setTable('dsUltimate_welt_de164.village_latest');
                $villages = $villagesModel->where('x', '<', $xStart)->where('x', '>', $xEnde)->where('y', '<', $yStart)->where('y', '>', $yEnde)->orderBy('y')->orderBy('x')->get();
            $count = 0;
            $y = $yEnde+1;
            for ($i1 = 0; $i1 <= 238; $i1++){
                    $x = $xEnde+1;
                    $yKoord = $y;
                    echo '<div class="row">';
                        for ($i2 = 0; $i2 <= 238; $i2++){
                            $xKoord = $x;
                            if (isset($villages[$count]) && $villages[$count]->x == $xKoord && $villages[$count]->y == $yKoord) {
                                if ($villages[$count]->owner == 1575966549){
                                    echo '<div class="" style="height: 4px; width: 4px; background-color: #ffff33;" data-toggle="tooltip" data-placement="top" title="" data-original-title="[' .$xKoord.'|'.$yKoord. ']"></div>';
                                }else{
                                    echo '<div class="bg-danger" style="height: 4px; width: 4px;" data-toggle="tooltip" data-placement="top" title="" data-original-title="[' .$xKoord.'|'.$yKoord. ']"></div>';
                                }

                                    $count++;
                            }else{
                                echo '<div class="bg-success" style="height: 4px; width: 4px;"></div>';
                            }
                            $x++;
                        }
                        echo '</div>';
                    $y++;
                }
    @endphp
@endsection
@section('js')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@stop
