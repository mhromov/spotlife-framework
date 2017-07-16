<?

/* Spotlife.ru index.php
    Class App
*/
App::init();
Class App
{
    /*
     * Конфиг:
     * 1. Путь к приложению
     * 2. Режим разработки     //Показываем Coming Soon template и выводим все ошибки
     *
     * Доп настройки
     * 1. Функция autoload
     *
     * Схема работы:
     * - App::init ();
     * -    function __autoload($className)
     * -    if (self::$configs['debug'])
     * -    Router::get(PAGE);
     * -        Router::startController($page);
     * -        Router::showView($page, $array);
     * -        Router::async($page);
     */
    public static $configs = [
        'indexroot' => __DIR__,
        'debug' => true
    ];
    public static $domainName = 'http://example.com';
    private static $DevPassword = '';
    public static $startURL = '/';

    public static function init()
    {
        // Настраиваем простейшую autoload функцию
        function __autoload($className)
        {
            App::autoload($className);
        }

        /*
         *   Если включен debug, показывается Coming Soon шаблон и включается вывод всех ошибок
         * */
        session_start();
        if (self::debugging()) exit;

        self::plugin('app/router.php');
        /*
         *   Передаем всю остальную работу в руки рутера
         * */
        Router::get();
        /*
         *  Debug stuff
         * */
//        if (isset($_GET['debug'])) self::printDebugStuff($time, $startMemory);
        return;
    }

    public static function autoload($className)
    {
        if (strpos($className, 'Controller') !== false) {
            $className = str_replace('Controller', '', $className);
            self::plugin('app/controllers/' . strtolower($className) . '.php');
        } else {
            self::plugin('app/classes/' . strtolower($className) . '.php');
        }
    }

    public static function plugin($file, $array = [])
    {
        include(self::$configs['indexroot'] . '/' . $file);
    }

    public static function debugging()
    {
        $password = self::$DevPassword;
        if ($password!='') {
            if (self::$configs['debug']) {
                if ($_GET['dev'] == $password) {
                    setcookie('dev', $password, time() + 3600 * 24 * 7, '/', 's2025.ru');
                    $_COOKIE['dev'] = $password;
                }
                if ($_COOKIE['dev'] != $password) {
                    self::plugin('app/views/comingsoon.php');
                    return true;
                }
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
//            return true;
            }
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        return false;
    }

    public static function printDebugStuff($time, $startMemory)
    {
        $time = microtime(true) - $time;
        $memory = memory_get_usage() - $startMemory;
        echo "<script>console.log('$time'+'s');</script>";
        echo "<script>console.log('$memory'+'bytes');</script>";
        $info = "$(document).ready(function () { document.getElementById('debug_content').innerHTML+='<br>'+$time+' s<br>'+$memory+' bytes';})";
        echo "<script>$info</script>";
        echo '<script>console.log("' . $info . '")</script>';
    }
}