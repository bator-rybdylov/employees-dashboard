<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Employee;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadEmployeeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $departments_list = $manager->getRepository('AppBundle:Department')->findAll();

        $lastNames = array('Алексеев', 'Борисов', 'Денисов', 'Калинин', 'Михайлов', 'Осокин', 'Павлов', 'Романов', 'Федотов', 'Яковлев');
        $firstNames = array('Алексей', 'Борис', 'Денис', 'Игорь', 'Михаил', 'Николай', 'Павел', 'Роман', 'Федор', 'Юрий');
        $patronymics = array('Алексеевич', 'Борисович', 'Денисович', 'Игоревич', 'Михайлович', 'Николаевич', 'Павлович', 'Романович', 'Юрьевич', 'Яковлевич');
        $positions = array('Консультант', 'Тренер', 'Грузчик', 'Инженер', 'Программист', 'Бухгалтер', 'Юрист', 'Водитель', 'Секретарь');

        for ($i = 0; $i < 300; $i++) {
            $employee = new Employee();
            $employee->setLastName($lastNames[array_rand($lastNames)]);
            $employee->setFirstName($firstNames[array_rand($firstNames)]);
            $employee->setPatronymic($patronymics[array_rand($patronymics)]);
            $employee->setBirthday(new \DateTime(rand(1950, 1995) . '-' . rand(1, 12) . '-' . rand(1, 31)));
            $employee->setEmail('test' . ($i + 1) . '@test.com');
            $employee->setPhoneNumber('+7 9' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(1000, 9999));

            $hireDate = new \DateTime($employee->getBirthday()->format('Y-m-d'));
            $hireDate->add(new \DateInterval('P'.rand(18, 30).'Y'.rand(1, 12).'M'.rand(1, 31).'D'));
            $employee->setHireDate($hireDate);

            if (rand(0, 1) === 0) {
                $employee->setRetireDate(null);
            } else {
                $retireDate = new \DateTime($employee->getHireDate()->format('Y-m-d'));
                $retireDate->add(new \DateInterval('P'.rand(0, 10).'Y'.rand(1, 12).'M'.rand(1, 31).'D'));
                $employee->setRetireDate($retireDate);
            }

            $employee->setPosition($positions[array_rand($positions)]);
            $employee->setDepartment($departments_list[array_rand($departments_list)]);
            $manager->persist($employee);
        }
        
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}