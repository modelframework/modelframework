<?php

namespace ModelFramework\AuthService;

use ModelFramework\BaseService\ServiceLocatorAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\GatewayService\MongoGateway;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\ModelService\ModelService;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Session\Container; // Use the session with Zend libraries
use Wepo\Model\Role;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\AclInterface;

/**
 * Description of AuthService
 *
 * @author PROG-3
 */
class AuthService
    implements AuthServiceInterface, ServiceLocatorAwareInterface, ModelServiceAwareInterface, GatewayServiceAwareInterface, AclInterface
{
    use ServiceLocatorAwareTrait, ModelServiceAwareTrait, GatewayServiceAwareTrait;

    const NONE = 0;
    const ALL  = 1;
    const OWN  = 2;

    private $_acl = null;
    private $_mainUser = null;
    private $_user = null;
    private $_role = null;
    private $_session = null;

    public function hasResource($resource)
    {
        return true;
    }

    public function init()
    {
        $this->_session = new Container('auth');

        $this->_user     = $this->getModel('User');
        $this->_mainUser = $this->getModel('MainUser');
        $this->_role     = $this->getModel('Role');
        $this->_role->exchangeArray([ 'id' => Role::GUEST, 'role' => Role::GUESTNAME ]);
        $this->checkAuth();

        return $this;
    }

    /**
     * @param string $gatewayName
     *
     * @return MongoGateway
     */
    public function getGateway($gatewayName)
    {
        return $this->getGatewayService()->get($gatewayName);
    }

    /**
     * @param string $modelName
     *
     * @return ModelService
     */
    public function getModel($modelName)
    {
        return $this->getModelService()->get($modelName);
    }

    public function setAcl(Acl $acl)
    {
        $this->_acl = $acl;

        return $this;
    }

    public function setUser(DataModelInterface $user)
    {
        $this->_user = $user;
        if ($user !== null && $user instanceof DataModelInterface) {
            $this->_session->offsetSet('user_id', $user->_id);
        } else {
            $this->_session->offsetSet('user_id', 0);
        }

        return $this;
    }

    public function setMainUser(DataModelInterface $mainUser)
    {
        $this->_mainUser = $mainUser;
        if ($mainUser !== null && $mainUser instanceof DataModelInterface) {
            $this->_session->offsetSet('main_user_id', $mainUser->_id);
        } else {
            $this->_session->offsetSet('main_user_id', 0);
        }

        $this->checkAuth();

        return $this;
    }

    public function initAcl($rules)
    {
        $this->_acl = new Acl();
        foreach ($rules[ 'roles' ] as $role => $extends) {
            if (is_int($role) && is_string($extends)) {
                $role    = $extends;
                $extends = null;
            }
            $this->_acl->addRole(new \Zend\Permissions\Acl\Role\GenericRole($role), $extends);
            //var_dump('addRole('.var_export($role, true).', '.var_export($extends, true));
        }
        //var_dump($rules['allows']);
        foreach ($rules[ 'allows' ] as $role => $allow) {
            foreach ($allow as $controller => $actions) {
                if (is_int($controller) && is_string($actions)) {
                    $controller = $actions;
                    $actions    = null;
                }
                if (!$this->_acl->hasResource($controller)) {
                    $this->_acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($controller));
                }
                $this->_acl->allow($role, $controller, $actions);
                //var_dump('allow('.var_export($role, true).', '.var_export($controller, true).', '.var_export($actions, true));
            }
        }

        foreach ($rules[ 'dataAccess' ] as $role => $dataAllow) {
            foreach ($dataAllow as $dataType => $mode) {
                if (!$this->_acl->hasResource($dataType)) {
                    $this->_acl->addResource($dataType);
                }

                $this->_acl->allow($role, $dataType, $mode);
            }
        }
    }

    public function checkAuth()
    {
        // If the session does not exist
        // at the beginning of this variable is equal to zero
        if (isset($this->_session->main_user_id) && $this->_session->main_user_id) {
            $this->_mainUser = $this->getGateway('MainUser')->get($this->_session->main_user_id);
            $company         = $this->getGateway('MainCompany')->get($this->_mainUser->company_id);

//                $cacheHandler = $this -> _serviceManager -> get('Wepo\Lib\CacheService');
//                $cacheHandler->setCompany($company->id());
            $dbs = $this->getGateway('MainDb')->find([ 'company_id' => $company->_id ]);

            if ($dbs->count() > 0) {
                $db         = $dbs->current();
                $connection = $this->getServiceLocator()->get('wepo_company')->getDriver()->getConnection();
                $connection->setConnectionParameters($db->toArray());
                $this->_user =
                    $this->getGateway('User')->findOne([ 'main_id' => $this->_mainUser->_id ]);
                $this->_role = $this->getGateway('Role')->get($this->_user->role_id);
                if (strlen($this->_user->theme)) {
                    $config = $this->getServiceLocator()->get('Config');
                    if (is_array($config) && isset($config[ 'view_manager' ])) {
                        $config = $config[ 'view_manager' ];
                        if (is_array($config) && isset($config[ 'template_path_stack' ])) {
                            $config[ 'template_path_stack' ][ 'wepo' ] =
                                __DIR__.'/../../../../../../module/Wepo/themes/'.$this->_user->theme;

                            $config[ 'template_path_stack' ]['partial'] =
                                __DIR__.'/../../../../../../module/Wepo/themes/'.$this->_user->theme.'/wepo';
//                                    __DIR__ . '/../themes/view/wepo',
                        }

//                        prn(__DIR__ . '/../../../view/' . 'wepo1', $config[ 'template_path_stack' ]['wepo']);

                        $zZfcTwigLoaderTemplatePathStack =
                            $this->getServiceLocator()->get('ZfcTwigLoaderTemplatePathStack');
                        $paths                           = $zZfcTwigLoaderTemplatePathStack->getPaths();
                        $zZfcTwigLoaderTemplatePathStack->setPaths($config[ 'template_path_stack' ]);
//                        prn($zZfcTwigLoaderTemplatePathStack, $paths );
                    }
                }
//                    $cacheHandler->setUser($this -> _user -> id() );
            } else {
                throw new Exception('Could not connect to db');
            }
        }
    }

    public function isGranted($resource = null, $privilege = null)
    {
        return $this->_acl->isAllowed($this->getRole(), $resource, $privilege);
    }

    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        return $this->_acl->isAllowed($role, $resource, $privilege);
    }

    public function getPermission($resource)
    {
        if (null === $this->_acl) {
            return false;
        }
        if (!$this->_acl->hasResource($resource)) {
            return false;
        }
        if (!$this->_acl->hasRole($this->getRole())) {
            return false;
        }
        if ($this->isGranted($resource, 'all')) {
            return self::ALL;
        } elseif ($this->isGranted($resource, 'own')) {
            return self::OWN;
        } else {
            return self::NONE;
        }
    }

    public function getMainUser()
    {
        return $this->_mainUser;
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function getAcl()
    {
        return $this->_acl;
    }

    public function getRole()
    {
        return $this->_role->role;
    }
}
