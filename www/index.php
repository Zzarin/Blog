<?php
/*подключение классов через именованную функцию
function myAutoLoader(string $className): void
{
    //var_dump($className); // проверка какие классы(файлы) подключились
    require_once __DIR__ . '/9_Namespace_and_autoloading/src/' . str_replace('\\', '/', $className) . '.php';

}

spl_autoload_register('myAutoLoader', true);
*/

$startTIme = microtime(true);
require __DIR__ . '/../vendor/autoload.php';

try {
//В функцию spl_autoload_register можно и вовсе передать не имя функции, а прямо саму функцию
    /*composer позволяет включить в эту автозагрузку еще и файлы нашего проекта. После этого нужно в наших
    фронт-контроллерах убрать функции автозагрузки, которые мы ранее писали сами.
     * spl_autoload_register(function (string $className) {
        require_once __DIR__ . '/../src/' . str_replace('\\', '/', $className) . '.php';
    });*/

    $route = $_GET['route'] ?? '';
    $routes = require __DIR__ . './../src/routes.php';

//var_dump($route);
//var_dump($routes);

    $isRouteFound = false;
    foreach ($routes as $pattern => $controllerAndAction) {
        preg_match($pattern, $route, $matches);
        if (!empty($matches)) {
            $isRouteFound = true;
            break;
        }
    }

    if (!$isRouteFound) {
        throw new \MyProject\Exceptions\NotFoundException();
    }

    unset($matches[0]);

    $controllerName = $controllerAndAction[0];
    $actionName = $controllerAndAction[1];

    $controller = new $controllerName;
    $controller->$actionName(...$matches);
} catch (\MyProject\Exceptions\DbException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/errors');

    // 500 - код ответа "сайт в данный момент недоступен"
    $view->renderHTML('500.php', ['error' => $e->getMessage()], 500);

} catch (\MyProject\Exceptions\NotFoundException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/errors');
    $view->renderHTML('404.php', ['error' => $e->getMessage()], 404);
} catch (\MyProject\Exceptions\UnauthorizedException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/errors');
    $view->renderHTML('401.php', ['error' => $e->getMessage()], 401);
} catch (\MyProject\Exceptions\ForbiddenException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/errors');
    $view->renderHTML('403.php', ['error' => $e->getMessage()], 403);
}

$endTime = microtime(true);
printf('<div style="text-align: center; padding: 5px">Время генерации страницы: %f</div>', $endTime - $startTIme);

//var_dump($controllerAndAction);
//var_dump($matches);


/*
    if (!empty($matches)) {
        $controller = new \MyProject\Controllers\MainController();
        $controller->sayHello($matches[1]);
        return;
    }

    $pattern = '~^$~';
    preg_match($pattern, $route, $matches);
    var_dump($matches);

    if(!empty($matches)) {
        $controller = new \MyProject\Controllers\MainController();
        $controller->main();
        return;
    }
*/