<?php
namespace fastphp;

// ��ܸ�Ŀ¼
defined('CORE_PATH') or define('CORE_PATH', __DIR__);

/**
 * fastphp��ܺ���
 */
class Fastphp
{
    // ��������
    protected $config = array();

    public function __construct($config)
    {
        $this->config = $config;
    }

    // ���г���
    public function run()
    {
        spl_autoload_register(array($this, 'loadClass'));
        $this->setReporting();
        $this->removeMagicQuotes();
        $this->unregisterGlobals();
        $this->setDbConfig();
        $this->route();
    }

    // ·�ɴ���
    public function route()
    {

        $controllerName = $this->config['defaultController'];
        $actionName = $this->config['defaultAction'];
        $param = array();

        $url = $_SERVER['REQUEST_URI'];
        // ���?֮�������
        $position = strpos($url, '?');
        $url = $position === false ? $url : substr($url, 0, $position);
        // ɾ��ǰ��ġ�/��
        $url = trim($url, '/');

        if ($url) {
            // ʹ�á�/���ָ��ַ�������������������
            $urlArray = explode('/', $url);
            // ɾ���յ�����Ԫ��
            $urlArray = array_filter($urlArray);

            // ��ȡ��������
            $controllerName = ucfirst($urlArray[0]);

            // ��ȡ������
            array_shift($urlArray);
            $actionName = $urlArray ? $urlArray[0] : $actionName;

            // ��ȡURL����
            array_shift($urlArray);
            $param = $urlArray ? $urlArray : array();
        }

        // �жϿ������Ͳ����Ƿ����
        $controller = 'app\\controllers\\'. $controllerName . 'Controller';

        if (!class_exists($controller)) {
            exit($controller . '������������');
        }
        if (!method_exists($controller, $actionName)) {
            exit($actionName . '����������');
        }

        // ����������Ͳ��������ڣ���ʵ��������������Ϊ��������������
        // �����õ����������Ͳ�����������ʵ������ʱ���������������Ҳ
        // ����ȥ�����Controller����һ��
        $dispatch = new $controller($controllerName, $actionName);

        // $dispatch���������ʵ������Ķ������ǾͿ��Ե������ķ�����
        // Ҳ�����񷽷��д�����������µ�ͬ�ڣ�$dispatch->$actionName($param)
        call_user_func_array(array($dispatch, $actionName), $param);
    }

    // ��⿪������
    public function setReporting()
    {
        if (APP_DEBUG === true) {
            error_reporting(E_ALL);
            ini_set('display_errors','On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
        }
    }

    // ɾ�������ַ�
    public function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map(array($this, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    // ��������ַ���ɾ��
    public function removeMagicQuotes()
    {
        if (get_magic_quotes_gpc()) {
            $_GET = isset($_GET) ? $this->stripSlashesDeep($_GET ) : '';
            $_POST = isset($_POST) ? $this->stripSlashesDeep($_POST ) : '';
            $_COOKIE = isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }

    // ����Զ���ȫ�ֱ������Ƴ�����Ϊ register_globals �Ѿ����ã����
    // �Ѿ����õ� register_globals ָ�����Ϊ on����ô�ֲ�����Ҳ��
    // �ڽű���ȫ���������п��á� ���磬 $_POST['foo'] Ҳ���� $foo ��
    // ��ʽ���ڣ�����д�ǲ��õ�ʵ�֣���Ӱ������е����������� �����Ϣ��
    // �ο�: http://php.net/manual/zh/faq.using.php#faq.register-globals
    public function unregisterGlobals()
    {
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

    // �������ݿ���Ϣ
    public function setDbConfig()
    {
        if ($this->config['db']) {
            define('DB_HOST', $this->config['db']['host']);
            define('DB_NAME', $this->config['db']['dbname']);
            define('DB_USER', $this->config['db']['username']);
            define('DB_PASS', $this->config['db']['password']);
        }
    }

    // �Զ�������
    public function loadClass($className)
    {
        $classMap = $this->classMap();

        if (isset($classMap[$className])) {
            // �����ں��ļ�
            $file = $classMap[$className];
        } elseif (strpos($className, '\\') !== false) {
            // ����Ӧ�ã�applicationĿ¼���ļ�
            $file = APP_PATH . str_replace('\\', '/', $className) . '.php';
            if (!is_file($file)) {
                return;
            }
        } else {
            return;
        }

        include $file;

        // ������Լ����жϣ������Ϊ$className���ࡢ�ӿڻ�����״�����ڣ����ڵ���ģʽ���׳�����
    }

    // �ں��ļ������ռ�ӳ���ϵ
    protected function classMap()
    {
        return array(
            'fastphp\base\Controller' => CORE_PATH . '/base/Controller.php',
            'fastphp\base\Model' => CORE_PATH . '/base/Model.php',
            'fastphp\base\View' => CORE_PATH . '/base/View.php',
            'fastphp\db\Db' => CORE_PATH . '/db/Db.php',
            'fastphp\db\Sql' => CORE_PATH . '/db/Sql.php',
        );
    }
}