<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $phone;
    public $pin;
    public $terms;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['phone', 'pin'], 'required'],
            ['terms', 'required'],
            ['terms','in','range'=>[1], 'strict'=>false, 'message' => 'Please accept the terms and conditions.'],
            //['terms', 'boolean'],
            //['email', 'unique', 'targetClass' => '\common\models\User'],

            // rememberMe must be a boolean value
            //['terms', 'boolean'],
            // password is validated by validatePassword()
            //['password', 'validatePassword'],
        ];
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
            //var_dump($user);exit;
            
            if (!$user || !$user->validatePassword($this->password)) {            
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }
    public function attributeLabels()
    {
        return [
            'terms' => '',
        ];
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
