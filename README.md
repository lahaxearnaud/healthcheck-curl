# HealthCheck system checker

## Installation

Install checker:

```bash
    composer require alahaxe/healthcheck-curl
```

Register service in your app:

```yaml
    Alahaxe\HealthCheckBundle\Checks\Curl\CurlCheck:
        # optional arguments
        arguments:
            $url: "https://httpbin.org/get"
            $name: "curlHttpBin"
            # optionals arguments
            $connectTimeout: 2
            $timeout: 5
            $warningOnTimeout: false # default is to report incident
            $warningOnFail: false # default is to report incident
            $trustCertificate: false # self signed ssl cert ?
        tags: ['lahaxearnaud.healthcheck.check']
```
