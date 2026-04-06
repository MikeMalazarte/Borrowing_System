<?php 

namespace App\Controllers;

class BorrowSys_Ctrl extends BaseController {

    protected $mybrmod;

    public function __construct() {
        $this->mybrmod = model('App\Models\MyBrwngModel');
    }

    public function index() {
        $meaction = $this->request->getPostGet('meaction');
        $session  = session();




        switch($meaction){
            
        case 'DO-LOGIN':
            $result = $this->mybrmod->doLogin();
            echo json_encode($result);
            break;

        case 'REGISTER':
            echo view('brwng_auth/register');
            break;

        case 'DO-REGISTER':
            echo json_encode($this->mybrmod->doRegister());
            break;

        case 'STUDENT-DASHBOARD':
            $user_role = $session->get('user_role');
            if ($user_role != 2) {
                echo view('brwng_auth/login');
                return;
            }
            $data['active_page'] = 'dashboard';
            echo view('template/header01', $data);
            echo view('brwng_stud/dashboard', $data);
            echo view('template/footer01');
            break;

        case 'GET-DASHBOARD-STATS':
            $user_role = $session->get('user_role');
            if ($user_role != 2) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
                return;
            }
            $data = $this->mybrmod->getDashboardStats();
            echo json_encode([
                'active'    => $data['active'],
                'total'     => $data['total'],
                'available' => $data['available'],
                'recent'    => $data['recent']  // ← still returned for stat cards
            ]);
            break;

        // ← ADD this new case for the HTML rows
        case 'GET-DASHBOARD-RECS':
            $user_role = $session->get('user_role');
            if ($user_role != 2) {
                echo 'Unauthorized.';
                return;
            }
            $data = $this->mybrmod->getDashboardStats();
            echo view('brwng_stud/dashboard_recs', ['recent' => $data['recent']]);
            break;

        case 'BROWSE-TOOLS':
            $data['active_page'] = 'browse';
            echo view('template/header01', $data);
            echo view('brwng_stud/browse_tools', $data);
            echo view('template/footer01');
            break;

        case 'MY-BORROWINGS':
            $data['active_page'] = 'borrowings';
            echo view('template/header01', $data);
            echo view('brwng/student/my_borrowings', $data);
            echo view('template/footer01');
            break;

        case 'GET-TOOLS':
            echo json_encode($this->mybrmod->getTools());
            break;

        case 'DO-BORROW':
            echo json_encode($this->mybrmod->borrowTool());
            break;
        
        case 'DO-RETURN':
            echo json_encode($this->mybrmod->returnTool());
            break;

        case 'DO-LOGOUT':
            session()->destroy();
            echo json_encode(['status' => 'ok']);
            break;

        






        default:
            if ($session->get('is_logged_in')) {
                $user_role = $session->get('user_role');
                if ($user_role == 1) {
                    return redirect()->to(base_url('Borrowing-System?meaction=ADMIN-DASHBOARD'));
                } else {
                    return redirect()->to(base_url('Borrowing-System?meaction=STUDENT-DASHBOARD'));
                }
            } else {
                echo view('brwng_auth/login');
            }
            break;
        }
    }

}