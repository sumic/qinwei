<?php
/**
 * =======================================================
 * @Description :admin用户模型
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月2日
 * @version: v1.0.0
 *
 */
namespace core\models\mysqldb\admin;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;
use core\behaviors\UpdateBehavior;
/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */

class AdminUser extends ActiveRecord implements IdentityInterface ,RateLimitInterface
{
    const STATUS_ACTIVE    = 10;
    const STATUS_INACTIVE  = 20;
    const STATUS_DELETED   = 30;
    const ROLE_USER = 10;
    
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_REGISTER = 'register';
    
    public $password;
    public $repassword;
    
	# 速度控制  6秒内访问3次，注意，数组的第一个不要设置1，设置1会出问题，一定要
	#大于2，譬如下面  6秒内只能访问三次
	# 文档标注：返回允许的请求的最大数目及时间，例如，[100, 600] 表示在600秒内最多100次的API调用。
	public  function getRateLimit($request, $action){
		 return [6000000, 6];
	}
	# 文档标注： 返回剩余的允许的请求和相应的UNIX时间戳数 当最后一次速率限制检查时。
	public  function loadAllowance($request, $action){
		//return [1,strtotime(date("Y-m-d H:i:s"))];
		//echo $this->allowance;exit;
		 return [$this->allowance, $this->allowance_updated_at];
	}
	# allowance 对应user 表的allowance字段  int类型
	# allowance_updated_at 对应user allowance_updated_at  int类型
	# 文档标注：保存允许剩余的请求数和当前的UNIX时间戳。
	public  function saveAllowance($request, $action, $allowance, $timestamp){
		$this->allowance = $allowance;
		$this->allowance_updated_at = $timestamp;
		$this->save();
	}
	
	 /**
     * @inheritdoc
     */
	# 设置 status  默认  ，以及取值的区间
	 /**
	  * @inheritdoc
	  */
	public function rules()
	{
	    return [
	        [['username', 'email'], 'required'],
	        [['password', 'repassword', 'role'], 'required', 'on' => ['create']],
	        [['username', 'email', 'password', 'repassword'], 'trim'],
	        [['password', 'repassword'], 'string', 'min' => 6, 'max' => 30],
	        // Unique
	        [['username', 'email'], 'unique'],
	        // Username
	        ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/'],
	        ['username', 'string', 'min' => 3, 'max' => 30],
	        // E-mail
	        [['email'], 'string', 'max' => 64],
	        [['face', 'address'], 'string', 'max' => 100],
	        ['email', 'email'],
	        [['age', 'sex'], 'integer'],
	        // Repassword
	        ['repassword', 'compare', 'compareAttribute' => 'password'],
	        //['status', 'default', 'value' => self::STATUS_ACTIVE],
	        ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
	        // Status
	        ['role', 'in', 'range' => array_keys(\Yii::$service->admin->role->getAllRoleArray(false))],
	    ];
	}
	// 验证场景
	public function scenarios()
	{
	    return [
	        'default' => ['username', 'email', 'password', 'repassword', 'status', 'role', 'face'],
	        'create'  => ['username', 'email', 'password', 'repassword', 'status', 'role', 'face'],
	        'update'  => ['username', 'email', 'password', 'repassword', 'status', 'role', 'face', 'address'],
	        'api'     => ['allowance','allowance_updated_at']
	    ];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
	    return [
	        'id' => 'Id',
	        'username' => '管理员账号',
	        'password' => '管理员密码',
	        'email' => '管理员邮箱',
	        'role' => '管理员角色',
	        'auth_key' => '登录密钥',
	        'password_hash' => '密码的哈希值',
	        'password_reset_token' => '重新登录密钥',
	        'status' => '状态',
	        'create_time' => '创建时间',
	        'create_id' => '创建用户',
	        'update_time' => '修改时间',
	        'update_id' => '修改用户',
	        'face' => '头像信息',
	        'last_time' => '上一次登录时间',
	        'last_ip' => '上一次登录的IP',
	        'repassword' => '确认密码',
	    ];
	}
	
    /**
     * @inheritdoc
     */
	# 设置table
    public static function tableName()
    {
        return '{{%admin_user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            UpdateBehavior::className(),
        ];
    }

    /**
    * 新增之前的处理
    * @param  bool $insert 是否是新增数据
    * @return bool 处理是否成功
    */
    public function beforeSave($insert)
    {
        // 新增记录和修改了密码
        if ($this->isNewRecord || (!$this->isNewRecord && $this->password)) {
            $this->setPassword($this->password);
            $this->generateAuthKey();
            $this->generatePasswordResetToken();
        }
        
        return parent::beforeSave($insert);
    }
    
    /**
     * 修改之后的处理
     * @param bool $insert 是否是新增数据
     * @param array $changedAttributes 修改的字段
     */
    public function afterSave($insert, $changedAttributes)
    {
        // 只有在新增或者修改了角色信息，那么才要修改角色信息
        if ($insert || !empty($changedAttributes['role'])) {
            $auth = Yii::$app->authManager;
            $isInsert = true;
            // 修改了角色信息，删除之前的角色信息
            if (!empty($changedAttributes['role'])) {
                // 不删除超级管理员的角色
                $superId = \Yii::$service->admin->user::SUPER_ADMIN_ID;
                if ($this->id != $superId) {
                    $auth->revoke($auth->getRole($changedAttributes['role']), $this->id);
                }
                
                // 没有存在这个角色才新增
                if (in_array($this->id, $auth->getUserIdsByRole($this->role))) {
                    $isInsert = false;
                }
            }
            
            // 添加角色
            if ($isInsert) {
                $auth->assign($auth->getRole($this->role), $this->id);
            }
        }
        
        parent::afterSave($insert, $changedAttributes);
    }
    
    /**
     * 删除之前的处理-验证不能删除超级管理员和自己
     */
    public function beforeDelete()
    {
        if ($this->id == self::SUPER_ADMIN_ID) {
            $this->addError('username', '不能删除超级管理员');
            return false;
        }
        
        if ($this->id == Yii::$app->user->id) {
            $this->addError('username', '不能删除自己');
            return false;
        }
        
        return parent::beforeDelete();
    }
    
    /**
     * 删除之后的处理删除缓存
     */
    public function afterDelete()
    {
        // 移出权限信息
        Yii::$app->authManager->revokeAll($this->id);
        parent::afterDelete(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
	# 通过id 找到identity
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
	# 通过access_token 找到identity
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }
	# 生成access_token
	public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
	# 此处是忘记密码所使用的
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
