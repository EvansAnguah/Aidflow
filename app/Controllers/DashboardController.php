<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Member;
use App\Models\Contribution;
use App\Models\WelfareRequest;
use App\Models\Disbursement;
use App\Models\AuditLog;
use App\Models\Setting;

class DashboardController extends Controller {
    private $memberModel;
    private $contributionModel;
    private $requestModel;
    private $disbursementModel;
    private $auditModel;
    private $settingModel;

    public function __construct() {
        $this->memberModel = new Member();
        $this->contributionModel = new Contribution();
        $this->requestModel = new WelfareRequest();
        $this->disbursementModel = new Disbursement();
        $this->auditModel = new AuditLog();
        $this->settingModel = new Setting();
    }

    /**
     * Dashboard Router Action
     */
    public function index() {
        Session::requireLogin();
        
        $role = Session::get('role');
        $userId = Session::get('user_id');

        // Fetch shared details
        $financialSummary = $this->settingModel->getSystemFinancialSummary();
        $memberCounts = $this->memberModel->getCounts();
        $requestCounts = $this->requestModel->getStatusCounts();
        $recentLogs = $this->auditModel->getAll(8);

        switch ($role) {
            case 'Admin':
                $data = [
                    'title' => 'Admin Dashboard',
                    'member_counts' => $memberCounts,
                    'financial' => $financialSummary,
                    'requests' => $requestCounts,
                    'recent_activities' => $recentLogs
                ];
                $this->view('dashboards/admin', $data);
                break;

            case 'Treasurer':
                $recentContributions = $this->contributionModel->getAll('', '');
                $recentContributions = array_slice($recentContributions, 0, 5);
                
                $data = [
                    'title' => 'Treasurer Dashboard',
                    'financial' => $financialSummary,
                    'member_counts' => $memberCounts,
                    'recent_contributions' => $recentContributions,
                    'requests' => $requestCounts
                ];
                $this->view('dashboards/treasurer', $data);
                break;

            case 'Welfare Officer':
                $pendingRequests = $this->requestModel->getAll('Pending', '', '');
                $reviewRequests = $this->requestModel->getAll('Under Review', '', '');

                $data = [
                    'title' => 'Welfare Officer Dashboard',
                    'member_counts' => $memberCounts,
                    'requests' => $requestCounts,
                    'pending_requests' => array_slice($pendingRequests, 0, 5),
                    'review_requests' => array_slice($reviewRequests, 0, 5)
                ];
                $this->view('dashboards/officer', $data);
                break;

            case 'Member':
                $member = $this->memberModel->findByUserId($userId);
                if (!$member) {
                    Session::logout();
                    Session::setFlash('error', 'Member profile not found. Please contact support.');
                    $this->redirect('auth/login');
                }

                $myContributions = $this->contributionModel->getByMemberId($member['id']);
                $outstanding = $this->contributionModel->getOutstandingContributions($member);
                $myRequests = $this->requestModel->getByMemberId($member['id']);

                $data = [
                    'title' => 'Member Portal',
                    'member' => $member,
                    'contributions' => array_slice($myContributions, 0, 5),
                    'total_paid' => array_sum(array_column($myContributions, 'amount')),
                    'outstanding' => $outstanding,
                    'requests' => array_slice($myRequests, 0, 5)
                ];
                $this->view('dashboards/member', $data);
                break;

            default:
                Session::logout();
                $this->redirect('auth/login');
        }
    }

    /**
     * AJAX Endpoint for Chart.js dashboard statistics
     */
    public function getAnalyticsData() {
        Session::requireLogin();
        
        $role = Session::get('role');
        if (!in_array($role, ['Admin', 'Treasurer', 'Welfare Officer'])) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Get monthly contribution stats
        $contributions = $this->contributionModel->getMonthlyStatistics();
        
        // Get category request distribution
        $categoriesStats = $this->requestModel->db->fetchAll(
            "SELECT wc.name as label, COUNT(wr.id) as value 
             FROM welfare_categories wc 
             LEFT JOIN welfare_requests wr ON wc.id = wr.category_id 
             GROUP BY wc.id"
        );

        $this->json([
            'contributions' => $contributions,
            'categories' => $categoriesStats
        ]);
    }
}
