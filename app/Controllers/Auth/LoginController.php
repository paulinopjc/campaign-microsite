<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class LoginController extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('auth/login');
    }

    public function google()
    {
        $credential = $this->request->getJSON()->credential ?? '';

        $tokenInfo = json_decode(file_get_contents(
            'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential)
        ), true);

        if (!$tokenInfo || ($tokenInfo['aud'] ?? '') !== getenv('GOOGLE_CLIENT_ID')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid token']);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $tokenInfo['email'])
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$user['google_sub']) {
            $userModel->update($user['id'], ['google_sub' => $tokenInfo['sub']]);
        }

        session()->set([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_role' => $user['role'],
            'logged_in' => true,
        ]);

        return $this->response->setJSON(['success' => true, 'redirect' => '/admin/dashboard']);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}