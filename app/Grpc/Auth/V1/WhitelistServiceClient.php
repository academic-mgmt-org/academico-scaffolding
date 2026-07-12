<?php
// GENERATED CODE -- DO NOT EDIT!

namespace App\Grpc\Auth\V1;

/**
 */
class WhitelistServiceClient extends \Grpc\BaseStub {

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
    public function GetAll(\App\Grpc\Auth\V1\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.v1.WhitelistService/GetAll',
        $argument,
        ['\App\Grpc\Auth\V1\WhitelistResponse', 'decode'],
        $metadata, $options);
    }

}
