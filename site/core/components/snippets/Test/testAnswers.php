<?php
$gal=json_decode($scriptProperties['val']);
$id=$scriptProperties['id'];
$out='';
foreach($gal as $item){
    if ($item->is_correct_answer) {
        $out.='
            <li><mark>'.$item->Answers.'</mark></li>
        ';
    } else {
        $out.='
            <li>'.$item->Answers.'</li>
        ';
    }
}
return $out;