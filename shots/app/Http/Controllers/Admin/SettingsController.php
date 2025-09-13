<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;

class SettingsController extends Controller
{
    public function adsForm()
    {
        $data = [
            'ADS_ENABLED' => env('ADS_ENABLED', false),
            'ADSENSE_CLIENT_ID' => env('ADSENSE_CLIENT_ID'),
            'ADSENSE_SLOT_LEFT' => env('ADSENSE_SLOT_LEFT'),
            'ADSENSE_SLOT_RIGHT' => env('ADSENSE_SLOT_RIGHT'),
            'ADSENSE_SLOT_BOTTOM' => env('ADSENSE_SLOT_BOTTOM'),
            'ADSENSE_TEST_MODE' => env('ADSENSE_TEST_MODE', false),
        ];
        return view('admin.settings.ads', compact('data'));
    }

    public function saveAds(Request $request)
    {
        $payload = $request->validate([
            'ADS_ENABLED' => 'nullable|boolean',
            'ADSENSE_CLIENT_ID' => 'nullable|string',
            'ADSENSE_SLOT_LEFT' => 'nullable|string',
            'ADSENSE_SLOT_RIGHT' => 'nullable|string',
            'ADSENSE_SLOT_BOTTOM' => 'nullable|string',
            'ADSENSE_TEST_MODE' => 'nullable|boolean',
        ]);
        $payload['ADS_ENABLED'] = $request->boolean('ADS_ENABLED') ? 'true' : 'false';
        $payload['ADSENSE_TEST_MODE'] = $request->boolean('ADSENSE_TEST_MODE') ? 'true' : 'false';
        $this->writeEnv($payload);
        Artisan::call('config:clear');
        return Redirect::route('admin.settings.ads')->with('status', 'Ad settings saved.');
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
