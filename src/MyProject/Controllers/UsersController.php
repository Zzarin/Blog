<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\NotFoundException;
use MyProject\Models\Users\User;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\Users\UserActivationService;
use MyProject\Models\Users\UsersAuthService;
use MyProject\Services\EmailSender;
use MyProject\View\View;

class UsersController extends AbstractController
{

    public function signUp()
    {
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST, $_FILES);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHTML('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }

            if ($user instanceof User) {
                $code = UserActivationService::createActivationCode($user);

                EmailSender::send($user, 'Активация', 'userActivation.php', [
                    'userId' => $user->getId(),
                    'code' => $code
                ]);

                $this->view->renderHTML('users/signUpSuccessful.php');
                return;
            }
        }

        $this->view->renderHTML('users/signUp.php');
    }

    public function activate(int $userId, string $activationCode): void
    {
        $user = User::getById($userId);

        //проверка что запрашивается верная учетная запись, т.е. пользователь в принципе существует
        if ($user === null) {
            $this->view->renderHTML(
                'errors/ActivationError.php',
                [
                    'errorMessage' => 'Пользователь не найден'
                ]
            );
            return;
        }

        if ($user->getActivationStatus()) {     //проверка что учетная запись уже активирована
            $this->view->renderHTML(
                'errors/ActivationError.php',
                [
                    'userName' => $user->getNickname(),
                    'errorMessage' => 'Учетная запись уже активирована'
                ]
            );
            return;
        }

        $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);

        //значит код активации неверен, так как то что ID пользователя верен, мы проверили выше по коду
        if (!$isCodeValid) {
            $this->view->renderHTML(
                'errors/ActivationError.php',
                [
                    'userName' => $user->getNickname(),
                    'errorMessage' => 'Код активации не верен'
                ]
            );
            return;
        }

        //нормальное выполнение программы
        $user->activate();
        UserActivationService::deleteActivationCode($user, $activationCode);
        $this->view->renderHTML(
            'users/UserSuccessfulActivation.php',
            [
                'userName' => $user->getNickname(),
                'userId' => $user->getId()
            ]
        );


    }

    public function login()
    {
        if (!empty($_POST)) {
            try {
                $user = User::login($_POST);
                UsersAuthService::createToken($user);
                header('Location: /');
                exit();
            } catch (InvalidArgumentException $e) {
                $this->view->renderHTML('users/login.php', ['error' => $e->getMessage()]);
                return;
            }
        }

        $this->view->renderHTML('users/login.php');
    }


    public function logout()
    {
        if (isset($_COOKIE['token'])) {
            UsersAuthService::deleteCookie();
        }

        header('Location: /');
    }
}