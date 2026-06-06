<?php

namespace App\Services;

use Kreait\Firebase\Contract\Database;

class FirebaseNotificationService
{
    protected $database;
    protected $messaging;

    public function __construct(Database $database)
    {
        $this->database = $database;
        try {
            $this->messaging = app('firebase.messaging');
        } catch (\Exception $e) {
            \Log::error("Firebase Messaging Error: " . $e->getMessage());
        }
    }

    /**
     * Send a notification to a specific topic or role in the Realtime Database and FCM.
     * 
     * @param string $topic e.g., 'manufacturing', 'admin'
     * @param string $title
     * @param string $message
     * @param array $additionalData
     */
    public function sendNotification(string $topic, string $title, string $message, array $additionalData = [])
    {
        $notificationData = array_merge([
            'title' => $title,
            'message' => $message,
            'timestamp' => now()->timestamp,
            'read' => false,
        ], $additionalData);

        // 1. Push to the realtime database under /notifications/{topic}
        try {
            // This creates a unique ID for each notification
            $this->database->getReference('notifications/' . $topic)
                ->push($notificationData);
        } catch (\Exception $e) {
            \Log::error("Firebase RTDB Notification Error: " . $e->getMessage());
        }

        // 2. Send FCM Push Notification to Mobile App via Topic
        if ($this->messaging) {
            try {
                $notification = \Kreait\Firebase\Messaging\Notification::create($title, $message);
                
                // Convert all additionalData values to strings as FCM data payload requires string values
                $stringifiedData = [];
                foreach ($additionalData as $key => $value) {
                    $stringifiedData[(string)$key] = (string)$value;
                }

                $cloudMessage = \Kreait\Firebase\Messaging\CloudMessage::new()
                    ->withTopic($topic)
                    ->withNotification($notification)
                    ->withData($stringifiedData);

                $this->messaging->send($cloudMessage);
            } catch (\Exception $e) {
                \Log::error("Firebase FCM Push Notification Error: " . $e->getMessage());
            }
        }
    }
}
