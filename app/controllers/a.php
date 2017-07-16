<?

class AController extends MainController
{
    public static function index()
    {
        return ['views' => ['article.php'], 'text' => 'Your title is up there', 'title' => Framework::getRoute(2)];
    }
}