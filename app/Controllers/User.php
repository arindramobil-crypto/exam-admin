<?php

namespace App\Controllers;

use App\Models\AdminUserModel;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new AdminUserModel();
    }

    /**
     * List all admin users
     */
    public function index()
    {
        $currentRole = session()->get('admin_role');
        $users = $this->userModel->orderBy('id', 'ASC')->findAll();

        return view('user/index', [
            'title'       => 'Manajemen Admin & Operator',
            'users'       => $users,
            'currentRole' => $currentRole,
            'currentId'   => session()->get('admin_id'),
        ]);
    }

    /**
     * Form to create a new user
     */
    public function create()
    {
        $this->ensureSuperadmin();

        return view('user/form', [
            'title' => 'Tambah Admin Baru',
            'user'  => null,
        ]);
    }

    /**
     * Store a new user
     */
    public function store()
    {
        $this->ensureSuperadmin();

        $nama     = trim($this->request->getPost('nama') ?? '');
        $username = trim($this->request->getPost('username') ?? '');
        $password = $this->request->getPost('password') ?? '';
        $role     = $this->request->getPost('role') ?? 'operator';
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($nama) || empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Semua kolom bertanda * wajib diisi!')->withInput();
        }

        if (strlen($password) < 6) {
            return redirect()->back()->with('error', 'Password minimal 6 karakter!')->withInput();
        }

        // Check unique username
        $existing = $this->userModel->where('username', $username)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Username sudah digunakan, silakan pilih username lain!')->withInput();
        }

        if (!in_array($role, ['superadmin', 'admin', 'operator'])) {
            $role = 'operator';
        }

        $this->userModel->insert([
            'nama'      => $nama,
            'username'  => $username,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'role'      => $role,
            'is_active' => $isActive,
        ]);

        return redirect()->to(base_url('user'))->with('success', 'Akun admin berhasil ditambahkan.');
    }

    /**
     * Form to edit a user
     */
    public function edit(int $id)
    {
        $this->ensureSuperadmin();

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('user'))->with('error', 'User tidak ditemukan!');
        }

        return view('user/form', [
            'title' => 'Edit Admin: ' . $user['nama'],
            'user'  => $user,
        ]);
    }

    /**
     * Update user
     */
    public function update(int $id)
    {
        $this->ensureSuperadmin();

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('user'))->with('error', 'User tidak ditemukan!');
        }

        $nama     = trim($this->request->getPost('nama') ?? '');
        $username = trim($this->request->getPost('username') ?? '');
        $password = $this->request->getPost('password') ?? '';
        $role     = $this->request->getPost('role') ?? 'operator';
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($nama) || empty($username)) {
            return redirect()->back()->with('error', 'Nama dan username wajib diisi!')->withInput();
        }

        // Check username uniqueness if changed
        $existing = $this->userModel->where('username', $username)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Username sudah dipakai oleh pengguna lain!')->withInput();
        }

        // Check if removing the last active superadmin
        if ($user['role'] === 'superadmin' && ($role !== 'superadmin' || $isActive == 0)) {
            $superadminCount = $this->userModel->where('role', 'superadmin')->where('is_active', 1)->countAllResults();
            if ($superadminCount <= 1) {
                return redirect()->back()->with('error', 'Tidak dapat mengubah role atau menonaktifkan satu-satunya Superadmin aktif!')->withInput();
            }
        }

        $data = [
            'nama'      => $nama,
            'username'  => $username,
            'role'      => in_array($role, ['superadmin', 'admin', 'operator']) ? $role : 'operator',
            'is_active' => $isActive,
        ];

        // Update password only if provided
        if (!empty($password)) {
            if (strlen($password) < 6) {
                return redirect()->back()->with('error', 'Password minimal 6 karakter!')->withInput();
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        // If updating currently logged in user, refresh session info
        if ((int)session()->get('admin_id') === $id) {
            session()->set([
                'admin_nama' => $nama,
                'admin_role' => $data['role'],
            ]);
        }

        return redirect()->to(base_url('user'))->with('success', 'Data admin berhasil diperbarui.');
    }

    /**
     * Delete user
     */
    public function delete(int $id)
    {
        $this->ensureSuperadmin();

        $currentId = (int)session()->get('admin_id');
        if ($currentId === $id) {
            return redirect()->to(base_url('user'))->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan!');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('user'))->with('error', 'User tidak ditemukan!');
        }

        if ($user['role'] === 'superadmin') {
            $superadminCount = $this->userModel->where('role', 'superadmin')->where('is_active', 1)->countAllResults();
            if ($superadminCount <= 1) {
                return redirect()->to(base_url('user'))->with('error', 'Tidak dapat menghapus satu-satunya Superadmin!');
            }
        }

        $this->userModel->delete($id);

        return redirect()->to(base_url('user'))->with('success', 'Akun admin berhasil dihapus.');
    }

    /**
     * Helper to enforce superadmin permissions
     */
    protected function ensureSuperadmin()
    {
        if (session()->get('admin_role') !== 'superadmin') {
            redirect()->to(base_url('user'))->with('error', 'Hanya Superadmin yang memiliki wewenang untuk mengubah data admin.')->send();
            exit;
        }
    }
}

