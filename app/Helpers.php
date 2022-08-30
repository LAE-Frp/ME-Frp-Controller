<?php

if (!function_exists('success')) {
    function success($data = null, $to = false)
    {
        if ($to) {
            return redirect()->to($to)->with('status', $data ?? 'Success!');
        }
        return redirect()->back()->with('status', $data ?? 'Success!');
    }
}

if (!function_exists('failed')) {
    function failed($data = null)
    {
        return redirect()->back()->with('error', $data ?? 'Success!');
    }
}

if (!function_exists('isUser')) {
    function isUser($user_id)
    {
        return auth()->id() === $user_id ? true : false;
    }
}


if (!function_exists('unitConversion')) {
    function unitConversion($num)
    {
        $p = 0;
        $format = 'Bytes';
        if ($num > 0 && $num < 1024) {
            $p = 0;
            return number_format($num) . ' ' . $format;
        }
        if ($num >= 1024 && $num < pow(1024, 2)) {
            $p = 1;
            $format = 'KB';
        }
        if ($num >= pow(1024, 2) && $num < pow(1024, 3)) {
            $p = 2;
            $format = 'MB';
        }
        if ($num >= pow(1024, 3) && $num < pow(1024, 4)) {
            $p = 3;
            $format = 'GB';
        }
        if ($num >= pow(1024, 4) && $num < pow(1024, 5)) {
            $p = 3;
            $format = 'TB';
        }
        $num /= pow(1024, $p);
        return number_format($num, 3) . ' ' . $format;
    }
}
