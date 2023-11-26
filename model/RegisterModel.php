<?php

require 'third-party/PHPMailer/src/Exception.php';
require 'third-party/PHPMailer/src/PHPMailer.php';
require 'third-party/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class RegisterModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getHash($email){
        $result = $this->database->uniqueQuery("SELECT Hash FROM user WHERE email = '$email'");
        return $result['Hash'];
    }

    public function validateEmail($email, $hash, $nameComplete){
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->AuthType = 'PLAIN';
            $mail->SMTPAuth = true;
            //Correo de remitente (que sea hotmail)
            $mail->Username = 'pregunta2.ok@hotmail.com';
            //Clave del correo
            $mail->Password = 'Programacionweb';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->Timeout = 30;

            //Nuevamente correo de remitente
            $mail->setFrom('pregunta2.ok@hotmail.com', "Pregunta2");
            $mail->addAddress($email, $nameComplete);
            $mail->Subject = 'Confirma tu cuenta... y empeza a responder!';

            $mail->isHTML(true);
            $mail->Body = '<h1> Tu URL para el activar tu correo </h1>
            Haz click <a href="' . $_SERVER['SERVER_NAME'] . '/Login/validateEmail?hash=' . 
                $hash . '">en este link</a> para validar tu email';

            $mail->AltBody = 'Si no puedes ver este mensaje, por favor,
            habilita el soporte para HTML en tu cliente de correo.';
            $mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }

    public function saveUser($nameComplete, $birth, $gender, $country, $city, $mail, $nameUser, $pass, $photo)
    {
        $ret = false;
        $hashPass = md5($pass);
        $token = openssl_random_pseudo_bytes(16);
        $hashValidate = md5($token);

        if ($this->validateUser($mail, $nameUser)) {
            $query = "INSERT INTO user (fullname, birth_date, gender, idCountry, idCity, email, username, password, profile_picture, Hash, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->database->prepare($query);
            $stmt->bind_param("sssidssssss", $nameComplete, $birth, $gender, $country, $city, $mail, $nameUser, $pass, $photo, $hashValidate, $hashPass);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $ret = true;
            }
        }
        return $ret;
    }

    public function validateUser($mail, $nameUser)
    {
        $query = "SELECT COUNT(*) AS count FROM user WHERE email = ? OR username = ?";
        $stmt = $this->database->prepare($query);
        $stmt->bind_param("ss", $mail, $nameUser);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            return false;
            // El mail o el nombre ya existen en la base de datos
        } else {
            return true;
        }
    }

    public function validatePassword($pass, $passValidate)
    {
        return $pass === $passValidate;
    }

}