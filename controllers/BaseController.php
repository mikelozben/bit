<?php
namespace Controllers;

use Components\MysqlConnector;
use Models\User;

/**
 * Base controller.
 */
class BaseController 
{
    /**
     * Database connection
     * @var resource
     */
    protected $connection = null;
    
    /**
     * Currently signed in user or null
     * @var Models/User|null
     */
    protected $user = null;
    
    /**
     * Default action
     * @var string
     */
    protected $defaultAction = 'index';
    
    /**
     * Views folder
     * @var string
     */
    protected $viewsFolder = '.';
    
    /**
     * Config data
     * @var array
     */
    protected $config = [];
    
    /**
     * Class constructor.
     * @param array $config
     */
    public function __construct(array $config) 
    {
        $this->connection = MysqlConnector::get($config);
        $this->user = User::loadCurrentUser();
        
        $this->config = $config;
        $this->viewsFolder = $this->config['baseDir'] . '/views/';
    }
    
    /**
     * Provides action name by given route.
     * @param string $strRoute
     * @return string
     */
    public function getAction($strRoute)
    {
        $strTargetMethodName = 'action' . ucfirst($this->defaultAction);
        if (is_string($strRoute)) {
            $strTargetMethodName = 'action' . ucfirst($strRoute);
        }

        return $strTargetMethodName;
    }
    
    /**
     * Process given route.
     * 
     * @param string|null $strRoute
     * @return string
     * @throws \Exception
     */
    public function processRoute($strRoute)
    {
        $strTargetMethodName = $this->getAction($strRoute);
        
        if (method_exists($this, $strTargetMethodName)) {
            return $this->{$strTargetMethodName}();
        }
        throw new \Exception("Failed to find action for route \"{$strRoute}\"");
    }
    
    /**
     * Renders page content and returns HTML.
     * 
     * @param string $viewName
     * @param array $viewParams
     * @return string
     * @throws \Exception
     */
    public function renderView($viewName, array $viewParams)
    {
        $pageContent = '';
        $strViewFileURI = "{$this->viewsFolder}{$viewName}.php";
        if (file_exists($strViewFileURI)) {
            ob_start();
            foreach ($viewParams as $variableName => $variableValue) {
                ${$variableName} = $variableValue;
            }
            require($strViewFileURI);
            $pageContent = ob_get_contents();
            ob_end_clean();
        } else {
           throw new \Exception('Failed to find view by name "' . $viewName . '"'); 
        }
        return $pageContent;
    }
    
    /**
     * Redirects to a specific url basing on given route.
     * @param string|null $route
     */
    public function redirect($route = null)
    {
        $strUrl = $this->config['baseUrl'] . 'index.php';
        if (is_string($route)) {
            $strUrl .= '?action=' . $route;
        }
        header('Location: ' . $strUrl);
        exit;

    }
}
