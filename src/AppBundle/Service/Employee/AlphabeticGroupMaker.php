<?php

namespace AppBundle\Service\Employee;

class AlphabeticGroupMaker
{
    const GROUP_MAX_COUNT = 7;
    const ALPHABET = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЫЭЮЯ';

    /**
     * Compose groups using counts of last names of every symbol. Number of groups is <= 7.
     * @param array $employee_alphabetic_counts
     * @return array
     */
    public function makeGroups($employee_alphabetic_counts)
    {
        $groups_made = 0;
        $distributed_employees_count = 0;
        $all_employees_count = array_sum($employee_alphabetic_counts);

        // Limit of employees number in group
        $group_limit = ceil($all_employees_count/self::GROUP_MAX_COUNT);

        $group_list = array();
        $group = array(
            'start' => null,// Starting symbol of group
            'end' => null,// Ending symbol of group
            'employees_count' => 0
        );
        for ($i = 0; $i < mb_strlen(self::ALPHABET); $i++) {
            $char = mb_substr(self::ALPHABET, $i, 1);
            if (is_null($group['start'])) {
                $group['start'] = $char;
            }
            if (isset($employee_alphabetic_counts[$char])) {
                if ($group['employees_count'] === 0) {
                    $group['employees_count'] = $employee_alphabetic_counts[$char];
                    $distributed_employees_count += $employee_alphabetic_counts[$char];
                } else {
                    // Current number of employees in group
                    $current_value = $group['employees_count'];
                    // Current number of employees in group + number of employees that begins with next symbol
                    $new_value = $current_value + $employee_alphabetic_counts[$char];

                    if ($current_value <= $group_limit) {
                        if (abs($new_value - $group_limit) < abs($current_value - $group_limit)) {
                            // Add employees to group if new value is closer to limit than current value.
                            $group['employees_count'] = $new_value;
                            $distributed_employees_count += $employee_alphabetic_counts[$char];
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
                                'employees_count' => 0,
                            );
                            if (abs($new_value - $group_limit) >= abs($current_value - $group_limit)) {
                                // Set new group. Starting symbol is set to current symbol of loop.
                                $group = array(
                                    'start' => $char,
                                    'end' => null,
                                    'employees_count' => $employee_alphabetic_counts[$char],
                                );
                            }

                            // Set new limit of employees number in group
                            if ($groups_made < self::GROUP_MAX_COUNT) {
                                $group_limit = ceil(($all_employees_count - $distributed_employees_count) / (self::GROUP_MAX_COUNT - $groups_made));
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

    /**
     * Get range of symbols from alphabet.
     * @param string $start_symbol
     * @param string $end_symbol
     * @return array
     */
    public function getAlphabetRange($start_symbol, $end_symbol)
    {
        $start_symbol_pos = mb_strpos(self::ALPHABET, $start_symbol);
        $end_symbol_pos = mb_strpos(self::ALPHABET, $end_symbol);

        if (false === $start_symbol_pos
            || false === $end_symbol_pos
            || $start_symbol_pos > $end_symbol_pos)
        {
            return array();
        }

        if ($start_symbol_pos === $end_symbol_pos) {
            return array(mb_substr(self::ALPHABET, $start_symbol_pos, 1));
        }

        $range = array();
        for ($i = $start_symbol_pos; $i <= $end_symbol_pos; $i++) {
            $range[] = mb_substr(self::ALPHABET, $i, 1);
        }

        return $range;
    }
}