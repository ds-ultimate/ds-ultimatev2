@extends('layouts.temp')

@section('content')
@php
    $w = 25;
    $mw = 100;
    
    $fw = $w / $mw;
    
    $lastE = -1;
    for($i = 0; $i <= $mw; $i++) {
        $x = intval($fw * $i);
        $xR = $fw * $i;
        $xe = intval($xR + max($fw - 1, 0));
        $xeR = $x + max($fw - 1, 0);
        if($lastE != $x - 1) echo "Framing error ";
        echo "$i: Rect start: $x / end: $xe / RawX: $xR / RawEnd: $xeR / fieldWidth: $fw  <br>";
        $lastE = $xe;
    }
@endphp
@endsection

@section('js')
@endsection