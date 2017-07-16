<?

class Framework
{
    public static function getRoute($n)
    {
        return explode('?', explode('/', $_SERVER['REQUEST_URI'])[$n])[0];
    }
}