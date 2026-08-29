<?php
namespace App\Controllers;
use App\Models\AdminUserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }
        return view('auth/login');
    }

    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $model = new AdminUserModel();
        $user = $model->where('username', $username)->where('is_active', 1)->first();
        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'admin_logged_in' => true,
                'admin_id'   => $user['id'],
                'admin_nama' => $user['nama'],
                'admin_role' => $user['role'],
            ]);
            return redirect()->to(base_url('dashboard'));
        }
        return redirect()->back()->with('error', 'Username atau password salah!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'));
    }
}