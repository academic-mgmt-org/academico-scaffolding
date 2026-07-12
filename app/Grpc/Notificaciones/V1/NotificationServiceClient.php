<?php
// GENERATED CODE -- DO NOT EDIT!

namespace App\Grpc\Notificaciones\V1;

/**
 */
class NotificationServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \App\Grpc\Notificaciones\V1\ListNotificationsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ListNotifications(\App\Grpc\Notificaciones\V1\ListNotificationsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/notificaciones.v1.NotificationService/ListNotifications',
        $argument,
        ['\App\Grpc\Notificaciones\V1\ListNotificationsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Notificaciones\V1\ListNotificationsRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function RecentNotifications(\App\Grpc\Notificaciones\V1\ListNotificationsRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/notificaciones.v1.NotificationService/RecentNotifications',
        $argument,
        ['\App\Grpc\Notificaciones\V1\ListNotificationsResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Notificaciones\V1\CountUnreadRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CountUnread(\App\Grpc\Notificaciones\V1\CountUnreadRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/notificaciones.v1.NotificationService/CountUnread',
        $argument,
        ['\App\Grpc\Notificaciones\V1\CountUnreadResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Notificaciones\V1\CreateNotificationRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreateNotification(\App\Grpc\Notificaciones\V1\CreateNotificationRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/notificaciones.v1.NotificationService/CreateNotification',
        $argument,
        ['\App\Grpc\Notificaciones\V1\NotificationResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Notificaciones\V1\MarkReadRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function MarkAsRead(\App\Grpc\Notificaciones\V1\MarkReadRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/notificaciones.v1.NotificationService/MarkAsRead',
        $argument,
        ['\App\Grpc\Notificaciones\V1\NotificationResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \App\Grpc\Notificaciones\V1\MarkAllReadRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function MarkAllAsRead(\App\Grpc\Notificaciones\V1\MarkAllReadRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/notificaciones.v1.NotificationService/MarkAllAsRead',
        $argument,
        ['\App\Grpc\Notificaciones\V1\GenericResponse', 'decode'],
        $metadata, $options);
    }

}
