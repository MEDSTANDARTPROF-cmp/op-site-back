
<div class="mt-4 mt-md-5 question">
    <div class="card border-0 rounded-4 shadow-lg" style="min-height: 200px;">
        <div class="card-body" style="
            display: flex;
            align-content: stretch;
            align-items: stretch;
            flex-wrap: nowrap;
            justify-content: space-evenly;
            flex-direction: column;
            padding: 0;
        ">
            <div id="test-start">
                <div class="card border-0">
                    <div class="card-body d-flex flex-column align-items-center">
                        <div class="fs-5">
                            У вас <b>60</b> минут для прохождения теста
                        </div>
                        <button class="btn btn-primary mt-3 btn-lg" id="test-start-button">Начать</button>
                    </div>
                </div>
            </div>
            <div id="test-end" class="d-none">
                <div class="card">
                    <div class="card-body d-flex flex-column align-items-center">
                        <div>
                            Вы набрали <span id="test-end-ball"></span> баллов
                        </div>
                        <button class="btn btn-outline-primary mt-3">Ок</button>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="test-questions-list" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Список вопросов</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table>
                                <tr>
                                    <th>Вопрос</th>
                                    <th>Результат</th>
                                </tr>
                                [[!getImageList?
                                    &tvname=`testBoxTv`
                                    &tpl=`testBoxListTpl`
                                    &docid=`[[*id]]`
                                ]]
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="test-result-screen" class="d-none d-flex flex-column">
                <div id="test-result-screen-icon" class="align-items-center d-flex flex-column justify-content-center text-center">
                    
                </div>
                <div class="text-center mt-3">
                    Вы набрали <span id="test-result-screen-ball"></span> баллов
                </div>
                <div class="mt-3 text-center">
                    <button class="align-self-center btn btn-outline-primary" onClick="window.location.reload();">Заново</button>
                    <button type="button" class="align-self-center btn btn-outline-secondary text-decoration-none ms-2" data-bs-toggle="modal" data-bs-target="#test-questions-list">
                        Список вопросов
                    </button>
                </div>
            </div>
            <div id="test-questions" class="d-none">
                <div class="border-bottom px-4">
                    <div class="progress-stacked my-3">
                        <div class="progress" role="progressbar" aria-label="Segment two" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="test-succes-progress" style="width: 0%">
                            <div class="progress-bar bg-success"></div>
                        </div>
                        <div class="progress" role="progressbar" aria-label="Segment three" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="test-wrong-progress" style="width: 0%">
                            <div class="progress-bar bg-danger"></div>
                        </div>
                    </div>
                </div>
                <div class="clearfix border-bottom p-0" >
                    <div class="float-md-end float-none pe-4 ps-4 ps-md-2 py-2">
                        Неправильных баллов <span id="test-wrong-ball" class="fw-bold text-danger">0</span> из <span id="test-wrong-ball-total-question">60</span>
                    </div>
                    <div class="ps-4 py-2">
                        Набрано баллов <span id="test-ball" class="fw-bold text-success">0</span> из <span id="test-ball-total-question"></span>
                    </div>
                    
                </div>
                <div class="clearfix border-bottom p-0" >
                    <div class="float-start">
                        <button type="button" class="btn btn-link fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#test-questions-list">
                            Список вопросов
                        </button>
                    </div>
                    <div class="float-end  border-start  px-4 py-2 fw-bold  text-center" id="test-timer"  style="width: 100px">
                        60:00
                    </div>
                    
                    
                    <div class="float-end   px-4 py-2">
                        Вопрос <span id="test-current-question">1</span> из <span id="test-count"></span>
                    </div>
                </div>
                <div class="p-0">
                    [[!getImageList?
                        &tvname=`testBoxTv`
                        &tpl=`testBoxTpl`
                        &docid=`[[*id]]`
                    ]]
                </div>
                
                <div class="border-top clearfix mt-3 px-4 py-3">
                    <button class="btn btn-lg btn-primary float-end ms-1" id="test-confirm-answer" disabled="true">Ответить</button>
                    <button class="btn btn-lg btn-primary float-end ms-1 d-none" id="test-skip-answer">Пропустить</button>
                    <button class="btn btn-lg btn-success me-3 float-end d-none" id="test-repeat-answer">Ещё раз</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        let formatText = (input) => {
            // Переводим весь текст в нижний регистр
            const lowerCaseText = input.toLowerCase();
        
            // Разбиваем текст на предложения по знакам препинания (точка, восклицательный и вопросительный знаки)
            const sentences = lowerCaseText.split(/(?<=[.!?])\s+/);
        
            // Форматируем каждое предложение, делая первую букву заглавной
            const formattedSentences = sentences.map(sentence => {
                return sentence.charAt(0).toUpperCase() + sentence.slice(1);
            });
        
            // Объединяем обратно в текст
            return formattedSentences.join(' ');
        }
        
        // Инициализируем массив для хранения вопросов
        let questions = [];
        let currentQuestion = 0;
        let completeQuestions = [];
        let ball = 0;

        // Ищем все элементы с id, содержащими "test-question-"
        $('[id^="test-question-"]').each(function () {
            // Получаем текст вопроса
            let questionText = $(this).text().trim();
            let questionOriginalText = formatText($(this).find('p').text().trim());
            $(this).find('p').text(questionOriginalText);
    
            // Извлекаем ключ из id, если нужно
            let id = $(this).attr('id');
            let key = id.replace('test-question-', ''); // Удаляем "test-question-" из id
            let correctAnswer = null;
            
            $('[id^="test-answer-' + key + '-"]').each(function () {
                let isCorrect = parseInt($(this).attr('iscorrect'));
                if (isCorrect) {
                    let answerId = $(this).attr('id');
                    answerId = answerId.replace('test-answer-' + key + '-', '');
                    correctAnswer = answerId;
                }
            });
    
            // Добавляем объект вопроса в массив
            questions.push({
                key: key,
                question: questionText,
                correct_answer: correctAnswer,
            });
        });
        
        let timerInterval; // Переменная для хранения идентификатора таймера
        const startTime = 60 * 60; // 60 минут в секундах
        
        // Click start test button
        jQuery(document).on('click', '#test-start-button', () => {
            jQuery(document).find('#test-ball-total-question').text(questions.length);
            jQuery(document).find('#test-wrong-ball-total-question').text(questions.length);
            jQuery(document).find('#test-count').text(questions.length);
            jQuery(document).find('#test-question-' + questions[currentQuestion].key).removeClass('d-none');
            jQuery(document).find('#test-questions').removeClass('d-none');
            jQuery(document).find('#test-start').addClass('d-none');
            
            let timeLeft = startTime;
    
            // Останавливаем предыдущий таймер, если он есть
            if (timerInterval) {
                clearInterval(timerInterval);
            }
    
            // Обновляем отображение времени сразу после нажатия
            $('#test-timer').text(formatTime(timeLeft));
    
            // Запускаем таймер
            timerInterval = setInterval(function () {
                timeLeft--;
    
                // Обновляем отображение времени
                $('#test-timer').text(formatTime(timeLeft));
    
                // Останавливаем таймер, когда время достигает 0
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    //alert("Время истекло!");
                    setResultScreen(1);
                }
            }, 1000); // Обновление каждую секунду
        })
        
        jQuery(document).on('change', '.test-answer', () => {
            jQuery(document).find('#test-confirm-answer').prop('disabled', false);
        })
        
        // Click to select question
        jQuery('.test-question-link').on('click', (e) => {
            jQuery(document).find('#test-question-' + questions[currentQuestion].key).addClass('d-none');
            
            currentQuestion = parseInt(jQuery(e.target).attr('answerid')) - 1;
            
            jQuery(document).find('#test-current-question').text(currentQuestion + 1);
            jQuery(document).find('#test-question-' + questions[currentQuestion].key).removeClass('d-none');
            
            // If question is not answered
            if (!completeQuestions[currentQuestion]) {
                jQuery(document).find('#test-confirm-answer').prop('disabled', true);
            } else {
                jQuery(document).find('#test-confirm-answer').prop('disabled', false);
            }
            
            jQuery(document).find('#test-confirm-answer').removeClass('d-none');
            jQuery(document).find('#test-skip-answer').addClass('d-none');
            jQuery(document).find('#test-repeat-answer').addClass('d-none');
        })
        
        jQuery(document).on('click', '#test-skip-answer', () => {
            jQuery(document).find('#test-question-' + questions[currentQuestion].key).addClass('d-none');
            if (!completeQuestions[currentQuestion + 1] && questions[currentQuestion + 1]) {
                // If next question is not answered and exist
                currentQuestion++;
            } else if (!questions[currentQuestion + 1]) {
                // If next question is not exist
                let isNotAnsweredQuestion = false;
                for (var index = 0; index < questions.length; index++) {
                    if (!completeQuestions[index]) {
                        currentQuestion = index;
                        isNotAnsweredQuestion = true;
                        
                        break;
                    }
                }
                
                if (!isNotAnsweredQuestion) {
                    setResultScreen();
                    
                    return;
                }
            } else {
                let isNotAnsweredQuestion = false;
                for (var index = 0; index < questions.length; index++) {
                    if (!completeQuestions[index]) {
                        currentQuestion = index;
                        isNotAnsweredQuestion = true;
                        
                        break;
                    }
                }
                
                if (!isNotAnsweredQuestion) {
                    setResultScreen();
                    
                    return;
                }
            }
            jQuery(document).find('#test-question-' + questions[currentQuestion].key).removeClass('d-none');
            jQuery(document).find('#test-current-question').text(currentQuestion + 1);
            jQuery(document).find('#test-ball').text(ball);
            
            jQuery(document).find('#test-confirm-answer').prop('disabled', true);
            jQuery(document).find('#test-confirm-answer').removeClass('d-none');
            jQuery(document).find('#test-skip-answer').addClass('d-none');
            jQuery(document).find('#test-repeat-answer').addClass('d-none');
        });
        
        jQuery(document).on('click', '#test-repeat-answer', () => {
            jQuery(document).find('#test-confirm-answer').prop('disabled', true);
                jQuery(document).find('#test-confirm-answer').removeClass('d-none');
                jQuery(document).find('#test-skip-answer').addClass('d-none');
                jQuery(document).find('#test-repeat-answer').addClass('d-none');
                jQuery(document).find('#test-wrong-result-' + questions[currentQuestion].key).addClass('d-none');
            
            $('[id^="test-answer-' + questions[currentQuestion].key + '-"]').each(function () {
                let questionText = $(this).text().trim();
                
                // Извлекаем ключ из id, если нужно
                let id = $(this).attr('id');
                let key = id.replace('test-answer-' + questions[currentQuestion].key + '-', ''); // Удаляем "test-answer-" из id
        
                if ($(this).is(':checked')) {
                    
                    if (questions[currentQuestion].correct_answer !== key) {
                        jQuery(this).closest('.form-check').find('.form-check-label').addClass('text-danger');
                    }
                }
            });
        });
        
        // Click answer button
        jQuery(document).on('click', '#test-confirm-answer', () => {
            let answerForCurrentQuestionIsTrue = false;
            $('[id^="test-answer-' + questions[currentQuestion].key + '-"]').each(function () {
                let questionText = $(this).text().trim();
                
                // Извлекаем ключ из id, если нужно
                let id = $(this).attr('id');
                let key = id.replace('test-answer-' + questions[currentQuestion].key + '-', ''); // Удаляем "test-answer-" из id
        
                if ($(this).is(':checked')) {
                    // Добавляем объект вопроса в массив
                    completeQuestions[currentQuestion] = {
                        answer: key,
                    };
                    
                    if (questions[currentQuestion].correct_answer === key) {
                        answerForCurrentQuestionIsTrue = true;
                    }
                }
            });
            updateBall();
            
            if (answerForCurrentQuestionIsTrue) {
                answerResult = '+';
            } else {
                answerResult = '-';
            }
            jQuery(document).find('#question-list-answer-' + questions[currentQuestion].key).text(answerResult);
            jQuery(document).find('#test-ball').text(ball);
            
            jQuery(document).find('#test-confirm-answer').prop('disabled', true);
            
            updateProgress();
            
            if (!answerForCurrentQuestionIsTrue) {
                jQuery(document).find('#test-confirm-answer').addClass('d-none');
                jQuery(document).find('#test-skip-answer').removeClass('d-none');
                jQuery(document).find('#test-repeat-answer').removeClass('d-none');
                jQuery(document).find('#test-wrong-result-' + questions[currentQuestion].key).removeClass('d-none');
                
                return;
            } else {
                jQuery(document).find('#test-wrong-result-' + questions[currentQuestion].key).addClass('d-none');
            }
            
            jQuery(document).find('#test-question-' + questions[currentQuestion].key).addClass('d-none');
            if (!completeQuestions[currentQuestion + 1] && questions[currentQuestion + 1]) {
                // If next question is not answered and exist
                currentQuestion++;
            } else if (!questions[currentQuestion + 1]) {
                // If next question is not exist
                let isNotAnsweredQuestion = false;
                for (var index = 0; index < questions.length; index++) {
                    if (!completeQuestions[index]) {
                        currentQuestion = index;
                        isNotAnsweredQuestion = true;
                        
                        break;
                    }
                }
                
                if (!isNotAnsweredQuestion) {
                    setResultScreen();
                    
                    return;
                }
            } else {
                let isNotAnsweredQuestion = false;
                for (var index = 0; index < questions.length; index++) {
                    if (!completeQuestions[index]) {
                        currentQuestion = index;
                        isNotAnsweredQuestion = true;
                        
                        break;
                    }
                }
                
                if (!isNotAnsweredQuestion) {
                    setResultScreen();
                    
                    return;
                }
            }
            jQuery(document).find('#test-question-' + questions[currentQuestion].key).removeClass('d-none');
            jQuery(document).find('#test-current-question').text(currentQuestion + 1);
        })
        
        let setResultScreen = (type = 0) => {
            let timeEnd = `<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-clock text-black-50" viewBox="0 0 16 16">
                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
            </svg>`;
            let testSuccess = `<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
            </svg>`;
            let testWrong = `<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z"/>
                <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z"/>
            </svg>`;
            if (type === 1) {
                jQuery(document).find('#test-result-screen-icon').append(timeEnd);
                jQuery(document).find('#test-result-screen-icon').append(`<p class="mt-2">Время вышло! <br />Перезагрузите страницу, чтобы попробовать ещё раз.</p>`);
            } else {
                ballToPass = Math.round(questions.length * 0.8);
                if (ball < ballToPass) {
                    jQuery(document).find('#test-result-screen-icon').append(testWrong);
                    jQuery(document).find('#test-result-screen-icon').append(`<p class="mt-2">Недостаточно баллов. Попробуйте ещё раз! <br />Перезагрузите страницу, чтобы попробовать ещё раз.</p>`);
                } else {
                    jQuery(document).find('#test-result-screen-icon').append(testSuccess);
                    jQuery(document).find('#test-result-screen-icon').append(`<p class="mt-2">Поздравляем! Вы успешно прошли тест!</p>`);
                }
            }
            
            jQuery(document).find('.test-question-link').each(function() {
                $(this).attr('disabled', true).addClass('text-decoration-none text-black');
            })
            jQuery(document).find('#test-result-screen-ball').text(ball);
            jQuery(document).find('#test-result-screen').removeClass('d-none');
            
            jQuery(document).find('#test-questions').addClass('d-none');
        }
        
        // Update progress bar
        let updateProgress = () => {
            let succesProgressProcent = 0;
            let wrongProgressProcent = 0;
            completeQuestions.map((completeQuestion, index) => {
                if (questions[index].correct_answer === completeQuestion.answer) {
                    succesProgressProcent++;
                } else {
                    wrongProgressProcent++;
                }
            })
            
            succesProgressProcent = Math.round(succesProgressProcent / questions.length * 100 * 100) / 100;
            wrongProgressProcent = Math.round(wrongProgressProcent / questions.length * 100 * 100) / 100;
            jQuery(document).find('#test-succes-progress').attr('aria-valuenow', succesProgressProcent);
            jQuery(document).find('#test-succes-progress').css({'width': succesProgressProcent + '%'});
            jQuery(document).find('#test-wrong-progress').attr('aria-valuenow', wrongProgressProcent);
            jQuery(document).find('#test-wrong-progress').css({'width': wrongProgressProcent + '%'});
        }
        
        let updateBall = () => {
            totalTrueAnswers = 0;
            completeQuestions.map((completeQuestion, index) => {
                if (questions[index].correct_answer === completeQuestion.answer) {
                    totalTrueAnswers++;
                }
            })
            ball = totalTrueAnswers;
        }
        
        let formatTime = (seconds) => {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }
    });
</script>

<style>
ul.form-control-lg.control-test {}

.control-test .form-check {
    padding-top: 8px;
    padding-bottom: 8px;
}

.control-test label.form-check-label {font-size: 13px;margin-top: -9px;}

.control-test input {
    margin-top: 5px;
    border-color: #9e9e9e;
    border-width: 2px;
}
</style>