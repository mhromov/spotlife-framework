<?

Class MainController
{
    public static function error($error = [])
    {
        $array = [
            'title' => '404',
            'text' => $error['error'],
            'views' => [
                'index.php'
            ]
        ];
        return $array;
    }

    public static function index()
    {
        return ['views' => ['index.php'], 'title' => 'Spotlife framework', 'text' => 'Example page'];
    }

}