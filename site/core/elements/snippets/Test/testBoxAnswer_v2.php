$gal=json_decode($scriptProperties['val']);
$id=$scriptProperties['id'];
$key=0;
$out='';
$countCorrectAnswers = 0;
foreach($gal as $item){
    if($item->is_correct_answer){
        $countCorrectAnswers++;
    }
}
foreach($gal as $item){
    $type = 'radio';
    if($countCorrectAnswers > 1){
        $type = 'checkbox';
    }
    $out.='
        <div class="form-check">
            <input class="form-check-input test-answer" isCorrect="'.$item->is_correct_answer.'" type="'.$type.'" name="flexRadioDefault'.$id.'" id="test-answer-'.$id.'-'.$key.'">
            <label class="form-check-label" for="test-answer-'.$id.'-'.$key.'">
                '.$item->Answers.'
            </label>
        </div>
    ';
    $key++;
}
return $out;