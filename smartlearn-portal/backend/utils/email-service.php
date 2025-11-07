<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configure();
    }
    
    private function configure() {
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host       = SMTP_HOST;
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = SMTP_USER;
        $this->mail->Password   = SMTP_PASS;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = SMTP_PORT;
        
        // Recipients
        $this->mail->setFrom(SMTP_USER, 'SmartLearn Portal');
        $this->mail->addReplyTo(SMTP_USER, 'SmartLearn Portal');
    }
    
    public function sendWelcomeEmail($to_email, $to_name) {
        try {
            $this->mail->addAddress($to_email, $to_name);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Welcome to SmartLearn Portal';
            $this->mail->Body    = $this->getWelcomeEmailTemplate($to_name);
            $this->mail->AltBody = "Welcome to SmartLearn Portal, {$to_name}! Start your Python learning journey today.";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
    
    public function sendTestResultEmail($to_email, $to_name, $test_title, $score) {
        try {
            $this->mail->addAddress($to_email, $to_name);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = "Test Result: {$test_title}";
            $this->mail->Body    = $this->getTestResultTemplate($to_name, $test_title, $score);
            $this->mail->AltBody = "Hello {$to_name}, you scored {$score}% in {$test_title}.";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
    
    private function getWelcomeEmailTemplate($name) {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                    .content { padding: 30px; background: #f9f9f9; }
                    .footer { padding: 20px; text-align: center; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Welcome to SmartLearn Portal!</h1>
                    </div>
                    <div class='content'>
                        <h2>Hello {$name},</h2>
                        <p>Welcome to your Python learning journey! We're excited to have you on board.</p>
                        <p>With SmartLearn Portal, you can:</p>
                        <ul>
                            <li>Take interactive Python tests</li>
                            <li>Track your learning progress</li>
                            <li>Challenge yourself with code exercises</li>
                            <li>Learn at your own pace</li>
                        </ul>
                        <p>Start by exploring the available tests and see how much you can learn!</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; 2024 SmartLearn Portal. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
    
    private function getTestResultTemplate($name, $test_title, $score) {
        $performance = $score >= 80 ? 'excellent' : ($score >= 60 ? 'good' : 'needs improvement');
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                    .content { padding: 30px; background: #f9f9f9; }
                    .score { font-size: 2em; font-weight: bold; text-align: center; margin: 20px 0; }
                    .footer { padding: 20px; text-align: center; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Test Results</h1>
                    </div>
                    <div class='content'>
                        <h2>Hello {$name},</h2>
                        <p>You have completed the test: <strong>{$test_title}</strong></p>
                        <div class='score' style='color: " . ($score >= 80 ? '#00ff88' : ($score >= 60 ? '#ffaa00' : '#ff4444')) . ";'>
                            Score: {$score}%
                        </div>
                        <p>Your performance is <strong>{$performance}</strong>. " . 
                        ($score >= 80 ? "Great job! Keep up the good work!" : 
                         ($score >= 60 ? "Good effort! Review the topics you missed." : 
                         "Don't worry! Review the material and try again.")) . "</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; 2024 SmartLearn Portal. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
}
?>