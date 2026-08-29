<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'key';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['key', 'value', 'updated_at'];

    /**
     * Get all settings as key-value associative array
     */
    public function getAllKeyValue(): array
    {
        $rows = $this->findAll();
        $res = [];
        foreach ($rows as $r) {
            $res[$r['key']] = $r['value'];
        }
        return $res;
    }

    /**
     * Get a specific setting value with fallback
     */
    public function getVal(string $key, $default = '')
    {
        $row = $this->where('key', $key)->first();
        return $row['value'] ?? $default;
    }

    /**
     * Set a setting value
     */
    public function setVal(string $key, $value): bool
    {
        $existing = $this->where('key', $key)->first();
        $now = date('Y-m-d H:i:s');
        if ($existing) {
            return $this->update($key, ['value' => $value, 'updated_at' => $now]);
        }
        return (bool)$this->insert(['key' => $key, 'value' => $value, 'updated_at' => $now]);
    }
}
