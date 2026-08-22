<?php
namespace App\Services\Utilitary;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Notification{
    public function __construct(
        private PHPMailer $mail
    ) {}

    public function sendByEmail(string $recepientName, string $recepientEmail, string $subjetc, string $bodyhtml) : bool{

        try{
            //Settings
            $this->mail->isSMTP();
            $this->mail->SMTPAuth = true;
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';

            //Account
            $this->mail->Username   = env('MAIL_USERNAME');
            $this->mail->Password   = env('MAIL_PASSWORD');
            $this->mail->Host = env('MAIL_HOST');
            $this->mail->Port = env('MAIL_PORT');

            $this->mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

            //Destinaty
            $this->mail->addAddress($recepientEmail, $recepientName);

            //Message
            $this->mail->Subject = $subjetc;
            $this->mail->Body    = $bodyhtml;
            $this->mail->send();
        }
        catch(Exception $e){
            return false;
        }
        return true;
    }
}
?>
