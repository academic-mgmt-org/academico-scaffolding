<?php
// GENERATED CODE -- DO NOT EDIT!

namespace App\Grpc\Notificaciones\V1;

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
     * @param \App\Grpc\Notificaciones\V1\HealthRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Health(\App\Grpc\Notificaciones\V1\HealthRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/notificaciones.v1.HealthService/Health',
        $argument,
        ['\App\Grpc\Notificaciones\V1\HealthResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Notificaciones\V1\ReadyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Ready(\App\Grpc\Notificaciones\V1\ReadyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/notificaciones.v1.HealthService/Ready',
        $argument,
        ['\App\Grpc\Notificaciones\V1\ReadyResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Notificaciones\V1\LiveRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Live(\App\Grpc\Notificaciones\V1\LiveRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/notificaciones.v1.HealthService/Live',
        $argument,
        ['\App\Grpc\Notificaciones\V1\LiveResponse', 'decode'],
        $metadata, $options);
    }

}
