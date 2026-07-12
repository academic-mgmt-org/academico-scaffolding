<?php
// GENERATED CODE -- DO NOT EDIT!

namespace App\Grpc\Auth\V1;

/**
 */
class AuthServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \App\Grpc\Auth\V1\LoginRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Login(\App\Grpc\Auth\V1\LoginRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.v1.AuthService/Login',
        $argument,
        ['\App\Grpc\Auth\V1\LoginResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Auth\V1\RefreshTokenRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function RefreshToken(\App\Grpc\Auth\V1\RefreshTokenRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.v1.AuthService/RefreshToken',
        $argument,
        ['\App\Grpc\Auth\V1\LoginResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Auth\V1\ForgotPasswordRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ForgotPassword(\App\Grpc\Auth\V1\ForgotPasswordRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.v1.AuthService/ForgotPassword',
        $argument,
        ['\App\Grpc\Auth\V1\GenericResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Auth\V1\ResetPasswordRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ResetPassword(\App\Grpc\Auth\V1\ResetPasswordRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.v1.AuthService/ResetPassword',
        $argument,
        ['\App\Grpc\Auth\V1\GenericResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Auth\V1\ValidateTokenRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ValidateToken(\App\Grpc\Auth\V1\ValidateTokenRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.v1.AuthService/ValidateToken',
        $argument,
        ['\App\Grpc\Auth\V1\ValidateTokenResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Auth\V1\LogoutRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function Logout(\App\Grpc\Auth\V1\LogoutRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.v1.AuthService/Logout',
        $argument,
        ['\App\Grpc\Auth\V1\GenericResponse', 'decode'],
        $metadata, $options);
    }

}
