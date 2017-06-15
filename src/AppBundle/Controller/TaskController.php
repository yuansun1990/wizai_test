<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TaskController extends Controller {
    /**
     * @Route("/taskList/showTask/{id}", defaults={"id" = 0}, name="showTask")
     */
    public function showTaskAction($id) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            return $this->redirectToRoute('login');

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        $em = $this->getDoctrine()
                   ->getRepository('AppBundle:Task');

        $task = $em->find($id);
        if ($task) {
            $ref_id = $task->getReferenceId();
            $title = $task->getTitle();
        } else {
            $ref_id = 0;
            $title = 0;
        }

        $subtasks = $em->findBy(array('userId' => $userId, 'referenceId' => $id));

        return $this->render('taskList/taskList.html.twig', array('subtasks' => $subtasks, 'title' => $title, 'ref_id' => $ref_id, 'id' => $id));
    }
    /**
     * @Route("/taskList/addTask/{ref_id}", defaults={"ref_id" = 0}, name="addTask")
     */
    public function addTaskAction(Request $request, $ref_id)
    {
        //  build the form
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
       
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // get userID from session and set it 
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $userId = $user->getId();

            $task = $form->getData();            

            $task->setUserID($userId);
            $task->setReferenceId($ref_id);

            // use entity manager to save it
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('task_list', ['id' => $ref_id]);
        }

        return $this->render('taskList/addTask.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/taskList/deleteTask/{id}", name="deleteTask")
     */
    public function deleteTaskAction($id) {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->find($id);
        $ref_id = $task->getReferenceId();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        if ($task != null && $task->getUserId() == $userId) {
            $this->deleteTask($task);
        }

        return $this->redirectToRoute('task_list', ['id' => $ref_id]);
    }

    private function deleteTask(Task $task) {
        $em = $this->getDoctrine()->getManager();
        $id = $task->getId();

        $rp = $this->getDoctrine()
                    ->getRepository('AppBundle:Task');

        if ($task != null) {
            $subtasks = $rp->findByReferenceId($id);
            foreach ($subtasks as $subtask) {
                $this->deleteTask($subtask);
            }

            $em->remove($task);
            $em->flush();
        }
    }
}