<?php

namespace App\Controllers;

use App\Models\SettingModel;

class Setting extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->getAllKeyValue();

        return view('setting/index', [
            'title'    => 'Pengaturan Sekolah & Kop Surat',
            'settings' => $settings,
        ]);
    }

    public function save()
    {
        $settingModel = new SettingModel();
        $fields = [
            'nama_sekolah',
            'alamat_sekolah',
            'kota',
            'telepon',
            'email',
            'website',
            'nama_kepala_sekolah',
            'nip_kepala_sekolah',
            'nama_ketua_panitia',
            'nip_ketua_panitia',
            'ttd_kartu_jabatan',
        ];

        foreach ($fields as $f) {
            $val = $this->request->getPost($f);
            if ($val !== null) {
                $settingModel->setVal($f, trim($val));
            }
        }

        // Handle logo upload
        $logoFile = $this->request->getFile('logo_file');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/logo/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newName = 'logo_' . time() . '.' . $logoFile->getExtension();
            $logoFile->move($uploadPath, $newName);
            $settingModel->setVal('logo', 'uploads/logo/' . $newName);
        }

        // Handle logo removal
        if ($this->request->getPost('hapus_logo') == '1') {
            $oldLogo = $settingModel->getVal('logo');
            if ($oldLogo && file_exists(FCPATH . $oldLogo)) {
                @unlink(FCPATH . $oldLogo);
            }
            $settingModel->setVal('logo', '');
        }

        return redirect()->to(base_url('setting'))->with('success', 'Pengaturan sekolah & kop dokumen berhasil disimpan!');
    }
}
