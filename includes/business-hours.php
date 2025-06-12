<?php
/**
 * Extended Business Hours Functionality
 * Adds timezone support and advanced formatting for business hours
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!function_exists('get_business_timezone')) {
    function get_business_timezone($location = null) {
        if (!empty($location['state'])) {
            $timezones = array(
                'Alabama' => 'America/Chicago',
                'Alaska' => 'America/Anchorage',
                'Arizona' => 'America/Phoenix',
                'Arkansas' => 'America/Chicago',
                'California' => 'America/Los_Angeles',
                'Colorado' => 'America/Denver',
                'Connecticut' => 'America/New_York',
                'Delaware' => 'America/New_York',
                'Florida' => 'America/New_York',
                'Georgia' => 'America/New_York',
                'Hawaii' => 'Pacific/Honolulu',
                'Idaho' => 'America/Boise',
                'Illinois' => 'America/Chicago',
                'Indiana' => 'America/Indiana/Indianapolis',
                'Iowa' => 'America/Chicago',
                'Kansas' => 'America/Chicago',
                'Kentucky' => 'America/New_York',
                'Louisiana' => 'America/Chicago',
                'Maine' => 'America/New_York',
                'Maryland' => 'America/New_York',
                'Massachusetts' => 'America/New_York',
                'Michigan' => 'America/Detroit',
                'Minnesota' => 'America/Chicago',
                'Mississippi' => 'America/Chicago',
                'Missouri' => 'America/Chicago',
                'Montana' => 'America/Denver',
                'Nebraska' => 'America/Chicago',
                'Nevada' => 'America/Los_Angeles',
                'New Hampshire' => 'America/New_York',
                'New Jersey' => 'America/New_York',
                'New Mexico' => 'America/Denver',
                'New York' => 'America/New_York',
                'North Carolina' => 'America/New_York',
                'North Dakota' => 'America/Chicago',
                'Ohio' => 'America/New_York',
                'Oklahoma' => 'America/Chicago',
                'Oregon' => 'America/Los_Angeles',
                'Pennsylvania' => 'America/New_York',
                'Rhode Island' => 'America/New_York',
                'South Carolina' => 'America/New_York',
                'South Dakota' => 'America/Chicago',
                'Tennessee' => 'America/Chicago',
                'Texas' => 'America/Chicago',
                'Utah' => 'America/Denver',
                'Vermont' => 'America/New_York',
                'Virginia' => 'America/New_York',
                'Washington' => 'America/Los_Angeles',
                'West Virginia' => 'America/New_York',
                'Wisconsin' => 'America/Chicago',
                'Wyoming' => 'America/Denver'
            );
            
            return isset($timezones[$location['state']]) ? $timezones[$location['state']] : wp_timezone_string();
        }
        return wp_timezone_string();
    }
}

if (!function_exists('format_business_time')) {
    function format_business_time($time, $format = 'g:i A') {
        if (empty($time)) return '';
        return date($format, strtotime($time));
    }
}

if (!function_exists('get_timezone_abbreviation')) {
    function get_timezone_abbreviation($timezone) {
        try {
            $dateTime = new DateTime('now', new DateTimeZone($timezone));
            return $dateTime->format('T');
        } catch (Exception $e) {
            return '';
        }
    }
}

if (!function_exists('convert_to_timezone')) {
    function convert_to_timezone($time, $from_timezone, $to_timezone) {
        try {
            $datetime = new DateTime($time, new DateTimeZone($from_timezone));
            $datetime->setTimezone(new DateTimeZone($to_timezone));
            return $datetime->format('H:i');
        } catch (Exception $e) {
            return $time;
        }
    }
}

if (!function_exists('get_business_hours_status')) {
    function get_business_hours_status($business_hours, $location = null) {
        $status = get_current_business_hours($business_hours);
        if (!$status) return false;

        // Get timezone
        $timezone = get_business_timezone($location);
        $tz_abbr = get_timezone_abbreviation($timezone);

        // Add timezone info to status array
        $status['timezone'] = $timezone;
        $status['timezone_abbr'] = $tz_abbr;

        // Format times if present
        if (isset($status['closes'])) {
            $status['closes_formatted'] = format_business_time($status['closes']);
        }

        return $status;
    }
}

if (!function_exists('format_hours_range')) {
    function format_hours_range($opening_time, $closing_time) {
        if (empty($opening_time) || empty($closing_time)) {
            return 'Closed';
        }

        if (is_24_hours($opening_time, $closing_time)) {
            return 'Open 24 Hours';
        }

        return format_business_time($opening_time) . ' - ' . format_business_time($closing_time);
    }
}

if (!function_exists('get_formatted_hours')) {
    function get_formatted_hours($business_hours, $location = null) {
        if (empty($business_hours)) return array();

        $timezone = get_business_timezone($location);
        $formatted_hours = array();
        $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        
        foreach ($days as $day) {
            $day_hours = array_values(array_filter($business_hours, function($hours) use ($day) {
                return isset($hours['day_of_week']) && strtolower($hours['day_of_week']) === $day;
            }));
            
            if (!empty($day_hours)) {
                $hours = $day_hours[0];
                $formatted_hours[$day] = array(
                    'day_name' => ucfirst($day),
                    'hours_range' => format_hours_range($hours['opening_time'], $hours['closing_time']),
                    'is_24_hours' => is_24_hours($hours['opening_time'], $hours['closing_time']),
                    'raw' => array(
                        'opens' => $hours['opening_time'],
                        'closes' => $hours['closing_time']
                    )
                );
            } else {
                $formatted_hours[$day] = array(
                    'day_name' => ucfirst($day),
                    'hours_range' => 'Closed',
                    'is_24_hours' => false,
                    'raw' => array(
                        'opens' => null,
                        'closes' => null
                    )
                );
            }
        }

        return $formatted_hours;
    }
}

if (!function_exists('is_holiday')) {
    function is_holiday($date = null) {
        if (!$date) {
            $date = current_time('Y-m-d');
        }

        // List of holidays (month-day format)
        $holidays = array(
            '01-01' => "New Year's Day",
            '07-04' => 'Independence Day',
            '12-25' => 'Christmas Day',
            // Add more holidays as needed
        );

        $check_date = date('m-d', strtotime($date));
        return isset($holidays[$check_date]) ? $holidays[$check_date] : false;
    }
}

if (!function_exists('get_next_business_day')) {
    function get_next_business_day($date = null, $business_hours = array()) {
        if (!$date) {
            $date = current_time('Y-m-d');
        }

        $next_date = date('Y-m-d', strtotime($date . ' +1 day'));
        $max_tries = 7; // Prevent infinite loop
        $tries = 0;

        while ($tries < $max_tries) {
            if (!is_holiday($next_date)) {
                $day_of_week = strtolower(date('l', strtotime($next_date)));
                
                // Check if business has hours for this day
                foreach ($business_hours as $hours) {
                    if (strtolower($hours['day_of_week']) === $day_of_week && 
                        !empty($hours['opening_time'])) {
                        return $next_date;
                    }
                }
            }
            $next_date = date('Y-m-d', strtotime($next_date . ' +1 day'));
            $tries++;
        }

        return $next_date;
    }
}