<?php

namespace AppBundle\Repository;

use AppBundle\ValueObject\EmployeeFilter;
use Doctrine\ORM\EntityRepository;

/**
 * EmployeeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EmployeeRepository extends EntityRepository
{
    /**
     * @param EmployeeFilter $employeeFilter
     * @return \Doctrine\ORM\Query
     */
    public function findByEmployeeFilter($employeeFilter)
    {
        $qb = $this->createQueryBuilder('employee')
            ->select('employee')
            ->orderBy('employee.lastName')
            ->addOrderBy('employee.firstName')
            ->addOrderBy('employee.patronymic');

        if (strlen($employeeFilter->getDepartmentName()) > 0) {
            $qb->leftJoin('employee.department', 'department')
                ->where('department.name LIKE :filterDepartmentName')
                ->setParameter('filterDepartmentName', '%' . $employeeFilter->getDepartmentName() . '%');
        }

        if (!is_null($employeeFilter->getHireDate())) {
            $qb->andWhere('employee.hireDate > :filterHireDate')
                ->setParameter('filterHireDate', $employeeFilter->getHireDate()->format('Y-m-d'));
        }

        if (!is_null($employeeFilter->getRetireDate())) {
            $qb->andWhere('employee.retireDate < :filterRetireDate')
                ->setParameter('filterRetireDate', $employeeFilter->getRetireDate()->format('Y-m-d'));
        }

        return $qb->getQuery();
    }

    /**
     * Get first symbols of employees last names and number of last names that begin with every first symbol.
     * @return array
     */
    public function getCounts()
    {
        $qb = $this->createQueryBuilder('employee')
            ->select('SUBSTRING(employee.lastName, 1, 1) AS first_symbol')
            ->addSelect('COUNT(SUBSTRING(employee.lastName, 1, 1)) AS employees_count')
            ->groupBy('first_symbol')
            ->orderBy('first_symbol');

        $result = $qb->getQuery()->getResult();

        $employee_list_alphabetic = array();
        foreach ($result as $arr) {
            $employee_list_alphabetic[$arr['first_symbol']] = $arr['employees_count'];
        }

        return $employee_list_alphabetic;
    }

    public function findByFirstSymbol($first_symbol_list)
    {
        return $this->createQueryBuilder('employee')
            ->select('employee')
            ->where('SUBSTRING(employee.lastName, 1, 1) IN (:first_symbol_list)')
            ->setParameter('first_symbol_list', $first_symbol_list)
            ->orderBy('employee.lastName')
            ->addOrderBy('employee.firstName')
            ->addOrderBy('employee.patronymic')
            ->getQuery()
            ->getResult();
    }
}
