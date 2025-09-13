<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorageDestination;
use App\Services\StorageRouter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class StorageDestinationController extends Controller
{
    public function index()
    {
        $destinations = StorageDestination::all();
        return view('admin.storage.index', compact('destinations'));
    }

    public function create()
    {
        return view('admin.storage.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'type' => 'required|in:local,ftp,s3,spaces',
            'credentials' => 'required',
            'is_default' => 'boolean',
        ]);
        // Accept JSON in textarea or array payload
        if (is_string($data['credentials'])) {
            $decoded = json_decode($data['credentials'], true);
            if (!is_array($decoded)) {
                return back()->withErrors(['credentials' => 'Credentials must be valid JSON.'])->withInput();
            }
            $data['credentials'] = $decoded;
        }
        if ($data['is_default'] ?? false) {
            StorageDestination::query()->update(['is_default' => false]);
        }
        $data['credentials'] = Crypt::encryptString(json_encode($data['credentials']));
        StorageDestination::create($data);
        return Redirect::route('admin.storage.index')->with('status', 'Destination created.');
    }

    public function edit($id)
    {
        $destination = StorageDestination::findOrFail($id);
        $destination->credentials = json_decode(Crypt::decryptString($destination->credentials), true);
        return view('admin.storage.edit', compact('destination'));
    }

    public function update(Request $request, $id)
    {
        $destination = StorageDestination::findOrFail($id);
        $data = $request->validate([
            'name' => 'required',
            'type' => 'required|in:local,ftp,s3,spaces',
            'credentials' => 'required',
            'is_default' => 'boolean',
        ]);
        if (is_string($data['credentials'])) {
            $decoded = json_decode($data['credentials'], true);
            if (!is_array($decoded)) {
                return back()->withErrors(['credentials' => 'Credentials must be valid JSON.'])->withInput();
            }
            $data['credentials'] = $decoded;
        }
        if ($data['is_default'] ?? false) {
            StorageDestination::query()->update(['is_default' => false]);
        }
        $data['credentials'] = Crypt::encryptString(json_encode($data['credentials']));
        $destination->update($data);
        return Redirect::route('admin.storage.index')->with('status', 'Destination updated.');
    }

    public function destroy($id)
    {
        StorageDestination::findOrFail($id)->delete();
        return Redirect::route('admin.storage.index')->with('status', 'Destination deleted.');
    }

    public function validateConnection($id)
    {
        $destination = StorageDestination::findOrFail($id);
        try {
            $result = \App\Services\StorageRouter::validateDestination($destination);
            if ($result['ok']) {
                Session::flash('status', 'Connection successful: '.implode(' | ', $result['messages']));
            } else {
                Session::flash('error', 'Connection failed: '.implode(' | ', $result['messages']));
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Connection failed: '.$e->getMessage());
        }
        return Redirect::route('admin.storage.index');
    }
}
