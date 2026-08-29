<?php
namespace App\Models;
use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table      = 'attendances';
    protected $primaryKey = 'id';
    protected $allowedFields = ['exam_id','subject_id','participant_id','status','jam_absen','keterangan','dicatat_oleh'];
    protected $useTimestamps = true;
}