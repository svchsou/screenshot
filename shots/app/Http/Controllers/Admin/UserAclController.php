<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;

class UserAclController extends Controller
{
    public function index()
    {
        $data = [
            'ADMIN_USERNAME' => env('ADMIN_USERNAME', 'admin'),
            'HAS_PASSWORD_HASH' => (bool) env('ADMIN_PASSWORD_HASH'),
        ];
        return view('admin.users.index', compact('data'));
    }

    public function save(Request $request)
    {
        $payload = $request->validate([
            'ADMIN_USERNAME' => 'required|string',
            'ADMIN_PASSWORD' => 'nullable|string|min:6',
        ]);
        $pairs = [ 'ADMIN_USERNAME' => $payload['ADMIN_USERNAME'] ];
        if (!empty($payload['ADMIN_PASSWORD'])) {
            $pairs['ADMIN_PASSWORD_HASH'] = password_hash($payload['ADMIN_PASSWORD'], PASSWORD_BCRYPT);
            $pairs['ADMIN_PASSWORD'] = '';
        }
        $this->writeEnv($pairs);
        Artisan::call('config:clear');
        return Redirect::route('admin.users.index')->with('status', 'Admin credentials updated.');
    }

    protected function writeEnv(array $pairs): void
    {
        $envPath = base_path('.env');
        $contents = file_exists($envPath) ? file_get_contents($envPath) : '';
        foreach ($pairs as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $line = $key.'='.$value;
            if (preg_match($pattern, $contents)) {
                $contents = preg_replace($pattern, $line, $contents);
            } else {
                $contents .= PHP_EOL.$line;
            }
        }
        file_put_contents($envPath, $contents);
    }
}

