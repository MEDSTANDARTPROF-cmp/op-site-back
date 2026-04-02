<head></head>
<body></body>

{'!Login'|snippet:[
'redirectLogin' => 113,
'postHooks' => 'hook.login',
]}


{switch $.request.redirect}
{case 'login', 'logout'}
{var $redirect = $.request.id|url}
{default}
{var $redirect = ''}
{/switch}

{if $redirect?}
    <script>
        window.parent.location.href= "{$redirect}";
    </script>
{/if}