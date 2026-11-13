<?php

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

class MoodleViolationModel extends Model
{
    protected $table      = 'local_cbt_kiosk_logs';
    protected $primaryKey = 'id';
    protected $DBGroup    = 'moodle';

    /**
     * Get recent violation logs for a list of quiz IDs
     */
    public function getViolationsByQuizIds(array $quizIds, int $limit = 50): array
    {
        if (empty($quizIds)) {
            return [];
        }

        try {
            return $this->db->table('local_cbt_kiosk_logs l')
                ->select('l.*, u.firstname, u.lastname, u.username, u.idnumber, q.name as quiz_name')
                ->join('user u', 'u.id = l.userid', 'left')
                ->join('quiz q', 'q.id = l.quizid', 'left')
                ->whereIn('l.quizid', $quizIds)
                ->orderBy('l.timecreated', 'DESC')
                ->limit($limit)
                ->get()->getResultArray();
        } catch (Throwable $e) {
            log_message('error', 'MoodleViolationModel::getViolationsByQuizIds failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get aggregated violation statistics per user for a list of quiz IDs
     * Returns array keyed by userid:
     * [
     *   userid => [
     *     'total_violations' => int,
     *     'is_locked'        => bool,
     *     'is_unlocked'      => bool,
     *     'last_action'      => string,
     *     'last_device'      => string,
     *     'last_time'        => int,
     *   ]
     * ]
     */
    public function getStudentViolationStats(array $quizIds): array
    {
        if (empty($quizIds)) {
            return [];
        }

        try {
            $logs = $this->db->table('local_cbt_kiosk_logs')
                ->whereIn('quizid', $quizIds)
                ->orderBy('timecreated', 'ASC')
                ->get()->getResultArray();

            $stats = [];
            foreach ($logs as $log) {
                $uid = (int)$log['userid'];
                if (!isset($stats[$uid])) {
                    $stats[$uid] = [
                        'total_violations' => 0,
                        'is_locked'        => false,
                        'is_unlocked'      => false,
                        'last_action'      => '',
                        'last_device'      => '',
                        'last_time'        => 0,
                    ];
                }

                $stats[$uid]['last_action'] = $log['action'];
                $stats[$uid]['last_device'] = $log['device_type'] ?? '';
                $stats[$uid]['last_time']   = (int)$log['timecreated'];

                if ($log['action'] === 'locked') {
                    $stats[$uid]['is_locked']   = true;
                    $stats[$uid]['is_unlocked'] = false;
                    $stats[$uid]['total_violations'] = max($stats[$uid]['total_violations'], 3);
                } elseif ($log['action'] === 'unlocked' || $log['action'] === 'remote_unlocked') {
                    $stats[$uid]['is_locked']   = false;
                    $stats[$uid]['is_unlocked'] = true;
                    // Reset to 2 after unlock (giving student 1 final chance)
                    $stats[$uid]['total_violations'] = 2;
                } elseif (strpos($log['action'], 'warning') !== false) {
                    $stats[$uid]['total_violations'] = (int)$log['violation_count'];
                }
            }
            return $stats;
        } catch (Throwable $e) {
            log_message('error', 'MoodleViolationModel::getStudentViolationStats failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Unlock a student remotely from the exam-admin dashboard
     */
    public function remoteUnlock(int $quizId, int $userId, string $adminName = 'Admin'): bool
    {
        try {
            $data = [
                'userid'          => $userId,
                'quizid'          => $quizId,
                'attemptid'       => 0,
                'action'          => 'remote_unlocked',
                'violation_count' => 2,
                'reason'          => 'Dibuka jarak jauh oleh Pengawas (' . $adminName . ') dari Dashboard',
                'device_type'     => 'Server/Dashboard',
                'ip_address'      => service('request')->getIPAddress() ?: '127.0.0.1',
                'timecreated'     => time(),
            ];
            return (bool)$this->insert($data);
        } catch (Throwable $e) {
            log_message('error', 'MoodleViolationModel::remoteUnlock failed: ' . $e->getMessage());
            return false;
        }
    }
}

