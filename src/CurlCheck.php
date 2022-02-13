<?php

namespace Alahaxe\HealthCheckBundle\Checks\Curl;

use Alahaxe\HealthCheck\Contracts\CheckInterface;
use Alahaxe\HealthCheck\Contracts\CheckStatus;
use Alahaxe\HealthCheck\Contracts\CheckStatusInterface;

class CurlCheck implements CheckInterface
{
    public function __construct(
        protected string $url,
        protected string $name,
        protected int $connectTimeout = 2,
        protected int $timeout = 5,
        protected bool $warningOnTimeout = false,
        protected bool $warningOnFail = false,
        protected bool $trustCertificate = false
    ) {
    }

    public function check(): CheckStatusInterface
    {
        $ch = curl_init();

        $failStatus = $this->warningOnFail ? CheckStatusInterface::STATUS_WARNING : CheckStatusInterface::STATUS_INCIDENT;
        try {
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->trustCertificate ? 2 : 0);

            $response = curl_exec($ch);
            $status = CheckStatusInterface::STATUS_OK;

            if ($error = curl_errno($ch)) {
                if (CURLE_OPERATION_TIMEDOUT === $error && $this->warningOnTimeout) {
                    $status = CheckStatusInterface::STATUS_WARNING;
                } else {
                    $status = $failStatus;
                }
            } else {
                $httpCode = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
                if ($httpCode !== 200) {
                    $status = $failStatus;
                }
            }
        } catch (\Throwable $th) {
            $status = $failStatus;
        } finally {
            curl_close($ch);
        }

        return new CheckStatus(
            $this->name,
            __CLASS__,
            $status
        );
    }
}
