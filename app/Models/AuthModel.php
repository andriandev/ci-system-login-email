<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table      = 'users_verify';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_user', 'token', 'is_active'];
    protected $useTimestamps = true;

    public function send_mail($emailTo, $subject, $message)
    {
        $email = \Config\Services::email();

        $email->setFrom('beliamelkaa@gmail.com', 'AndrianDev');
        $email->setTo($emailTo);
        // $email->setCC('another@another-example.com');
        // $email->setBCC('them@their-example.com');

        $email->setSubject($subject);
        $email->setMessage($message);

        // Return bool
        return $email->send();
    }

    public function getToken($option, $value)
    {
        $data = $this->where([$option => $value])->get()->getResultArray();
        if ($data) {
            $data = $data[0];
        } else {
            $data = null;
        }
        return $data;
    }
}
