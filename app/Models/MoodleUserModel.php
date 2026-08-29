<?php

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

class MoodleUserModel extends Model
{
    protected $table      = 'user';
    protected $primaryKey = 'id';
    protected $DBGroup    = 'moodle';
    protected $allowedFields = [
        'auth', 'confirmed', 'mnethostid', 'username', 'password',
        'idnumber', 'firstname', 'lastname', 'email', 'lang',
        'calendartype', 'timecreated', 'timemodified', 'deleted', 'suspended'
    ];

    public function getEnrolledStudents(int $courseId): array
    {
        try {
            return $this->db->table('user u')
                ->select('DISTINCT u.id, u.firstname, u.lastname, u.username, u.email, u.idnumber, u.department, u.institution', false)
                ->join('user_enrolments ue', 'ue.userid = u.id')
                ->join('enrol e', 'e.id = ue.enrolid')
                ->where('e.courseid', $courseId)
                ->where('ue.status', 0)
                ->where('e.status', 0)
                ->where('u.deleted', 0)
                ->where('u.suspended', 0)
                ->orderBy('u.lastname', 'ASC')
                ->orderBy('u.firstname', 'ASC')
                ->get()->getResultArray();
        } catch (Throwable $e) {
            log_message('error', 'MoodleUserModel::getEnrolledStudents failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllStudents(): array
    {
        try {
            return $this->db->table('user')
                ->select('id, firstname, lastname, username, email, idnumber, department, institution')
                ->where('deleted', 0)
                ->where('suspended', 0)
                ->where('id >', 1)
                ->orderBy('lastname', 'ASC')
                ->orderBy('firstname', 'ASC')
                ->get()->getResultArray();
        } catch (Throwable $e) {
            log_message('error', 'MoodleUserModel::getAllStudents failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getCourses(): array
    {
        try {
            return $this->db->table('course')
                ->select('id, fullname, shortname, idnumber, summary')
                ->where('id >', 1)
                ->orderBy('fullname', 'ASC')
                ->get()->getResultArray();
        } catch (Throwable $e) {
            log_message('error', 'MoodleUserModel::getCourses failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getCohorts(): array
    {
        try {
            return $this->db->table('cohort')
                ->select('id, name, idnumber, description, visible')
                ->orderBy('name', 'ASC')
                ->get()->getResultArray();
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getStudentsByCohort(int $cohortId): array
    {
        try {
            return $this->db->table('user u')
                ->select('DISTINCT u.id, u.firstname, u.lastname, u.username, u.email, u.idnumber, u.department, u.institution', false)
                ->join('cohort_members cm', 'cm.userid = u.id')
                ->where('cm.cohortid', $cohortId)
                ->where('u.deleted', 0)
                ->where('u.suspended', 0)
                ->orderBy('u.lastname', 'ASC')
                ->orderBy('u.firstname', 'ASC')
                ->get()->getResultArray();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Create or update student account in Moodle
     */
    public function createOrUpdateStudent(array $userData): ?int
    {
        try {
            $username = strtolower(trim($userData['username']));
            if (empty($username)) return null;

            $existing = $this->db->table('user')
                ->where('username', $username)
                ->where('mnethostid', 1)
                ->get()->getRowArray();

            $now = time();
            $plainPassword = $userData['password'] ?? '123456';
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

            // Split full name into firstname and lastname
            $fullName = trim($userData['nama'] ?? $username);
            $nameParts = preg_split('/\s+/', $fullName, 2);
            $firstName = $nameParts[0] ?? $username;
            $lastName  = $nameParts[1] ?? ($nameParts[0] ?? $username);

            $email = !empty($userData['email']) ? trim($userData['email']) : ($username . '@school.local');
            $idNumber = !empty($userData['nis']) ? trim($userData['nis']) : ($userData['idnumber'] ?? $username);

            if ($existing) {
                // Update password and ensure account is active & confirmed
                $this->db->table('user')->where('id', $existing['id'])->update([
                    'password'     => $hashedPassword,
                    'firstname'    => $firstName,
                    'lastname'     => $lastName,
                    'idnumber'     => $idNumber,
                    'confirmed'    => 1,
                    'deleted'      => 0,
                    'suspended'    => 0,
                    'timemodified' => $now,
                ]);
                return (int)$existing['id'];
            } else {
                // Insert new Moodle user
                $this->db->table('user')->insert([
                    'auth'         => 'manual',
                    'confirmed'    => 1,
                    'mnethostid'   => 1,
                    'username'     => $username,
                    'password'     => $hashedPassword,
                    'idnumber'     => $idNumber,
                    'firstname'    => $firstName,
                    'lastname'     => $lastName,
                    'email'        => $email,
                    'lang'         => 'en',
                    'calendartype' => 'gregorian',
                    'timecreated'  => $now,
                    'timemodified' => $now,
                ]);
                return (int)$this->db->insertID();
            }
        } catch (Throwable $e) {
            log_message('error', 'MoodleUserModel::createOrUpdateStudent error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Enrol a Moodle user into a specific Moodle course
     */
    public function enrolStudentToCourse(int $userId, int $courseId): bool
    {
        try {
            if (!$userId || !$courseId) return false;

            // 1. Find manual enrol instance for this course
            $enrol = $this->db->table('enrol')
                ->where('courseid', $courseId)
                ->where('enrol', 'manual')
                ->get()->getRowArray();

            if (!$enrol) {
                // Fallback to any active enrol instance
                $enrol = $this->db->table('enrol')
                    ->where('courseid', $courseId)
                    ->where('status', 0)
                    ->get()->getRowArray();
            }

            if (!$enrol) return false;

            $now = time();

            // 2. Check if user is already enrolled
            $existingEnrol = $this->db->table('user_enrolments')
                ->where('enrolid', $enrol['id'])
                ->where('userid', $userId)
                ->get()->getRowArray();

            if (!$existingEnrol) {
                $this->db->table('user_enrolments')->insert([
                    'status'       => 0,
                    'enrolid'      => $enrol['id'],
                    'userid'       => $userId,
                    'timestart'    => $now,
                    'timeend'      => 0,
                    'modifierid'   => 2,
                    'timecreated'  => $now,
                    'timemodified' => $now,
                ]);
            }

            // 3. Assign Student role (roleid = 5) in Course Context (contextlevel = 50)
            $context = $this->db->table('context')
                ->where('contextlevel', 50) // 50 = CONTEXT_COURSE
                ->where('instanceid', $courseId)
                ->get()->getRowArray();

            if ($context) {
                $existingRole = $this->db->table('role_assignments')
                    ->where('roleid', 5) // 5 = Student
                    ->where('contextid', $context['id'])
                    ->where('userid', $userId)
                    ->get()->getRowArray();

                if (!$existingRole) {
                    $this->db->table('role_assignments')->insert([
                        'roleid'       => 5,
                        'contextid'    => $context['id'],
                        'userid'       => $userId,
                        'timemodified' => $now,
                        'modifierid'   => 2,
                        'component'    => '',
                        'itemid'       => 0,
                        'sortorder'    => 0,
                    ]);
                }
            }

            return true;
        } catch (Throwable $e) {
            log_message('error', 'MoodleUserModel::enrolStudentToCourse error: ' . $e->getMessage());
            return false;
        }
    }
}