<?php
// GENERATED CODE -- DO NOT EDIT!

namespace App\Grpc\Auth\V1;

/**
 */
class HealthServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \App\Grpc\Auth\V1\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Health(\App\Grpc\Auth\V1\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.v1.HealthService/Health',
        $argument,
        ['\App\Grpc\Auth\V1\HealthResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Auth\V1\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Ready(\App\Grpc\Auth\V1\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.v1.HealthService/Ready',
        $argument,
        ['\App\Grpc\Auth\V1\ReadyResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Auth\V1\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Live(\App\Grpc\Auth\V1\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.v1.HealthService/Live',
        $argument,
        ['\App\Grpc\Auth\V1\LiveResponse', 'decode'],
        $metadata, $options);
    }

}
