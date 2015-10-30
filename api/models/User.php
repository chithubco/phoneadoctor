<?php

namespace app\models;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\web\IdentityInterface;


/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $firstname
 * @property string $lastname
 * @property string $phone
 * @property string $email
 * @property string $birth_date
 * @property string $location
 * @property string $activationKey
 * @property integer $createtime
 * @property integer $lastvisit
 * @property integer $status
 * @property string $role
 */
class User extends \yii\db\ActiveRecord  implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createtime', 'lastvisit', 'status'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['password', 'activationKey'], 'string', 'max' => 128],
            [['firstname', 'lastname'], 'string', 'max' => 255],
            [['phone', 'email', 'birth_date', 'location', 'role'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'phone' => 'Phone',
            'email' => 'Email',
            'birth_date' => 'Birth Date',
            'location' => 'Location',
            'activationKey' => 'Activation Key',
            'createtime' => 'Createtime',
            'lastvisit' => 'Lastvisit',
            'status' => 'Status',
            'role' => 'Role',
        ];
    }
    /************************************************************************/
     /** INCLUDE USER LOGIN VALIDATION FUNCTIONS**/

     /**
     * @inheritdoc
     */

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    /**

     * @inheritdoc

     */

/* modified */

    public static function findIdentityByAccessToken($token, $type = null)
    {
          return static::findOne(['access_token' => $token]);
    }
    
  public function signup()
    {         

        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->firstname = $this->firstname;
            $user->lastname  = $this->lastname;
            $user->phone  = $this->phone;
            $user->email = $this->email;
            $user->birth_date = $this->birth_date;
            $user->location   = $this->location;
            $user->generateAuthKey();
            $user->createtime = unixtojd(time());
            $user->status = 1;      
            $user->role = $this->role;      
            $user->setPassword($this->password);
           
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }    

  

/* removed

    public static function findIdentityByAccessToken($token)

    {

        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');

    }

*/

    /**
     * Finds user by username
     *
     * @return static|null

     */

    public static function findByUsername($username)

    {
         /*$pass = Yii::$app->security->generatePasswordHash('admin');       
         return $pass;*/
        return static::findOne(['username' => $username]);
    }


    public static function findByPasswordResetToken($token)

    {

        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];

        $parts = explode('_', $token);

        $timestamp = (int) end($parts);

        if ($timestamp + $expire < time()) {

            // token expired

            return null;

        }

 

        return static::findOne([

            'password_reset_token' => $token

        ]);

    }

 


    public function getId()

    {

        return $this->getPrimaryKey();

    }
 

    public function getAuthKey()

    {

        return $this->activationKey;

    }

    public function validateAuthKey($authKey)

    {

        return $this->getAuthKey() === $authKey;

    }

 

    public function validatePassword($password)

    {
        //return $this->password === sha1($password);
        return $this->password = Yii::$app->security->generatePasswordHash($password); 

    }

    public function setPassword($password)

    { 
       $this->password = Yii::$app->security->generatePasswordHash($password); 

    }

 

    public function generateAuthKey()

    {

        $this->activationKey = Yii::$app->security->generateRandomKey();

    }

    /**
     * Generates new password reset token
     */

    public function generatePasswordResetToken()

    {

        $this->password_reset_token = Yii::$app->security->generateRandomKey() . '_' . time();

    }


    public function removePasswordResetToken()

    {
    $this->password_reset_token = null;

    }

    /** EXTENSION MOVIE **/
    
}
