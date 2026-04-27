<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Email;
use App\Core\Message;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");
    }

    public function index(): void
    {
        if (Auth::check()) {

            if (Auth::role() === User::TECHNICIAN) {
                redirect("/tecnico/dashboard");
                return;
            }

            if (Auth::role() === User::TEACHER) {
                redirect("/professor/dashboard");
                return;
            }

        }

        echo $this->view->render("auth/auth-login", [
            "title" => "Entrar " . APP_NAME
        ]);
    }

    public function authenticate(?array $data): void
    {
        $this->validateCsrfToken($data, "/entrar");


        if (empty($data['email']) || empty($data['password'])) {
            Message::warning("Os campos EMAIL e SENHA são obrigatorios.");
            redirect("/entrar");
            return;
        }

        $user = User::findByEmail($data["email"]);

        if (!$user || !$user->passwordVerify($data["password"])) {
            Message::warning("Credenciais inválidas.");
            redirect("/entrar");
            return;
        }


        //SE ENCONTROU USUARIO, ENTÃO VERIFICO O STATUS
        if ($user->getStatus() === User::INACTIVE) {
            Message::error("Usuário está inativo. Contate o administrador.");
            redirect("/entrar");
            return;

        }

        $session = new Session();
        $session->set("auth", [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "role" => $user->getRole(),
        ]);

        $session->regenerate();

        $user->setLastLoginAt();
        $user->save();


        if ($user->getRole() === User::TECHNICIAN) {
            Message::success("Bem-vindo(a) " . $user->getName());
            redirect("/tecnico/dashboard");
            return;
        }
        if ($user->getRole() === User::TEACHER) {
            Message::success("Bem-vindo(a), Professor(a)" . $user->getName());
            redirect("/professor/dashboard");
            return;
        }

        $session->destroy();
        Message::error("Perfil de acesso não reconhecido.");
        redirect("/entrar");


    }

    public function logout($data): void
    {
        $session = new Session();

        if (!$data || !csrf_verify($data["_csrf"] ?? null)) {
            Message::error("Token de segurança inválido");

            $authSession = $session->get("auth");

            if ($authSession) {

                if ($authSession->role === User::TECHNICIAN) {
                    redirect("/tecnico/dashboard");
                    return;
                }

                if ($authSession->role === User::TEACHER) {
                    redirect("/professor/dashboard");
                    return;
                }

            }

            redirect("/entrar");
            return;

        }

        $session->unset("auth");
        Message::secondary("Sua sessão foi encerrada, mas volte logo!");
        redirect("/entrar");
    }

    public function create(): void
    {
        echo $this->view->render("auth/auth-register", [
            "title" => "Cadastrar " . APP_NAME
        ]);
    }

    public function store(?array $data): void
    {
        //0. Validação do CSRF Token
        $this->validateCsrfToken($data, "/cadastrar");

        // 1. validaçao dos campos obrigatorios

        $required = [
            "name" => "O campo NOME é obrigatório.",
            "email" => "O campo EMAIL é obrigatório.",
            "password" => "O campo SENHA é obrigatório.",
            "password_confirm" => "O campo CONFIRMA SENHA é obrigatório.",
        ];

        $errors = [];
        foreach ($required as $value => $message) {
            if (empty($data[$value])) {
                $errors[] = $message;
            }
        }

        if ($errors) {

            foreach ($errors as $error) {
                Message::error($error);
            }
            redirect("/cadastrar");
            return;
        }

        //2. Verificar se e-mail já exite
        if (User::findByEmail($data['email'])) {
            Message::warning("O email informado já está cadastrado.");
            redirect("/cadastrar");
            return;
        }

        //3 Validar se as senhas correspondem
        if ($data['password'] !== $data['password_confirm']) {
            Message::warning("As senhas não correspondem.");
            redirect("/cadastrar");
            return;
        }

        $data['role'] = User::TEACHER;
        $data['status'] = User::REGISTERED;

        //4. Criar o usuário
        try {

            $newUser = new User();
            $newUser->fill($data);
            $newUser->save();

        } catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/cadastrar");
            return;
        }

        Message::success("Usuário Cadastrado com sucesso. Faca Login");
        redirect("/cadastrar/sucesso");
        return;


    }

    public function storeSuccess(): void
    {
        echo $this->view->render("auth/auth-register-success", [
            "title" => "Contar criada " . APP_NAME
        ]);
    }

    public function forgotPassword(): void
    {
        echo $this->view->render("auth/auth-forgot-password", [
            "title" => "Esqueci a senha | " . APP_NAME,
        ]);
    }

    public function sendResetLink(?array $data): void
    {
        //0. Validação de Token
        $this->validateCsrfToken($data, "/redefinir-senha");

        //1. Validação do campo email
        if (empty($data['email'])) {
            Message::warning("O campo EMAIL é obrigatório.");
            redirect("/redefinir-senha");
            return;
        }

        $user = User::findByEmail($data['email']);

        //2. Validação da Existencia do Usuario
        if (!$user) {
            Message::warning("Se o email estive cadastrado, você receberá o link de redefinição de senha.");
            redirect("/redefinir-senha");
            return;
        }

        //3. Validação do Token
        $token = $user->setResetToken();
        $user->save();

        //4. Preparar mensagem de email
        $template = file_get_contents(__DIR__ . "/../Views/Email/forgot-password.php");
        $body = str_replace(
            ["{{NOME_USUARIO}}", "{{LINK_RESET}}", "{{EXPIRACAO_HORAS}}", "{{ANO}}"],
            [$user->getName(), url("/resetar-senha/{$token}"), "2", date("Y")],
            $template
        );

        try {
            $email = new Email();
            $email->bootstrap(
                "Redefinir Senha | " . APP_NAME,
                $body,
                $user->getEmail(),
                $user->getName(),
            );

            $email->send();

            Message::warning("Se o email estive cadastrado, você receberá o link de redefinição de senha.");
        } catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error("Não foi possivel enviar o e-mail.");
            redirect("/redefinir-senha");
            return;
        }

        redirect("/redefinir-senha/sucesso");
    }

    public function sendResetLinkSuccess(): void
    {
        echo $this->view->render("auth/auth-forgot-password-success", [
            "title" => "Redefinir a Senha | " . APP_NAME
        ]);
    }

    public function resetPassword(?array $data): void
    {
        $user = User::findByResetToken($data['token']);

        $now = new \DateTimeImmutable("now", new \DateTimeZone(APP_TIMEZONE));
        $expiration = new \DateTimeImmutable($user->getResetExpiresAt(), new \DateTimeZone(APP_TIMEZONE));

        if (!$user || $now->diff($expiration)->invert === 1) {
            Message::error("Link invalido ou expirado. Solicite novamente.");
            redirect("/redefinir-senha");
            return;
        }

        echo $this->view->render("auth/auth-reset-password", [
            "title" => "Resetar a Senha | " . APP_NAME,
            "token" => $user->getResetToken(),
        ]);
    }

    public function updatePassword(?array $data): void
    {
        $this->validateCsrfToken($data, "/resetar-senha");

        if (empty($data['password']) || empty($data['password_confirm'])) {
            Message::warning("Os campo SENHA e CONFIRMAR SENHA são obrigatórios.");
            redirect("/redefinir-senha");
            return;
        }

        if($data['password'] !== $data['password_confirm']) {
            Message::warning("As senhas não conferem.");
            redirect("/redefinir-senha");
            return;
        }

        $user = User::findByResetToken($data['token']);

        $now = new \DateTimeImmutable("now", new \DateTimeZone(APP_TIMEZONE));
        $expiration = new \DateTimeImmutable($user->getResetExpiresAt(), new \DateTimeZone(APP_TIMEZONE));

        if (!$user || $now->diff($expiration)->invert === 1) {
            Message::error("Link invalido ou expirado. Solicite novamente.");
            redirect("/redefinir-senha");
            return;
        }

        try {

            $user->fill([
                "password" => $data["password"],
                "reset_token" => null,
                "reset_expires_at" => null,

            ]);

        }catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/redefinir-senha");
            return;
        }

        Message::success("Senha alterada com sucesso. Faça Login.");
        redirect("/entrar");
    }
}