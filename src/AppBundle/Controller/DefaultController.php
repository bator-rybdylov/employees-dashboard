<?php

namespace AppBundle\Controller;

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
        $employees_query = $manager->getRepository('AppBundle:Employee')
            ->findByEmployeeFilter($employee_filter);

        // Paginate employees list
        $paginator = $this->get('knp_paginator');
        $employees_pagination = $paginator->paginate(
            $employees_query,
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function alphabeticPageAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $employee_alphabetic_counts = $manager->getRepository('AppBundle:Employee')->getCounts();

        // Distribute employees in alphabetical groups
        $group_maker = $this->get('app.group_maker');
        $alphabetic_groups_list = $group_maker->makeGroups($employee_alphabetic_counts);

        // Get group number from request
        $group_number = $request->query->getInt('group', 1) - 1;
        if ($group_number < 0 || $group_number > count($alphabetic_groups_list)) {
            $group_number = 0;
        }
        // Get array of symbols of this group
        $symbols_range = $group_maker->getAlphabetRange(
            $alphabetic_groups_list[$group_number]['start'],
            $alphabetic_groups_list[$group_number]['end']
        );

        // Get employees only for this group
        $employee_list = $manager->getRepository('AppBundle:Employee')->findByFirstSymbol($symbols_range);

        return $this->render('@App/alphabetic_page.html.twig', array(
            'alphabetic_groups_list' => $alphabetic_groups_list,
            'employee_list' => $employee_list,
            'group_number' => $group_number,
        ));
    }
}
