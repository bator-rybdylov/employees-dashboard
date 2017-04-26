<?php

namespace AppBundle\Service\Employee;

use AppBundle\Entity\Employee;

class AlphabeticGroupMaker
{
    const GROUP_MAX_COUNT = 7;
    const ALPHABET = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЫЭЮЯ';

    /**
     * Get array where all employees grouped by first character of last name
     * @param array $employee_list
     * @return array
     */
    private function getAlphabeticList($employee_list)
    {
        $employee_list_alphabetic = array();
        /** @var Employee $employee */
        foreach ($employee_list as $employee) {
            $last_name = $employee->getLastName();
            if (strlen($last_name) > 0) {
                $first_symbol = mb_convert_encoding(mb_substr($last_name, 0, 1), 'UTF-8');
                $employee_list_alphabetic[$first_symbol][] = $employee;
            }
        }

        return $employee_list_alphabetic;
    }

    /**
     * Distribute employees in groups. Number of groups is <= 7.
     * @param $employee_list
     * @return array
     */
    public function makeGroups($employee_list)
    {
        $groups_made = 0;
        $distributed_employees_count = 0;
        // Limit of employees number in group
        $group_limit = ceil(count($employee_list)/self::GROUP_MAX_COUNT);

        $employee_list_alphabetic = $this->getAlphabeticList($employee_list);
        //var_dump($employee_list_alphabetic);

        $group_list = array();
        $group = array(
            'start' => null,// Starting symbol of group
            'end' => null,// Ending symbol of group
            'employees' => array(),
        );
        for ($i = 0; $i < mb_strlen(self::ALPHABET); $i++) {
            $char = mb_substr(self::ALPHABET, $i, 1);
            if (is_null($group['start'])) {
                $group['start'] = $char;
            }
            if (isset($employee_list_alphabetic[$char])) {
                if (empty($group['employees'])) {
                    $group['employees'] = $employee_list_alphabetic[$char];
                    $distributed_employees_count += count($employee_list_alphabetic[$char]);
                } else {
                    // Current number of employees in group
                    $current_value = count($group['employees']);
                    // Current number of employees in group + number of employees that begins with next symbol
                    $new_value = $current_value + count($employee_list_alphabetic[$char]);

                    if ($current_value <= $group_limit) {
                        if (abs($new_value - $group_limit) < abs($current_value - $group_limit)) {
                            // Add employees to group if new value is closer to limit than current value.
                            $group['employees'] = array_merge($group['employees'], $employee_list_alphabetic[$char]);
                            $distributed_employees_count += count($employee_list_alphabetic[$char]);
                        }
                        if (($new_value > $group_limit && abs($new_value - $group_limit) < abs($current_value - $group_limit))
                            || abs($new_value - $group_limit) >= abs($current_value - $group_limit))
                        {
                            // Set current symbol as a ending symbol
                            $group['end'] = mb_substr(self::ALPHABET, $i, 1);
                            if (abs($new_value - $group_limit) >= abs($current_value - $group_limit)) {
                                // Set previous symbol as a ending symbol
                                $group['end'] = mb_substr(self::ALPHABET, $i - 1, 1);
                            }

                            // Add to groups list
                            $group_list[] = $group;
                            $groups_made++;

                            // Set new empty group.
                            $group = array(
                                'start' => null,
                                'end' => null,
                                'employees' => array(),
                            );
                            if (abs($new_value - $group_limit) >= abs($current_value - $group_limit)) {
                                // Set new group. Starting symbol is set to current symbol of loop.
                                $group = array(
                                    'start' => $char,
                                    'end' => null,
                                    'employees' => $employee_list_alphabetic[$char],
                                );
                            }

                            // Set new limit of employees number in group
                            if ($groups_made < self::GROUP_MAX_COUNT) {
                                $group_limit = ceil((count($employee_list) - $distributed_employees_count) / (self::GROUP_MAX_COUNT - $groups_made));
                            }
                        }
                    }
                }
            }

            // Add group to groups list if on the last loop.
            if ($i === mb_strlen(self::ALPHABET) - 1) {
                $group['end'] = mb_substr(self::ALPHABET, $i, 1);
                $group_list[] = $group;
            }
        }

        return $group_list;
    }
}