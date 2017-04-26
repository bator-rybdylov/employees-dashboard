<?php

namespace AppBundle\Controller;

use AppBundle\Form\EmployeeFilterType;
use AppBundle\ValueObject\EmployeeFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        // Get filter values
        $hireDate = null;
        if (($request->query->get('hireDate'))) {
            $hireDate = new \DateTime($request->query->get('hireDate'));
        }

        $retireDate = null;
        if (strlen($request->query->get('retireDate')) > 0) {
            $retireDate = new \DateTime($request->query->get('retireDate'));
        }

        // Create filter object
        $employee_filter = new EmployeeFilter();
        $employee_filter->setDepartmentName($request->query->get('departmentName'));
        $employee_filter->setHireDate($hireDate);
        $employee_filter->setRetireDate($retireDate);

        // Find employees by filter
        $employees_list = $manager->getRepository('AppBundle:Employee')
            ->findByEmployeeFilter($employee_filter);

        // Paginate employees list
        $paginator = $this->get('knp_paginator');
        $employees_pagination = $paginator->paginate(
            $employees_list,
            $request->query->getInt('page', 1),/*page number*/
            10
        );

        return $this->render('@App/index.html.twig', [
            'employees_pagination' => $employees_pagination,
            'departmentName' => $employee_filter->getDepartmentName(),
            'hireDate' => $hireDate,
            'retireDate' => $retireDate,
        ]);
    }

    /**
     * @Route("/employee/{employee_id}", name="employee_page")
     * @param int $employee_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employeePageAction($employee_id)
    {
        $manager = $this->getDoctrine()->getManager();

        $employee = $manager->find('AppBundle:Employee', $employee_id);

        return $this->render('@App/employee_page.html.twig', array(
            'employee' => $employee
        ));
    }

    /**
     * @Route("/alphabetic", name="alphabetic_employees_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function alphabeticPageAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $employee_list = $manager->getRepository('AppBundle:Employee')->findBy(
            array(),
            array('lastName' => 'ASC', 'firstName' => 'ASC', 'patronymic' => 'ASC')
        );

        // Distribute employees in alphabetical groups
        $alphabetic_groups_list = $this->get('app.group_maker')->makeGroups($employee_list);

        return $this->render('@App/alphabetic_page.html.twig', array(
            'alphabetic_groups_list' => $alphabetic_groups_list
        ));
    }
}
