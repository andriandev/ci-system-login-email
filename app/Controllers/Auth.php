<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\UsersModel;

class Auth extends BaseController
{
    protected $authModel;
    protected $usersModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->usersModel = new UsersModel();
    }

    public function register()
    {
        $data = [
            'title' => 'Register',
            'validation' => session()->getFlashdata('validation')
        ];

        return view('auth/register', $data);
    }

    public function login()
    {
        $data = [
            'title' => 'Login',
            'validation' => session()->getFlashdata('validation')
        ];

        return view('auth/login', $data);
    }

    public function forgot_pw()
    {
        $token = htmlspecialchars($this->request->getVar('token'));
        $title = 'Forgot Password';
        $status = 'forgot-pw';

        if (!empty($token)) {
            $data = $this->authModel->getToken('token', $token);

            if ($data) {
                if ($data['is_active'] == 1) {
                    $title = 'New Password';
                    $status = 'new-pw';
                }
            }
        }

        $data = [
            'title' => $title,
            'status' => $status,
            'validation' => session()->getFlashdata('validation')
        ];

        return view('auth/forgot-pw', $data);
    }

    public function logout()
    {
        // Destroy all session
        $ses = [
            'id',
            'email',
            'name',
            'image',
            'is_login'
        ];
        session()->remove($ses);

        // Session setflashdata
        session()->setFlashdata('alert', 'success');
        session()->setFlashdata('message', 'Logout successfully.');

        return redirect()->to(base_url('/'));
    }

    public function check_register()
    {
        // Validation
        if (!$this->validate([
            'email' => [
                'rules' => 'required|is_unique[users.email]|valid_email',
                'errors' => [
                    'required' => 'Email field is required cannot be empty.',
                    'is_unique' => 'Email already exists, please use another email.',
                    'valid_email' => 'Please enter a valid email.'
                ]
            ],
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Name field is required cannot be empty.',
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password field is required cannot be empty.',
                    'min_length' => 'The password must be at least 6 characters.'
                ]
            ],
            'image' => [
                'rules' => 'uploaded[image]|max_size[image,1024]|mime_in[image,image/png,image/jpeg,image/jpg]|is_image[image]',
                'errors' => [
                    'uploaded' => 'Image field is required cannot be empty.',
                    'max_size' => 'Image files cannot be larger than 1 MB.',
                    'mime_in' => 'Please enter a valid image file.',
                    'is_image' => 'Please enter a valid image file.'
                ]
            ],
        ])) {
            session()->setFlashdata('validation', \Config\Services::validation()->getErrors());
            return redirect()->to(base_url('/register'))->withInput();
        };

        // Get data from request
        $email = htmlspecialchars($this->request->getVar('email'));
        $name = htmlspecialchars($this->request->getVar('name'));

        // Get file image from request
        $fileImage = $this->request->getFile('image');
        // Generate random name
        $nameImage = $fileImage->getRandomName();

        // Move file image to public dir
        $fileImage->move('assets/img', $nameImage);

        // Insert data to database
        $save_user = $this->usersModel->save([
            'email' => $email,
            'name' => $name,
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'image' => $nameImage,
            'is_active' => 0,
        ]);

        if ($save_user) {
            // Generate random token
            helper('text');
            $token = random_string('alnum', 42);

            // Get id users from database
            $user = $this->usersModel->getUser('email', $email);

            if ($user) {
                //Insert token to database
                $save_token = $this->authModel->save([
                    'id_user' => $user['id'],
                    'token' => $token,
                    'is_active' => 1
                ]);

                if ($save_token) {
                    // Token succes insert to database
                    // Send email verivication
                    $linkVerification = base_url('/email-verify') . "?token=$token";
                    $message = "Please verify your email by clicking this link $linkVerification";
                    $sendMail = $this->authModel->send_mail($email, 'Email Verification', $message);

                    if ($sendMail) {
                        // If send mail success
                        session()->setFlashdata('alert', 'success');
                        session()->setFlashdata('message', 'Registration is successful, please check your email inbox (or spam folder) for verification.');
                    } else {
                        // If send mail failed
                        session()->setFlashdata('alert', 'warning');
                        session()->setFlashdata('message', 'Email failed to send, please try again.');

                        // Delete data user
                        $this->usersModel->delete($user['id']);

                        // Delete token
                        $this->authModel->where('token', $token)->delete();

                        // Delete image
                        unlink('assets/img/' . $nameImage);
                    }
                } else {
                    // Token failed insert to database
                    session()->setFlashdata('alert', 'danger');
                    session()->setFlashdata('message', 'Registration failed, a system error occurred.');
                    return redirect()->to(base_url('/register'))->withInput();
                }
            }


            return redirect()->to(base_url('/login'));
        } else {
            // Failed insert data
            session()->setFlashdata('alert', 'danger');
            session()->setFlashdata('message', 'Registration failed, a system error occurred.');
            return redirect()->to(base_url('/register'))->withInput();
        }
    }

    public function check_login()
    {
        // Validation
        if (!$this->validate([
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email field is required cannot be empty.',
                    'valid_email' => 'Please enter a valid email.'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password field is required cannot be empty.',
                ]
            ],
        ])) {
            session()->setFlashdata('validation', \Config\Services::validation()->getErrors());
            return redirect()->to(base_url('/login'))->withInput();
        };

        // Get data from request
        $email = htmlspecialchars($this->request->getVar('email'));
        $password = $this->request->getVar('password');

        // Get user by email from database
        $user = $this->usersModel->getUser('email', $email);

        if ($user) {
            // Check is_active
            if ($user['is_active'] == 1) {
                // Check password
                if (password_verify($password, $user['password'])) {
                    // Success login
                    $ses = [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'name' => $user['name'],
                        'image' => $user['image'],
                        'is_login' => true
                    ];
                    session()->set($ses);

                    session()->setFlashdata('alert', 'success');
                    session()->setFlashdata('message', 'Login success.');

                    return redirect()->to(base_url('/profile'));
                } else {
                    // Failed login = password wrong
                    session()->setFlashdata('alert', 'danger');
                    session()->setFlashdata('message', 'Login failed, the password you entered is incorrect.');
                }
            } else {
                // Failed login = is_active 0
                session()->setFlashdata('alert', 'warning');
                session()->setFlashdata('message', 'Login failed, please check email for verification');
            }
        } else {
            // Failed login = username or password not match
            session()->setFlashdata('alert', 'danger');
            session()->setFlashdata('message', 'Login failed, the email or password you entered does not match.');
        }

        return redirect()->to(base_url('/login'))->withInput();
    }

    public function check_forgot_pw()
    {
        if (!$this->validate([
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email field is required cannot be empty.',
                    'valid_email' => 'Please enter a valid email.'
                ]
            ],
        ])) {
            session()->setFlashdata('validation', \Config\Services::validation()->getErrors());
            return redirect()->to(base_url('/forgot-password'))->withInput();
        };

        $email = htmlspecialchars($this->request->getVar('email'));

        // Get data from database by email
        $user = $this->usersModel->getUser('email', $email);

        if ($user) {
            // Check is_active
            if ($user['is_active'] == 1) {
                // Generate random token
                helper('text');
                $token = random_string('alnum', 42);

                //Insert token to database
                $save_token = $this->authModel->save([
                    'id_user' => $user['id'],
                    'token' => $token,
                    'is_active' => 1
                ]);

                if ($save_token) {
                    // Token succes insert to database
                    // Send email reset link
                    $linkVerification = base_url('/forgot-password') . "?token=$token";
                    $message = "To reset your password please click this link $linkVerification";
                    $sendMail = $this->authModel->send_mail($email, 'Reset Password', $message);

                    if ($sendMail) {
                        // If send mail success
                        session()->setFlashdata('alert', 'success');
                        session()->setFlashdata('message', 'Email has been sent, please check your email inbox (or spam folder) for reset password.');
                    } else {
                        // If send mail failed
                        session()->setFlashdata('alert', 'warning');
                        session()->setFlashdata('message', 'Email failed to send, please try again.');

                        // Delete token
                        $this->authModel->where('token', $token)->delete();
                    }
                } else {
                    // Token failed insert to database
                    session()->setFlashdata('alert', 'danger');
                    session()->setFlashdata('message', 'Reset password failed, a system error occurred.');
                    return redirect()->to(base_url('/forgot-password'))->withInput();
                }
            } else {
                // Email not verified
                session()->setFlashdata('alert', 'danger');
                session()->setFlashdata('message', 'The user email address has not been verified.');
            }
        } else {
            // Email addres not registered
            session()->setFlashdata('alert', 'danger');
            session()->setFlashdata('message', 'Email address not registered.');
        }

        return redirect()->to(base_url('/forgot-password'))->withInput();
    }

    public function email_verify()
    {
        $token = htmlspecialchars($this->request->getVar('token'));

        if ($token) {
            // Check token from database
            $data = $this->authModel->getToken('token', $token);

            if ($data) {
                // If exist, check is_active token
                if ($data['is_active'] == 1) {
                    // Set is_active user 1
                    $this->usersModel->save([
                        'id' => $data['id_user'],
                        'is_active' => 1
                    ]);

                    // Set is_active token 0
                    $this->authModel->save([
                        'id' => $data['id'],
                        'is_active' => 0
                    ]);

                    session()->setFlashdata('alert', 'success');
                    session()->setFlashdata('message', 'Email has been successfully verified, please login.');
                } else {
                    session()->setFlashdata('alert', 'danger');
                    session()->setFlashdata('message', 'The token has already been used.');
                }
            } else {
                session()->setFlashdata('alert', 'danger');
                session()->setFlashdata('message', 'Invalid token.');
            }

            return redirect()->to(base_url('/login'));
        }

        // If empty token, redirect to home
        return redirect()->to(base_url('/'));
    }

    public function reset_password()
    {
        // Get data from request
        $token = $this->request->getVar('token');

        // Validation
        if (!$this->validate([
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password field is required cannot be empty.',
                    'min_length' => 'The password must be at least 6 characters.'
                ]
            ],
        ])) {
            session()->setFlashdata('validation', \Config\Services::validation()->getErrors());
            return redirect()->to(base_url('/forgot-password' . '?token=' . $token))->withInput();
        };

        // Get data from database by token
        $data = $this->authModel->getToken('token', $token);

        if ($data) {
            if ($data['is_active'] == 1) {
                // Update password
                $newPassword = password_hash($this->request->getVar('password'), PASSWORD_BCRYPT);

                $save_user = $this->usersModel->save([
                    'id' => $data['id_user'],
                    'password' => $newPassword
                ]);

                if ($save_user) {
                    // Set is_active token 0
                    $this->authModel->save([
                        'id' => $data['id'],
                        'is_active' => 0
                    ]);

                    session()->setFlashdata('alert', 'success');
                    session()->setFlashdata('message', 'Password reset was successful, please login.');

                    return redirect()->to(base_url('/login'));
                }
            }
        }

        session()->setFlashdata('alert', 'danger');
        session()->setFlashdata('message', 'Password reset failed.');

        return redirect()->to(base_url('/'));
    }
}
