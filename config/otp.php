<?php

return [
    'static_enabled' => (bool) env('STATIC_OTP_ENABLED', false),
    'static_code' => (string) env('STATIC_OTP_CODE', '123456'),
];
