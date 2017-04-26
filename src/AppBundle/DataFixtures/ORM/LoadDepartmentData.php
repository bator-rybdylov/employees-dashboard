<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Department;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadDepartmentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $department_1 = new Department();
        $department_1->setName('Отдел №1');
        $manager->persist($department_1);

        $department_2 = new Department();
        $department_2->setName('Отдел №2');
        $manager->persist($department_2);

        $department_3 = new Department();
        $department_3->setName('Отдел №3');
        $manager->persist($department_3);

        $department_4 = new Department();
        $department_4->setName('Отдел №4');
        $manager->persist($department_4);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}