<?

Class Router
{
    public static function get()
    {
        // Setting up rout and getting controller and view
        /*
         *      Получаем значение $page
         * */
        $routParts = self::getRoutParts(self::getRoutString());
        // Получаем данные из контроллера
        $array = self::startController($routParts);
        self::showView($array);
        // Что-то делаем после
//        self::async($routParts);
    }

    public static function getRoutParts($rout)
    {
        $parts = explode('/', $rout);
        for ($i = 0, $l = count($parts); $i < $l; ++$i) {
            if ($parts[$i] == '') {
                array_splice($parts, $i, 1);
            }
        }
        return $parts;
    }

    public static function getRoutString()
    {
        if (isset($_GET['r']) && ($_GET['r'] != '')) {
            $rout = $_GET['r'];
        } else if (isset($argv[1]) && $argv[1] != '') {
            $rout = $argv[1];
        } else {
            $rout = explode('?', str_replace(App::$startURL, '', $_SERVER['REQUEST_URI']))[0];
        }
        return $rout;
    }

    private static function startController($routParts)
    {
        if (!isset($routParts[0])) {
            if (is_callable(['mainController', 'index'])) {
                return mainController::index();
            } else {
                return mainController::error(['error' => 'Wrong root 0 ' . implode('/', $routParts)]);
            }
        }
        if (!isset($routParts[1])) {
            $page = $routParts[0];
            if ($page == '') {
                if (is_callable(['mainController', 'index'])) {
                    return mainController::index();
                } else {
                    return mainController::error(['error' => 'Wrong root 1 ' . implode('/', $routParts)]);
                }
            } else if (is_callable(['mainController', $page])) {
                return mainController::$page();
            } else if (is_callable([$page, 'index'])) {
                return $page::index();
            } else if (is_callable([$page, 'error'])) {
                return $page::error(['error' => 'Wrong root 2 ' . implode('/', $routParts)]);
            } else {
                return mainController::error(['error' => 'Wrong root 3 ' . implode('/', $routParts)]);
            }
        } else {
            $part1 = $routParts[1];
            $part0 = ucfirst($routParts[0]) . 'Controller';
            if ($routParts[1] != '') {
                if (is_callable([$part0, $part1])) {
                    return $part0::$part1();
                } else if (is_callable([$part0, 'index'])) {
                    return $part0::index(['error' => 'Wrong root 4 ' . implode('/', $routParts)]);
                } else {
                    return mainController::error(['error' => 'Wrong root 5 ' . implode('/', $routParts)]);
                }
            } else {
                if (is_callable([$part0, 'index'])) {
                    return $part0::index(['error' => 'Wrong root 6 ' . implode('/', $routParts)]);
                } else {
                    return mainController::error(['error' => 'Wrong root 7 ' . implode('/', $routParts)]);
                }
            }
        }
    }

    private static function showView($array)
    {
        if (isset($array['views']) && is_array($array['views'])) {
            foreach ($array['views'] as $view) {
                App::plugin('app/views/' . $view, $array);
            }
        }
    }

    public static function error($error)
    {
        MainController::error(['error' => $error]);
    }

    private static function async($page)
    {
        if (is_callable(['Controller', $page . '_Async'])) {
            $method = $page . '_Async';
            Controller::$method();
        }
    }
}