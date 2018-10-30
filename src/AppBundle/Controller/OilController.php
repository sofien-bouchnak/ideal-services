<?php
/**
 * Created by PhpStorm.
 * User: Sofien
 * Date: 26/10/2018
 * Time: 21:10
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Oil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class OilController extends Controller
{
    /**
     * @Route("/oils/{id}", name="oil_show")
     * @Method({"GET"})
     * @param Oil $oil
     * @return Response
     */
    public function showAction(Oil $oil)
    {

        $data = $this->get('jms_serializer')->serialize($oil, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/oil", name="oil_create")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $data = $request->getContent();
        $oil = $this->get('jms_serializer')->deserialize($data, 'AppBundle\Entity\Oil', 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($oil);
        $em->flush();

        $validator = $this->get('validator');

        $errors = $validator->validate($oil);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/oils", name="oil_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $oils = $this->getDoctrine()->getRepository('AppBundle:Oil')->findAll();
        //dump($oils); exit;
        $data = $this->get('jms_serializer')->serialize($oils, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}