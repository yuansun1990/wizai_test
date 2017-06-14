<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class taskListController extends Controller{
    /**
     * @Route("/taskList", name="taskList")
     */

    public function showTaskAction(Request $request)
    {
        // 1) build the form
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        return $this->render('taskList/taskList.html.twig');
    }
}