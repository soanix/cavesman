<?
class mail extends display{
    function __construct(){
        $this->db = new db();
        $this->config = array(
            "smartyList" => "comercios,promociones"
        );
		$this->find = '';
        parent::__construct();
    }
	function send(){
		require _ROOT_.'/vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

		$mail = new PHPMailer;

		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.server';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'emailusername';                 // SMTP username
		$mail->Password = 'emailpassword';                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to
		$mail->CharSet = 'UTF-8';
		$mail->setFrom('from@example.com', 'Name');
		$mail->addAddress('EMAILADDRESS', 'Info NAME');     // Add a recipient
		$mail->addReplyTo($this->p("mail"), $this->p("name"));
		$mail->addCC('cc@example.com');
		$mail->addBCC('bcc@example.com');

		//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $this->p("subject");
		$mail->Body    = "<p>".nl2br($this->p("message"))."<p>";
		//$mail->AltBody = $this->p("message");

		if(!$mail->send()) {
		    /*echo 'Message could not be sent.';
		    echo 'Mailer Error: ' . $mail->ErrorInfo;*/
			return "";
		} else {
		    return $this->l('Mensaje enviado correctamente. Gracias!');
		}
	}
}
