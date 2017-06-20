<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Project;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ProjectController extends Controller {
    /**
     * @Route("/projectList/showProject", name="showProject")
     */
    public function showProjectAction() {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            return $this->redirectToRoute('login');

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        $em = $this->getDoctrine()
                   ->getRepository('AppBundle:Project');

        $projects = $em->findAll();
        
        $data = ['projects' => $projects, 'user_id' => $userId];
        return $this->render('project/showProject.html.twig', $data);
    }
}