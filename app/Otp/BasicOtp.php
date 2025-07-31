<?php

namespace App\Otp;

use SadiqSalau\LaravelOtp\Contracts\OtpInterface as Otp;

class BasicOtp implements Otp
{
    /**
     * Constructs Otp class
     */
    public function __construct(public string $email, public array $data)
    {
        //
    }

    /**
     * Processes the Otp
     *
     * @return mixed
     */
    public function process()
    {
        return $this->data;
    }
}
