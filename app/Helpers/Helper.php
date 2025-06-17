<?php
if (!function_exists('format_date')) {
    // $inputDate = '06.17.25';
    function format_date($inputDate, $format = 'Y-m-d')
    {
        if (empty($inputDate) || !preg_match('/^\d{2}\.\d{2}\.\d{2}$/', $inputDate)) {
            return null;
        }

        $date = date_create_from_format('m.d.y', $inputDate);
        $outputDate = date_format($date, 'Y-m-d');
        return $outputDate;
    }
}