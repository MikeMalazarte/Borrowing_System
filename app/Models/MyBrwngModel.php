<?php namespace App\Models;

use CodeIgniter\Model;

class MyBrwngModel extends Model {

    protected $mybsdbmod;

    public function __construct() {
        parent::__construct();
        $this->mybsdbmod = new BaseDBModel(); // ← your preferred way
    }

    public function doLogin() {
        $email    = $this->mybsdbmod->request->getPost('login_email');    // ← fix
        $password = $this->mybsdbmod->request->getPost('login_password'); // ← fix

        if (empty($email) || empty($password)) {
            return ['status' => 'error', 'message' => 'Please fill in all fields.'];
        }

        $q = $this->mybsdbmod->exec("SELECT * FROM `users` 
                                    WHERE `email`  = '{$email}' 
                                    AND   `status` = 1 
                                    LIMIT 1");

        if ($q->getNumRows() == 0) {
            return ['status' => 'error', 'message' => 'Email not found.'];
        }

        $user = $q->getRowArray();

        if (!password_verify($password, $user['password'])) {
            return ['status' => 'error', 'message' => 'Incorrect password.'];
        }

        $this->mybsdbmod->session->set([
            'is_logged_in' => true,
            'user_code'    => $user['user_code'],
            'full_name'    => $user['full_name'],
            'user_role'    => $user['user_role'],
            'course'       => $user['course'],
            'year_level'   => $user['year_level'],
            'email'        => $user['email']
        ]);

        return ['status' => 'ok', 'user_role' => $user['user_role']];
    }


    public function doRegister() {
        $firstname        = $this->mybsdbmod->request->getPost('reg_firstname');
        $lastname         = $this->mybsdbmod->request->getPost('reg_lastname');
        $email            = $this->mybsdbmod->request->getPost('reg_email');
        $course           = $this->mybsdbmod->request->getPost('reg_course');
        $yearlevel        = $this->mybsdbmod->request->getPost('reg_yearlevel');
        $password         = $this->mybsdbmod->request->getPost('reg_password');
        $password_confirm = $this->mybsdbmod->request->getPost('reg_password_confirm');

        // validation
        if (empty($firstname) || empty($lastname) || empty($email) ||
            empty($course)    || empty($yearlevel) || empty($password) || empty($password_confirm)) {
            return ['status' => 'error', 'message' => 'Please fill in all fields.'];
        }

        if ($password !== $password_confirm) {
            return ['status' => 'error', 'message' => 'Passwords do not match.'];
        }

        // check if email already exists
        $q = $this->mybsdbmod->exec("SELECT `recid` FROM `users` 
                                    WHERE `email` = '{$email}' 
                                    LIMIT 1");
        if ($q->getNumRows() > 0) {
            return ['status' => 'error', 'message' => 'Email already exists.'];
        }

        // generate user code e.g. STU-00001
        $q         = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `users`");
        $row       = $q->getRowArray();
        $user_code = 'STU-' . str_pad($row['total'] + 1, 5, '0', STR_PAD_LEFT);

        // hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // insert to DB
        $full_name = $firstname . ' ' . $lastname;
        $this->mybsdbmod->exec("INSERT INTO `users` 
                                (`user_code`, `full_name`, `email`, `password`, `course`, `year_level`, `user_role`, `status`) 
                                VALUES 
                                ('{$user_code}', '{$full_name}', '{$email}', '{$hashed_password}', '{$course}', '{$yearlevel}', 2, 1)");

        return ['status' => 'ok'];
    }

    public function getDashboardStats() {
        $user_code = $this->mybsdbmod->session->get('user_code'); // ← fix

        // Active borrowings count
        $q      = $this->mybsdbmod->exec("SELECT COUNT(*) AS total 
                                        FROM `borrowings` 
                                        WHERE `user_code` = '{$user_code}' 
                                        AND   `status`    = 1");
        $active = $q->getRowArray()['total'];

        // Total borrowed count
        $q     = $this->mybsdbmod->exec("SELECT COUNT(*) AS total 
                                        FROM `borrowings` 
                                        WHERE `user_code` = '{$user_code}'");
        $total = $q->getRowArray()['total'];

        // Available tools count
        $q         = $this->mybsdbmod->exec("SELECT SUM(`available`) AS total 
                                            FROM `tools` 
                                            WHERE `status` = 1");
        $available = $q->getRowArray()['total'] ?? 0;

        // Recent borrowings (last 5)
        $q = $this->mybsdbmod->exec("SELECT 
                                        `brw_code`,
                                        `tool_code`,
                                        `tool_name`,
                                        DATE_FORMAT(`time_from`, '%h:%i %p') AS time_from,
                                        DATE_FORMAT(`time_to`,   '%h:%i %p') AS time_to,
                                        DATE_FORMAT(`borrowed_at`, '%b %d, %Y') AS borrowed_at,
                                        DATE_FORMAT(
                                            CONCAT(`due_date`, ' ', COALESCE(`time_to`, '23:59:59')),
                                            '%b %d, %Y %h:%i %p'
                                        ) AS due_date,
                                        CASE `status`
                                            WHEN 1 THEN 'Active'
                                            WHEN 2 THEN 'Returned'
                                            WHEN 3 THEN 'Overdue'
                                        END AS status
                                    FROM `borrowings` 
                                    WHERE `user_code` = '{$user_code}'
                                    ORDER BY `created_at` DESC 
                                    LIMIT 5");
        $recent = $q->getResultArray();

        $q        = $this->mybsdbmod->exec("SELECT COUNT(*) AS total 
                                    FROM `borrowings` 
                                    WHERE `user_code` = '{$user_code}' 
                                    AND   `status`    = 2");
        $returned = $q->getRowArray()['total'];

        return [
            'active'    => $active,
            'total'     => $total,
            'available' => $available,
            'recent'    => $recent,
            'returned'  => $returned
        ];
    } // end getDashboardStats

    public function updateOverdue() {
        $this->mybsdbmod->exec("UPDATE `borrowings` 
                                SET    `status` = 3 
                                WHERE  `status` = 1 
                                AND    CONCAT(`due_date`, ' ', COALESCE(`time_to`, '23:59:59')) < NOW()");
    }

    public function getTools() {
        $term   = $this->mybsdbmod->request->getPost('term');
        $strcon = '';

        if (!empty($term)) {
            $strcon = "AND (`tool_name` LIKE '%{$term}%' OR `description` LIKE '%{$term}%')";
        }

        $q = $this->mybsdbmod->exec("SELECT * FROM `tools` 
                                    WHERE `status` = 1 
                                    {$strcon}
                                    ORDER BY `tool_name` ASC");
        return $q->getResultArray();
    }

    public function borrowTool() {
        $user_code   = $this->mybsdbmod->session->get('user_code');
        $tool_code   = $this->mybsdbmod->request->getPost('tool_code');
        $borrow_date = $this->mybsdbmod->request->getPost('borrow_date');
        $time_from   = $this->mybsdbmod->request->getPost('time_from');
        $time_to     = $this->mybsdbmod->request->getPost('time_to');
        $due_date    = $this->mybsdbmod->request->getPost('due_date');

        if (empty($tool_code) || empty($borrow_date) || empty($time_from) || 
            empty($time_to)   || empty($due_date)) {
            return ['status' => 'error', 'message' => 'Please fill in all fields.'];
        }

        // check tool available
        $q = $this->mybsdbmod->exec("SELECT * FROM `tools` 
                                    WHERE `tool_code` = '{$tool_code}' 
                                    AND   `available` > 0 
                                    AND   `status`    = 1 
                                    LIMIT 1");
        if ($q->getNumRows() == 0) {
            return ['status' => 'error', 'message' => 'Tool is not available.'];
        }

        $tool = $q->getRowArray();

        // generate borrow code
        $brw_code = 'BRW-' . date('YmdHis') . rand(100, 999);

        // insert with time fields
        $this->mybsdbmod->exec("INSERT INTO `borrowings` 
                                (`brw_code`, `user_code`, `tool_code`, `tool_name`,
                                `borrow_date`, `time_from`, `time_to`,
                                `borrowed_at`, `due_date`, `status`) 
                                VALUES 
                                ('{$brw_code}', '{$user_code}', '{$tool_code}', '{$tool['tool_name']}',
                                '{$borrow_date}', '{$time_from}', '{$time_to}',
                                CURDATE(), '{$due_date}', 1)");

        // decrease available
        $this->mybsdbmod->exec("UPDATE `tools` 
                                SET `available` = `available` - 1 
                                WHERE `tool_code` = '{$tool_code}'");

        return ['status' => 'ok'];
    }

    public function returnTool() {
        $user_code = $this->mybsdbmod->session->get('user_code');
        $brw_code  = $this->mybsdbmod->request->getPost('brw_code');

        if (empty($brw_code)) {
            return ['status' => 'error', 'message' => 'Invalid request.'];
        }

        // check borrowing exists and belongs to this user
        $q = $this->mybsdbmod->exec("SELECT * FROM `borrowings` 
                                    WHERE `brw_code`  = '{$brw_code}' 
                                    AND   `user_code` = '{$user_code}' 
                                    AND   `status`    IN (1, 3)
                                    LIMIT 1");
        if ($q->getNumRows() == 0) {
            return ['status' => 'error', 'message' => 'Borrowing record not found.'];
        }

        $borrowing = $q->getRowArray();

        // update borrowing status to returned
        $this->mybsdbmod->exec("UPDATE `borrowings` 
                                SET    `status`      = 2, 
                                    `returned_at` = CURDATE() 
                                WHERE  `brw_code`    = '{$brw_code}'");

        // increase tool available count back
        $this->mybsdbmod->exec("UPDATE `tools` 
                                SET    `available` = `available` + 1 
                                WHERE  `tool_code` = '{$borrowing['tool_code']}'");

        return ['status' => 'ok'];
    } // end returnTool

    public function changePassword() {
        $user_code        = $this->mybsdbmod->session->get('user_code');
        $current_password = $this->mybsdbmod->request->getPost('current_password');
        $new_password     = $this->mybsdbmod->request->getPost('new_password');

        if (empty($current_password) || empty($new_password)) {
            return ['status' => 'error', 'message' => 'Please fill in all fields.'];
        }

        // 1. Fetch current hashed password from DB
        $q = $this->mybsdbmod->exec("SELECT `password` FROM `users` 
                                    WHERE `user_code` = '{$user_code}' 
                                    LIMIT 1");

        if ($q->getNumRows() == 0) {
            return ['status' => 'error', 'message' => 'User not found.'];
        }

        $user = $q->getRowArray();

        // 2. Verify current password
        if (!password_verify($current_password, $user['password'])) {
            return ['status' => 'error', 'message' => 'Current password is incorrect.'];
        }

        // 3. Hash and update new password
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $this->mybsdbmod->exec("UPDATE `users` 
                                SET    `password` = '{$hashed}' 
                                WHERE  `user_code` = '{$user_code}'");

        return ['status' => 'ok'];
    }

} // end MyBrwngModel