$gal=json_decode($scriptProperties['val']);
$id=$scriptProperties['id'];
$key=0;
$out='';
foreach($gal as $item){
    $out.='
        <div class="form-check">
            <input class="form-check-input test-answer" isCorrect="'.$item->is_correct_answer.'" type="radio" name="flexRadioDefault'.$id.'" id="test-answer-'.$id.'-'.$key.'">
            <label class="form-check-label" for="test-answer-'.$id.'-'.$key.'">
                '.$item->Answers.'
            </label>
        </div>
    ';
    $key++;
}
return $out;