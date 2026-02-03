<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Chandachewe\Whatsapp\Messages\TextMessage;
use Chandachewe\Whatsapp\Messages\ButtonMessage;

class WhatsAppService
{
    /**
     * Send WhatsApp message with QR code and Lightning link
     */
    public static function sendPaymentQRCode($phoneNumber, $lnurl, $qrCodeUrl, $amountSats, $amountKwacha)
    {
        try {
            // Format phone number (ensure it starts with country code)
            $formattedPhone = self::formatPhoneNumber($phoneNumber);
            
            if (empty($formattedPhone)) {
                Log::error('Invalid phone number format', [
                    'original' => $phoneNumber,
                    'formatted' => $formattedPhone
                ]);
                return false;
            }

            // Get WhatsApp configuration
            $version = config('services.whatsapp.version', 'v19.0');
            $businessPhoneNumberId = config('services.whatsapp.business_phone_number_id');
            $token = config('services.whatsapp.token');
            $baseUri = config('services.whatsapp.base_uri', 'https://graph.facebook.com');

            if (!$businessPhoneNumberId || !$token) {
                Log::error('WhatsApp configuration missing: business_phone_number_id or token');
                return false;
            }

            // Set BASE_URI in environment for the WhatsApp package classes
            if (!isset($_ENV['BASE_URI'])) {
                $_ENV['BASE_URI'] = $baseUri;
            }

            // Create message content
            $message = self::createPaymentMessage($amountSats, $amountKwacha, $lnurl, $qrCodeUrl);

            // Send text message with QR code image using ButtonMessage (if QR code URL available)
            if ($qrCodeUrl) {
                return self::sendWithImage($version, $businessPhoneNumberId, $formattedPhone, $token, $message, $qrCodeUrl, $amountSats);
            } else {
                // Send text message only
                return self::sendTextMessage($version, $businessPhoneNumberId, $formattedPhone, $token, $message);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp service error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send text message with QR code image using ButtonMessage
     */
    private static function sendWithImage($version, $businessPhoneNumberId, $phoneNumber, $token, $message, $qrCodeUrl, $amountSats)
    {
        try {
            $buttonMessage = new ButtonMessage(
                $version,
                $businessPhoneNumberId,
                $phoneNumber,
                $token
            );

            // Send message with QR code image in header
            $response = $buttonMessage->button(
                'Bitcoin Payment QR Code',
                $message,
                'Scan the QR code above or use the Lightning link in the message',
                [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'qr_code_' . time(),
                                'title' => 'View QR Code'
                            ]
                        ]
                    ]
                ],
                'image', // Use image type for header
                $qrCodeUrl // QR code image URL
            );

            $responseData = json_decode($response, true);
            
            if (isset($responseData['messages']) || (isset($responseData['error']) && $responseData['error']['code'] === 131047)) {
                // Success or user needs to initiate conversation first
                Log::info('WhatsApp message with QR code sent', [
                    'phone' => $phoneNumber,
                    'response' => $responseData
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp message with QR code', [
                    'phone' => $phoneNumber,
                    'response' => $response
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp message with image: ' . $e->getMessage());
            // Fallback to text message only
            return self::sendTextMessage($version, $businessPhoneNumberId, $phoneNumber, $token, $message);
        }
    }

    /**
     * Send text message only
     */
    private static function sendTextMessage($version, $businessPhoneNumberId, $phoneNumber, $token, $message)
    {
        try {
            $textMessage = new TextMessage(
                $version,
                $businessPhoneNumberId,
                $phoneNumber,
                $token
            );

            $response = $textMessage->text($message);
            $responseData = json_decode($response, true);

            if (isset($responseData['messages']) || (isset($responseData['error']) && $responseData['error']['code'] === 131047)) {
                // Success or user needs to initiate conversation first
                Log::info('WhatsApp text message sent', [
                    'phone' => $phoneNumber,
                    'response' => $responseData
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp text message', [
                    'phone' => $phoneNumber,
                    'response' => $response
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp text message: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Format phone number to international format (WhatsApp Business API format)
     */
    private static function formatPhoneNumber($phone)
    {
        if (empty($phone) || !is_string($phone)) {
            Log::warning('Empty or invalid phone number provided', ['phone' => $phone]);
            return null;
        }

        // Remove all non-numeric characters
        $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);

        // Check if phone is empty after cleaning
        if (empty($cleanedPhone)) {
            Log::warning('Phone number is empty after cleaning', ['original' => $phone]);
            return null;
        }

        // If already starts with country code (260), validate and return
        if (substr($cleanedPhone, 0, 3) === '260') {
            $phoneWithoutCountry = substr($cleanedPhone, 3);
            // Zambian numbers should be 9 digits after country code (allow 8-10 for flexibility)
            if (strlen($phoneWithoutCountry) >= 8 && strlen($phoneWithoutCountry) <= 10) {
                return $cleanedPhone;
            } else {
                Log::warning('Phone number with country code has invalid length', [
                    'original' => $phone,
                    'formatted' => $cleanedPhone,
                    'length_after_country' => strlen($phoneWithoutCountry)
                ]);
                // Still return it, but log a warning
                return $cleanedPhone;
            }
        }

        // If starts with 0, replace with country code
        if (substr($cleanedPhone, 0, 1) === '0') {
            $formattedPhone = '260' . substr($cleanedPhone, 1);
        } else {
            // If doesn't start with country code, add it
            $formattedPhone = '260' . $cleanedPhone;
        }

        // Validate final length (Zambian numbers: 260 + 8-10 digits = 11-13 digits total)
        $phoneWithoutCountry = substr($formattedPhone, 3);
        if (strlen($phoneWithoutCountry) < 8 || strlen($phoneWithoutCountry) > 10) {
            Log::warning('Phone number length validation warning', [
                'original' => $phone,
                'cleaned' => $cleanedPhone,
                'formatted' => $formattedPhone,
                'length_after_country' => strlen($phoneWithoutCountry)
            ]);
            // Still return it, but log a warning
        }

        Log::info('Phone number formatted', [
            'original' => $phone,
            'formatted' => $formattedPhone
        ]);

        // Return without + prefix (WhatsApp Business API uses format: 260971176778)
        return $formattedPhone;
    }

    /**
     * Create payment message
     */
    private static function createPaymentMessage($amountSats, $amountKwacha, $lnurl, $qrCodeUrl)
    {
        $message = "🎉 *Payment Successful!*\n\n";
        $message .= "Your Bitcoin purchase has been confirmed.\n\n";
        $message .= "📊 *Transaction Details:*\n";
        $message .= "Amount: " . number_format($amountSats) . " SATS\n";
        $message .= "Amount Paid: " . number_format($amountKwacha, 2) . " ZMW\n\n";
        $message .= "⚡ *To Receive Your Bitcoin:*\n";
        $message .= "1. Scan the QR code below with your Lightning wallet\n";
        $message .= "2. Or use this Lightning link:\n";
        $message .= $lnurl . "\n\n";
        $message .= "⏰ *Important:* The QR code expires in 10 minutes.\n\n";
        $message .= "Thank you for using Bit2Kwacha! 🚀";

        return $message;
    }
}

