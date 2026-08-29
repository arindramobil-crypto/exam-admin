<?php
namespace App\Models;
use CodeIgniter\Model;

class RoomModel extends Model
{
    protected $table      = 'rooms';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_ruang','gedung','lantai','kapasitas','keterangan'];
    protected $useTimestamps = true;
}