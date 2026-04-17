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


   //////////////////////////////////////////////////////////
    public function getAdminDashboardStats() {
        // Total tools
        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `tools` WHERE `status` = 1");
        $total_tools = $q->getRowArray()['total'];

        // Available tools
        $q = $this->mybsdbmod->exec("SELECT SUM(`available`) AS total FROM `tools` WHERE `status` = 1");
        $available_tools = $q->getRowArray()['total'] ?? 0;

        // Active borrowings
        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings` WHERE `status` = 1");
        $active_borrowings = $q->getRowArray()['total'];

        // Total students
        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `users` WHERE `user_role` = 2 AND `status` = 1");
        $total_students = $q->getRowArray()['total'];

        // Overdue
        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings` WHERE `status` = 3");
        $overdue = $q->getRowArray()['total'];

        // Returned today
        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings` WHERE `status` = 2 AND `returned_at` = CURDATE()");
        $returned_today = $q->getRowArray()['total'];

        // Borrowed today
        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings` WHERE `borrowed_at` = CURDATE()");
        $borrowed_today = $q->getRowArray()['total'];

        // Total borrowings
        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings`");
        $total_borrowings = $q->getRowArray()['total'];

        // Recent borrowings (last 10)
        $q = $this->mybsdbmod->exec("SELECT 
                                        b.`brw_code`,
                                        b.`tool_name`,
                                        u.`full_name`,
                                        u.`user_code`,
                                        DATE_FORMAT(b.`borrowed_at`, '%b %d, %Y') AS borrowed_at,
                                        DATE_FORMAT(b.`due_date`,    '%b %d, %Y') AS due_date,
                                        CASE b.`status`
                                            WHEN 1 THEN 'Active'
                                            WHEN 2 THEN 'Returned'
                                            WHEN 3 THEN 'Overdue'
                                        END AS status
                                    FROM `borrowings` b
                                    LEFT JOIN `users` u ON b.`user_code` = u.`user_code`
                                    ORDER BY b.`created_at` DESC
                                    LIMIT 10");
        $recent = $q->getResultArray();

        // Inventory
        $q = $this->mybsdbmod->exec("SELECT `tool_name`, `available`, `quantity` FROM `tools` WHERE `status` = 1 ORDER BY `tool_name` ASC");
        $inventory = $q->getResultArray();

        $page     = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $limit    = 8;
        $offset   = ($page - 1) * $limit;
        $term     = isset($_POST['term']) ? trim($_POST['term']) : '';
        $strcon   = '';

        if (!empty($term)) {
            $strcon = "AND (b.`tool_name` LIKE '%{$term}%' 
                        OR u.`full_name`  LIKE '%{$term}%' 
                        OR u.`user_code`  LIKE '%{$term}%')";
        }

        // Total count for pagination
        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total
                                    FROM `borrowings` b
                                    LEFT JOIN `users` u ON b.`user_code` = u.`user_code`
                                    WHERE 1=1 {$strcon}");
        $total_records = $q->getRowArray()['total'];
        $total_pages   = ceil($total_records / $limit);

        // Paginated results
        $q = $this->mybsdbmod->exec("SELECT 
                                        b.`brw_code`,
                                        b.`tool_name`,
                                        u.`full_name`,
                                        u.`user_code`,
                                        DATE_FORMAT(b.`borrowed_at`, '%b %d, %Y') AS borrowed_at,
                                        DATE_FORMAT(b.`due_date`,    '%b %d, %Y') AS due_date,
                                        CASE b.`status`
                                            WHEN 1 THEN 'Active'
                                            WHEN 2 THEN 'Returned'
                                            WHEN 3 THEN 'Overdue'
                                        END AS status
                                    FROM `borrowings` b
                                    LEFT JOIN `users` u ON b.`user_code` = u.`user_code`
                                    WHERE 1=1 {$strcon}
                                    ORDER BY b.`created_at` DESC
                                    LIMIT {$limit} OFFSET {$offset}");
        $recent = $q->getResultArray();

        return [
            'total_tools'       => $total_tools,
            'available_tools'   => $available_tools,
            'active_borrowings' => $active_borrowings,
            'total_students'    => $total_students,
            'overdue'           => $overdue,
            'returned_today'    => $returned_today,
            'borrowed_today'    => $borrowed_today,
            'total_borrowings'  => $total_borrowings,
            'recent'            => $recent,
            'inventory'         => $inventory,
            'recent'        => $recent,
            'total_pages'   => (int)$total_pages,
            'current_page'  => $page,
            'total_records' => (int)$total_records,
        ];
    } 

    public function getAdminTools() {
    $page   = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit  = 10;
    $offset = ($page - 1) * $limit;
    $term   = isset($_POST['term']) ? trim($_POST['term']) : '';
    $strcon = '';

    if (!empty($term)) {
        $strcon = "AND (`tool_name` LIKE '%{$term}%' OR `description` LIKE '%{$term}%')";
    }

    $q             = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `tools` WHERE `status` = 1 {$strcon}");
    $total_records = $q->getRowArray()['total'];
    $total_pages   = ceil($total_records / $limit);

    $q     = $this->mybsdbmod->exec("SELECT * FROM `tools` 
                                    WHERE `status` = 1 {$strcon}
                                    ORDER BY `tool_name` ASC
                                    LIMIT {$limit} OFFSET {$offset}");
    $tools = $q->getResultArray();

    return [
        'tools'         => $tools,
        'total_pages'   => (int)$total_pages,
        'current_page'  => $page,
        'total_records' => (int)$total_records
    ];
    }

    public function addTool() {
        $tool_name   = trim($this->mybsdbmod->request->getPost('tool_name'));
        $description = trim($this->mybsdbmod->request->getPost('tool_description'));
        $quantity    = (int)$this->mybsdbmod->request->getPost('tool_quantity');

        if (empty($tool_name) || empty($quantity)) {
            return ['status' => 'error', 'message' => 'Please fill in all fields.'];
        }

        if ($quantity < 1) {
            return ['status' => 'error', 'message' => 'Quantity must be at least 1.'];
        }

        // Check duplicate
        $q = $this->mybsdbmod->exec("SELECT `recid` FROM `tools` 
                                    WHERE `tool_name` = '{$tool_name}' 
                                    AND   `status`    = 1 
                                    LIMIT 1");
        if ($q->getNumRows() > 0) {
            return ['status' => 'error', 'message' => 'Tool already exists.'];
        }

        // Generate tool code
        $q         = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `tools`");
        $row       = $q->getRowArray();
        $tool_code = 'TOOL-' . str_pad($row['total'] + 1, 5, '0', STR_PAD_LEFT);

        $this->mybsdbmod->exec("INSERT INTO `tools` 
                                (`tool_code`, `tool_name`, `description`, `quantity`, `available`, `status`) 
                                VALUES 
                                ('{$tool_code}', '{$tool_name}', '{$description}', {$quantity}, {$quantity}, 1)");

        return ['status' => 'ok'];
    }

    public function editTool() {
        $tool_code   = trim($this->mybsdbmod->request->getPost('tool_code'));
        $tool_name   = trim($this->mybsdbmod->request->getPost('tool_name'));
        $description = trim($this->mybsdbmod->request->getPost('tool_description'));
        $quantity    = (int)$this->mybsdbmod->request->getPost('tool_quantity');

        if (empty($tool_code) || empty($tool_name) || empty($quantity)) {
            return ['status' => 'error', 'message' => 'Please fill in all fields.'];
        }

        // Get current tool to compute new available
        $q   = $this->mybsdbmod->exec("SELECT * FROM `tools` WHERE `tool_code` = '{$tool_code}' LIMIT 1");
        $old = $q->getRowArray();

        // borrowed = quantity - available
        $borrowed      = $old['quantity'] - $old['available'];
        $new_available = max(0, $quantity - $borrowed);

        $this->mybsdbmod->exec("UPDATE `tools` 
                                SET `tool_name`   = '{$tool_name}',
                                    `description` = '{$description}',
                                    `quantity`    = {$quantity},
                                    `available`   = {$new_available}
                                WHERE `tool_code` = '{$tool_code}'");

        return ['status' => 'ok'];
    }

    public function archiveTool() {
        $tool_code = trim($this->mybsdbmod->request->getPost('tool_code'));

        if (empty($tool_code)) {
            return ['status' => 'error', 'message' => 'Invalid request.'];
        }

        $this->mybsdbmod->exec("UPDATE `tools` SET `status` = 0 WHERE `tool_code` = '{$tool_code}'");

        return ['status' => 'ok'];
    }

    public function getAdminBorrowings() {
    $page      = isset($_POST['page'])   ? (int)$_POST['page']        : 1;
    $limit     = 10;
    $offset    = ($page - 1) * $limit;
    $term      = isset($_POST['term'])   ? trim($_POST['term'])        : '';
    $status    = isset($_POST['status']) ? trim($_POST['status'])      : 'all';
    $strcon    = '';

    if (!empty($term)) {
        $strcon .= " AND (b.`tool_name`  LIKE '%{$term}%' 
                      OR  u.`full_name`  LIKE '%{$term}%' 
                      OR  u.`user_code`  LIKE '%{$term}%'
                      OR  b.`brw_code`   LIKE '%{$term}%')";
    }

    if ($status !== 'all') {
        $strcon .= " AND b.`status` = {$status}";
    }

    // Counts for chips
    $q        = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings`");
    $cnt_all  = $q->getRowArray()['total'];

    $q        = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings` WHERE `status` = 1");
    $cnt_active = $q->getRowArray()['total'];

    $q        = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings` WHERE `status` = 2");
    $cnt_returned = $q->getRowArray()['total'];

    $q        = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings` WHERE `status` = 3");
    $cnt_overdue = $q->getRowArray()['total'];

    // Total for pagination
    $q             = $this->mybsdbmod->exec("SELECT COUNT(*) AS total 
                                            FROM `borrowings` b
                                            LEFT JOIN `users` u ON b.`user_code` = u.`user_code`
                                            WHERE 1=1 {$strcon}");
    $total_records = $q->getRowArray()['total'];
    $total_pages   = ceil($total_records / $limit);

    // Results
    $q = $this->mybsdbmod->exec("SELECT 
                                    b.`brw_code`,
                                    b.`tool_name`,
                                    b.`tool_code`,
                                    u.`full_name`,
                                    u.`user_code`,
                                    DATE_FORMAT(b.`borrowed_at`,  '%b %d, %Y') AS borrowed_at,
                                    DATE_FORMAT(b.`due_date`,     '%b %d, %Y') AS due_date,
                                    DATE_FORMAT(b.`returned_at`,  '%b %d, %Y') AS returned_at,
                                    DATE_FORMAT(b.`time_from`,    '%h:%i %p')  AS time_from,
                                    DATE_FORMAT(b.`time_to`,      '%h:%i %p')  AS time_to,
                                    b.`status`,
                                    CASE b.`status`
                                        WHEN 1 THEN 'Active'
                                        WHEN 2 THEN 'Returned'
                                        WHEN 3 THEN 'Overdue'
                                    END AS status_label
                                FROM `borrowings` b
                                LEFT JOIN `users` u ON b.`user_code` = u.`user_code`
                                WHERE 1=1 {$strcon}
                                ORDER BY b.`created_at` DESC
                                LIMIT {$limit} OFFSET {$offset}");
        $borrowings = $q->getResultArray();

        return [
            'borrowings'    => $borrowings,
            'total_pages'   => (int)$total_pages,
            'current_page'  => $page,
            'total_records' => (int)$total_records,
            'cnt_all'       => (int)$cnt_all,
            'cnt_active'    => (int)$cnt_active,
            'cnt_returned'  => (int)$cnt_returned,
            'cnt_overdue'   => (int)$cnt_overdue
        ];
    }

    public function adminReturnTool() {
        $brw_code = trim($this->mybsdbmod->request->getPost('brw_code'));

        if (empty($brw_code)) {
            return ['status' => 'error', 'message' => 'Invalid request.'];
        }

        $q = $this->mybsdbmod->exec("SELECT * FROM `borrowings` 
                                    WHERE `brw_code` = '{$brw_code}' 
                                    AND   `status`   IN (1, 3)
                                    LIMIT 1");
        if ($q->getNumRows() == 0) {
            return ['status' => 'error', 'message' => 'Borrowing record not found.'];
        }

        $borrowing = $q->getRowArray();

        $this->mybsdbmod->exec("UPDATE `borrowings` 
                                SET    `status`      = 2,
                                    `returned_at` = CURDATE()
                                WHERE  `brw_code`    = '{$brw_code}'");

        $this->mybsdbmod->exec("UPDATE `tools` 
                                SET    `available` = `available` + 1 
                                WHERE  `tool_code` = '{$borrowing['tool_code']}'");

        return ['status' => 'ok'];
    }

    public function exportBorrowingsCSV() {
        $q    = $this->mybsdbmod->exec("SELECT 
                                            b.`brw_code`,
                                            u.`full_name`,
                                            u.`user_code`,
                                            b.`tool_name`,
                                            DATE_FORMAT(b.`borrowed_at`, '%b %d, %Y') AS borrowed_at,
                                            DATE_FORMAT(b.`time_from`,   '%h:%i %p')  AS time_from,
                                            DATE_FORMAT(b.`time_to`,     '%h:%i %p')  AS time_to,
                                            DATE_FORMAT(b.`due_date`,    '%b %d, %Y') AS due_date,
                                            DATE_FORMAT(b.`returned_at`, '%b %d, %Y') AS returned_at,
                                            CASE b.`status`
                                                WHEN 1 THEN 'Active'
                                                WHEN 2 THEN 'Returned'
                                                WHEN 3 THEN 'Overdue'
                                            END AS status
                                        FROM `borrowings` b
                                        LEFT JOIN `users` u ON b.`user_code` = u.`user_code`
                                        ORDER BY b.`created_at` DESC");
        $rows = $q->getResultArray();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="borrowings_' . date('Ymd') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Brw Code', 'Student', 'Student ID', 'Tool', 'Borrowed', 'Time From', 'Time To', 'Due Date', 'Returned', 'Status']);
        foreach ($rows as $row) {
            fputcsv($out, [
                $row['brw_code'],
                $row['full_name'],
                $row['user_code'],
                $row['tool_name'],
                $row['borrowed_at'],
                $row['time_from'],
                $row['time_to'],
                $row['due_date'],
                $row['returned_at'] ?? '—',
                $row['status']
            ]);
        }
        fclose($out);
        exit;
    }

    public function getAdminStudents() {
        $page    = isset($_POST['page'])   ? (int)$_POST['page']   : 1;
        $limit   = 10;
        $offset  = ($page - 1) * $limit;
        $term    = isset($_POST['term'])   ? trim($_POST['term'])   : '';
        $status  = isset($_POST['status']) ? trim($_POST['status']) : 'all';
        $strcon  = "AND u.`user_role` = 2";

        if (!empty($term)) {
            $strcon .= " AND (u.`full_name` LIKE '%{$term}%' 
                        OR  u.`user_code` LIKE '%{$term}%' 
                        OR  u.`email`     LIKE '%{$term}%')";
        }

        if ($status !== 'all') {
            $strcon .= " AND u.`status` = {$status}";
        }

        // Chip counts
        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `users` WHERE `user_role` = 2");
        $cnt_all = $q->getRowArray()['total'];

        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `users` WHERE `user_role` = 2 AND `status` = 1");
        $cnt_active = $q->getRowArray()['total'];

        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `users` WHERE `user_role` = 2 AND `status` = 0");
        $cnt_suspended = $q->getRowArray()['total'];

        // Total for pagination
        $q             = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `users` u WHERE 1=1 {$strcon}");
        $total_records = $q->getRowArray()['total'];
        $total_pages   = ceil($total_records / $limit);

        // Students with borrowing stats
        $q = $this->mybsdbmod->exec("SELECT 
                                        u.`user_code`,
                                        u.`full_name`,
                                        u.`email`,
                                        u.`course`,
                                        u.`year_level`,
                                        u.`status`,
                                        COUNT(b.`brw_code`)                          AS total_borrowed,
                                        SUM(CASE WHEN b.`status` IN (2,3) 
                                                AND b.`returned_at` > b.`due_date` 
                                                THEN 1 ELSE 0 END)                  AS total_overdue,
                                        SUM(CASE WHEN b.`status` = 1 THEN 1 ELSE 0 END) AS active_now
                                    FROM `users` u
                                    LEFT JOIN `borrowings` b ON u.`user_code` = b.`user_code`
                                    WHERE 1=1 {$strcon}
                                    GROUP BY u.`user_code`
                                    ORDER BY u.`full_name` ASC
                                    LIMIT {$limit} OFFSET {$offset}");
        $students = $q->getResultArray();

        return [
            'students'      => $students,
            'total_pages'   => (int)$total_pages,
            'current_page'  => $page,
            'total_records' => (int)$total_records,
            'cnt_all'       => (int)$cnt_all,
            'cnt_active'    => (int)$cnt_active,
            'cnt_suspended' => (int)$cnt_suspended
        ];
    }

    public function getStudentDetail() {
        $user_code = trim($this->mybsdbmod->request->getPost('user_code'));

        // Borrowing stats
        $q = $this->mybsdbmod->exec("SELECT 
                                        COUNT(*)                                         AS total_borrowed,
                                        SUM(CASE WHEN `status` IN (2,3) 
                                                AND `returned_at` > `due_date` 
                                                THEN 1 ELSE 0 END)                      AS total_overdue,
                                        SUM(CASE WHEN `status` = 1 THEN 1 ELSE 0 END)   AS active_now,
                                        SUM(CASE WHEN `status` = 2 
                                                AND `returned_at` <= `due_date` 
                                                THEN 1 ELSE 0 END)                      AS on_time
                                    FROM `borrowings`
                                    WHERE `user_code` = '{$user_code}'");
        $stats = $q->getRowArray();

        // Borrowing history (last 10)
        $q = $this->mybsdbmod->exec("SELECT 
                                        `tool_name`,
                                        DATE_FORMAT(`borrowed_at`,  '%b %d, %Y') AS borrowed_at,
                                        DATE_FORMAT(`due_date`,     '%b %d, %Y') AS due_date,
                                        DATE_FORMAT(`returned_at`,  '%b %d, %Y') AS returned_at,
                                        CASE `status`
                                            WHEN 1 THEN 'Active'
                                            WHEN 2 THEN 'Returned'
                                            WHEN 3 THEN 'Overdue'
                                        END AS status_label,
                                        CASE 
                                            WHEN `status` = 2 AND `returned_at` <= `due_date` THEN 1
                                            ELSE 0
                                        END AS on_time
                                    FROM `borrowings`
                                    WHERE `user_code` = '{$user_code}'
                                    ORDER BY `created_at` DESC
                                    LIMIT 10");
        $history = $q->getResultArray();

        return [
            'status'   => 'ok',
            'stats'    => $stats,
            'history'  => $history
        ];
    }

    public function toggleStudentStatus() {
        $user_code  = trim($this->mybsdbmod->request->getPost('user_code'));
        $new_status = (int)$this->mybsdbmod->request->getPost('new_status');

        if (empty($user_code)) {
            return ['status' => 'error', 'message' => 'Invalid request.'];
        }

        $this->mybsdbmod->exec("UPDATE `users` 
                                SET    `status` = {$new_status} 
                                WHERE  `user_code` = '{$user_code}' 
                                AND    `user_role` = 2");

        return ['status' => 'ok'];
    }

    public function getAdminProfileStats() {
        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `tools` WHERE `status` = 1");
        $total_tools = $q->getRowArray()['total'];

        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `users` WHERE `user_role` = 2 AND `status` = 1");
        $total_students = $q->getRowArray()['total'];

        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings` WHERE `status` = 1");
        $active_borrowings = $q->getRowArray()['total'];

        $q = $this->mybsdbmod->exec("SELECT COUNT(*) AS total FROM `borrowings` WHERE `status` = 3");
        $overdue = $q->getRowArray()['total'];

        return [
            'status'            => 'ok',
            'total_tools'       => (int)$total_tools,
            'total_students'    => (int)$total_students,
            'active_borrowings' => (int)$active_borrowings,
            'overdue'           => (int)$overdue
        ];
    }

} // end MyBrwngModel