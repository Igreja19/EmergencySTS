<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\User; // <-- O teu User.php (que já está correto)

/**
 * Password reset request form
 */
class ForgotPasswordForm extends Model // <--- O nome da Classe tem de ser igual ao nome do Ficheiro
{
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'Não existe nenhum utilizador com este email.'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email Institucional',
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken(); // <-- Função do teu User.php
            if (!$user->save()) {
                return false;
            }
        }

        // Esta é a parte que envia o email.
        return Yii::$app
            ->mailer
            ->compose(
            // Estes são os ficheiros de template do email
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Recuperação de palavra-passe para ' . Yii::$app->name)
            ->send();
    }
}