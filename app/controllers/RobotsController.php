<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class RobotsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for robots
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Robots', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $robots = Robots::find($parameters);
        if (count($robots) == 0) {
            $this->flash->notice("The search did not find any robots");

            $this->dispatcher->forward([
                "controller" => "robots",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $robots,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a robot
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $robot = Robots::findFirstByid($id);
            if (!$robot) {
                $this->flash->error("robot was not found");

                $this->dispatcher->forward([
                    'controller' => "robots",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $robot->id;

            $this->tag->setDefault("id", $robot->id);
            $this->tag->setDefault("name", $robot->name);
            $this->tag->setDefault("type", $robot->type);
            $this->tag->setDefault("year", $robot->year);
            
        }
    }

    /**
     * Creates a new robot
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "robots",
                'action' => 'index'
            ]);

            return;
        }

        $robot = new Robots();
        $robot->name = $this->request->getPost("name");
        $robot->type = $this->request->getPost("type");
        $robot->year = $this->request->getPost("year");
        

        if (!$robot->save()) {
            foreach ($robot->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "robots",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("robot was created successfully");

        $this->dispatcher->forward([
            'controller' => "robots",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a robot edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "robots",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $robot = Robots::findFirstByid($id);

        if (!$robot) {
            $this->flash->error("robot does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "robots",
                'action' => 'index'
            ]);

            return;
        }

        $robot->name = $this->request->getPost("name");
        $robot->type = $this->request->getPost("type");
        $robot->year = $this->request->getPost("year");
        

        if (!$robot->save()) {

            foreach ($robot->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "robots",
                'action' => 'edit',
                'params' => [$robot->id]
            ]);

            return;
        }

        $this->flash->success("robot was updated successfully");

        $this->dispatcher->forward([
            'controller' => "robots",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a robot
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $robot = Robots::findFirstByid($id);
        if (!$robot) {
            $this->flash->error("robot was not found");

            $this->dispatcher->forward([
                'controller' => "robots",
                'action' => 'index'
            ]);

            return;
        }

        if (!$robot->delete()) {

            foreach ($robot->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "robots",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("robot was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "robots",
            'action' => "index"
        ]);
    }

}
