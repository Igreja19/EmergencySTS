<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    const SCENARIO_BACKEND  = 'backend';
    const SCENARIO_FRONTEND = 'frontend';

    public $username;
    public $password;
    public $rememberMe = true;
    public bool $acessoRestrito = false;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_BACKEND] = ['username', 'password', 'rememberMe'];
        $scenarios[self::SCENARIO_FRONTEND] = ['username', 'password', 'rememberMe'];

        return $scenarios;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();

        if (!$user) {
            return false;
        }

        // Roles permitidas no BACKEND
        $allowedRoles = ['admin', 'medico', 'enfermeiro'];

        $auth = Yii::$app->authManager;
        $hasAccess = false;

        foreach ($allowedRoles as $role) {
            if ($auth->checkAccess($user->id, $role)) {
                $hasAccess = true;
                break;
            }
        }

        if ($this->scenario === self::SCENARIO_BACKEND && !$hasAccess) {
            $this->acessoRestrito = true;

            $this->clearErrors();

            $this->addError('username', 'Não tem permissões para aceder ao backoffice.');

            return false;
        }

        // Roles permitidas no FRONTEND
        if ($this->scenario === self::SCENARIO_FRONTEND) {
            // Verifica se tem a role 'paciente'
            if (!$auth->checkAccess($user->id, 'paciente')) {
                $this->acessoRestrito = true;

                $this->clearErrors();
                $this->addError('username', 'Apenas pacientes podem aceder a esta área.');

                return false;
            }
        }

        return Yii::$app->user->login(
            $user,
            $this->rememberMe ? 3600 * 24 * 30 : 0
        );
    }


    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
