<div class="clearfix">
    <head></head>
    <body></body>

    {'!AjaxForm'|snippet:[
    'snippet' => 'FormIt',
    'hooks' => 'email',
    'formSelector' => 'ajax-form-callback',
    'form' => '@FILE chunks/forms/callback.tpl',
    'emailTo' => 'vovanblya@mail.ru',
    'emailFrom_' => ('mail_smtp_user'|option),
    'emailSubject' => 'Обратный звонок',
    'emailTpl' => '@FILE chunks/emails/callback.tpl',
    'validate' => 'name:required,phone:required',
    'validationErrorMessage' => 'В форме содержатся ошибки!',
    'successMessage' => 'Сообщение успешно отправлено',
    ]}

</div>