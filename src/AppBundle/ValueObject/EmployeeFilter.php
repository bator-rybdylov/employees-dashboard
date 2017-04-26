<?php

namespace AppBundle\ValueObject;

class EmployeeFilter
{
    /**
     * @var string
     */
    private $departmentName;

    /**
     * @var \DateTime
     */
    private $hireDate;

    /**
     * @var \DateTime
     */
    private $retireDate;


    /**
     * Set hireDate
     *
     * @param \DateTime $hireDate
     *
     * @return EmployeeFilter
     */
    public function setHireDate($hireDate)
    {
        $this->hireDate = $hireDate;

        return $this;
    }

    /**
     * Get hireDate
     *
     * @return \DateTime
     */
    public function getHireDate()
    {
        return $this->hireDate;
    }

    /**
     * Set retireDate
     *
     * @param \DateTime $retireDate
     *
     * @return EmployeeFilter
     */
    public function setRetireDate($retireDate)
    {
        $this->retireDate = $retireDate;

        return $this;
    }

    /**
     * Get retireDate
     *
     * @return \DateTime
     */
    public function getRetireDate()
    {
        return $this->retireDate;
    }

    /**
     * Set departmentName
     *
     * @param string $departmentName
     *
     * @return EmployeeFilter
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;

        return $this;
    }

    /**
     * Get departmentName
     *
     * @return string
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }
}