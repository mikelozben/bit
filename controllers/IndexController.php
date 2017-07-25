<?php
namespace Controllers;

use Models\User;

/**
 * Index Controller.
 */
class IndexController extends BaseController 
{
    /**
     * Controller default action
     * @var string
     */
    protected $defaultAction = 'login';
    
    /**
     * Common data for views
     * @var array
     */
    public $viewCommonData = [ 
        'strTitle'              => 'BIT Test',
        'strDescription'        => 'BIT Test Desc',
        'urlPrefix'             => '',
        'arrHeaderCssScripts'   => [],
        'arrHeaderJsScripts'    => []
    ];
    
    /**
     * Constructor.
     * @param array $config
     */
    public function __construct(array $config) 
    {
        parent::__construct($config);
        $this->viewCommonData['urlPrefix'] = $config['urlPrefix'];
        $this->viewCommonData['arrHeaderCssScripts'] = [
            $this->config['baseUrl'] . 'css/common.css'
        ];
    }
    
    /**
     * {@inheritDoc} As default process 'login' action
     * for unsigned users, and 'account' action for signed ones.
     * 
     * @param string|null $strRoute
     * @return string
     * @throws \Exception
     */
    public function processRoute($strRoute)
    {
        $strTargetMethodName = $this->getAction($strRoute);
        
        if ((null === $strRoute) && ($strTargetMethodName === $this->defaultAction) && (null !== $this->user)) {
            $strTargetMethodName = $this->getAction('account');
        }
        
        if (method_exists($this, $strTargetMethodName)) {
            return $this->{$strTargetMethodName}();
        }
        throw new \Exception("Failed to find action for route \"{$strRoute}\"");
    }
    
    /**
     * Login action.
     * @return string
     */
    public function actionLogin()
    {
        $isSigned = (null !== $this->user);
        $errorMsg = null;
        
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            
            if (!is_string($username) || (0 >= strlen($username))) {
                $errorMsg = 'Username invalid';
            } elseif (!is_string($password) || (0 >= strlen($password))) {
                $errorMsg = 'Password invalid';
            } else {
                $objUser = User::loadByUsername($username);
                if (null === $objUser) {
                    $errorMsg = 'User not found';
                } else {
                    if (!$objUser->verifyPassword($password)) {
                        $errorMsg = 'Username or password invalid';
                    } else {
                        User::saveCurrentUser($objUser);
                        $this->redirect('account');
                    }
                }
            }
        }
        session_write_close();
        
        return $this->renderView('login', array_merge($this->viewCommonData, [
            'isSigned' => $isSigned,
            'errorMsg' => $errorMsg
        ]));
    }
    
    /**
     * Logout action.
     */
    public function actionLogout()
    {
        if (null !== $this->user) {
            User::saveCurrentUser(null);
        }
        session_write_close();
        
        $this->redirect('login');
    }
    
    /**
     * Account action.
     * 
     * @return string
     */
    public function actionAccount()
    {
        if (null === $this->user) {
            $this->redirect('login');
        }
        
        $currentBalance = $this->user->balance;
        $errorMsg = null;
        
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            if (is_numeric($amount)) {
                $amount = floatval($amount);
            }
            if (is_float($amount)) {
                try {
                    $currentBalance = $this->user->makeTransaction($amount);
                } catch (\Exception $ex) {
                    $errorMsg = $ex->getMessage();
                }
            } else {
                $errorMsg = 'Failed to process transaction: amount invalid';
            }
        }
        session_write_close();
        
        return $this->renderView('account', array_merge($this->viewCommonData, [
            'username'          => $this->user->username,
            'currentBalance'    => $currentBalance,
            'errorMsg'          => $errorMsg
        ]));
    }
}
