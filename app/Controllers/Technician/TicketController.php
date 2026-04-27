<?php

namespace App\Controllers\Technician;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Message;
use App\Models\Category;
use App\Models\School;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;

class TicketController extends Controller
{
    public function __construct()
    {
        parent::__construct("App");

        Auth::requireRole(User::TECHNICIAN);
    }

    public function index(): void
    {
        $ticketsModel = new Ticket();

        $tickets = (new Ticket())->ticketsOrderedByStatusPriorityAndOpeningDate();

        echo $this->view->render("technician/ticket/index", [
            "tickets" => $tickets,
        ]);

        clear_old();
    }

    public function create(): void
    {
        $schools = School::all();
        $categories = Category::all();
        $teachers = User::usersByRole(User::TEACHER);

        echo $this->view->render("technician/ticket/create", [
            "schools" => $schools,
            "categories" => $categories,
            "teachers" => $teachers
        ]);

        clear_old();
    }

    public function store(?array $data): void
    {
        $this->validateCsrfToken($data, "tecnico/chamados/cadastrar");
        $data['status'] = Ticket::OPEN;

        $newTicket = new Ticket();

        $errors = array_merge(
            $newTicket->validate($data),
            $newTicket->validateBusinessRules($data)
        );

        if ($errors) {
            flash_old($data);
            foreach ($errors as $error) {
                Message::warning($error);
            }

            redirect("/tecnico/chamados/cadastrar");
            return;
        }

        try {
            $newTicket->fill([
                "title" => $data["title"],
                "description" => $data["description"],
                "school_id" => $data["school_id"],
                "category_id" => $data["category_id"],
                "opened_by" => $data["opened_by"],
                "status" => $data["status"],
                "priority" => $data["priority"]
            ]);

            $newTicket->setOpenedAt();
            $newTicket->save();

        } catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/tecnico/chamados/cadastrar");
            return;
        }

        Message::success("Chamado cadastrado com sucesso!");
        redirect("/tecnico/chamados/editar/" . $newTicket->getId());

    }

    public function edit(?array $data): void
    {
        $ticket = Ticket::find($data["id"]);

        if (!$ticket) {
            Message::error("Chamado não encontrado");
            redirect("/tecnico/chamados/cadastrar");
            return;
        }

        $technicians = User::usersByRole(User::TECHNICIAN);

        echo $this->view->render("technician/ticket/edit", [
            "ticket" => $ticket,
            "technicians" => $technicians,
        ]);
    }

    public function update(?array $data): void
    {

        $this->validateCsrfToken($data, "tecnico/chamados/editar/" . $data["id"]);

        $ticketId = $data["id"];

        $ticket = Ticket::find($ticketId);

        if (!$ticket) {
            Message::warning("Chamado não encontrado ou não existe");
            redirect("/tecnico/chamados/editar/" . $ticketId);
            return;
        }

        //Verificaçao do tecnico
        $errors = array_merge(
            $ticket->validateTechnician($data),
            $ticket->validateStatusTransition($data['status'])
        );

        if ($errors) {
            flash_old($data);

            foreach ($errors as $error) {
                Message::warning($error);
            }

            redirect("/tecnico/chamados/editar/" . $ticket->getId());
            return;
        }

        try {
            $ticket->fill([
                "status" => $data["status"],
                "priority" => $data["priority"]
            ]);

            if (!empty($data['assigned_to'])){
                $ticket->setAssignedTo($data['assigned_to']);
            }

            if (in_array($data['status'], [Ticket::FINISHED, Ticket::ARCHIVED], true)) {
                $ticket->setClosedAt();
            }

            $ticket->save();

        } catch (\InvalidArgumentException $invalidArgumentException) {
            Message::error($invalidArgumentException->getMessage());
            redirect("/tecnico/chamados/editar/" . $ticket->getId());
            return;
        };

        Message::success("Chamado editado com sucesso!");
        redirect("/tecnico/chamados/editar/" . $ticket->getId());

    }
}