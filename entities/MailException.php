<?php

class MailException extends Exception
{
    public static function checkMail(): string
    {
        if (mail($_POST['email'], 'Ссылка на восстановление пароля.', 'ссылочка') === false)
        {
            throw new MailException('Ссылка на восстановление пароля не отправлена, что-то пошло не так!');
        }

        mail($_POST['email'], 'Ссылка на восстановление пароля.', 'ссылочка');
        return 'Вам на почту отправлена ссылка на восстановление пароля!';
    }
}
