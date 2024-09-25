<?php
function sendDiscordNotification($message) {
    $webhookUrl = "https://discord.com/api/webhooks/1216835175019708526/fO-gKCvZgmIeIYEhgTcQFYKKFMMJAiEXlnCY97FygmzNfTdHPPoUGybEpMXm_oNZ-6Sm";
    
    $payload = json_encode([
        'content' => $message,
    ]);
    
    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "cURL error: $error_msg";
    }
    
    curl_close($ch);
    return $response;
}
?>
