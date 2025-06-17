<?php
class EmailService {
    public static function sendPasswordChangeNotification($to, $username) {
        $subject = "Password Change Notification - Task Manager";
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 20px auto; padding: 20px; }
                .alert { background: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; }
                .footer { margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Password Change Notification</h2>
                <p>Hello $username,</p>
                <div class='alert'>
                    <p>Your password was recently changed on your Task Manager account.</p>
                    <p>If you did not make this change, please contact support immediately.</p>
                </div>
                <p>Time of change: " . date('Y-m-d H:i:s') . "</p>
                <div class='footer'>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Task Manager <noreply@taskmanager.com>" . "\r\n";

        return mail($to, $subject, $message, $headers);
    }

    public static function sendEmailChangeNotification($old_email, $new_email, $username) {
        // Send to old email
        $subject = "Email Change Notification - Task Manager";
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 20px auto; padding: 20px; }
                .alert { background: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; }
                .footer { margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Email Address Change Notification</h2>
                <p>Hello $username,</p>
                <div class='alert'>
                    <p>Your email address has been changed from:</p>
                    <p><strong>$old_email</strong></p>
                    <p>to:</p>
                    <p><strong>$new_email</strong></p>
                    <p>If you did not make this change, please contact support immediately.</p>
                </div>
                <p>Time of change: " . date('Y-m-d H:i:s') . "</p>
                <div class='footer'>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Task Manager <noreply@taskmanager.com>" . "\r\n";

        Send to both old and new email addresses
        mail($old_email, $subject, $message, $headers);
        return mail($new_email, $subject, $message, $headers);
    }
}
