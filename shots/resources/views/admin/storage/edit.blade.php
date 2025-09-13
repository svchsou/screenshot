@extends('layouts.app')

@section('content')
  <div class="mui-panel" style="max-width:820px; margin:0 auto;">
    <h1 class="mui--text-headline" style="margin-top:0">Edit Storage Destination</h1>
    <form id="destForm" method="POST" action="{{ route('admin.storage.update', $destination->id) }}">
      @csrf @method('PUT')
      <div class="mui-textfield"><label>Name</label>
        <input type="text" name="name" value="{{ $destination->name }}" required>
      </div>
      <div class="mui-textfield"><label>Type</label>
        <select id="type" name="type" class="mui-select" required>
          <option value="local" @if($destination->type=='local') selected @endif>Local</option>
          <option value="ftp" @if($destination->type=='ftp') selected @endif>FTP</option>
          <option value="s3" @if($destination->type=='s3') selected @endif>S3</option>
          <option value="spaces" @if($destination->type=='spaces') selected @endif>Spaces</option>
        </select>
      </div>

      @php($c = $destination->credentials)
      <div id="section-local" class="type-section @if($destination->type!=='local') mui--hide @endif">
        <div class="mui-textfield"><label>Root Path</label>
          <input type="text" id="local_root" value="{{ $c['root'] ?? '' }}" placeholder="C:/wamp64/www/uploads">
        </div>
        <div class="mui--text-caption">Ensure the path exists and PHP has write permission.</div>
      </div>

      <div id="section-ftp" class="type-section @if($destination->type!=='ftp') mui--hide @endif">
        <div class="mui-textfield"><label>Host</label><input type="text" id="ftp_host" value="{{ $c['host'] ?? '' }}"></div>
        <div class="mui-textfield"><label>Username</label><input type="text" id="ftp_username" value="{{ $c['username'] ?? '' }}"></div>
        <div class="mui-textfield"><label>Password</label><input type="password" id="ftp_password" value="{{ $c['password'] ?? '' }}"></div>
        <div class="mui-textfield"><label>Root Path</label><input type="text" id="ftp_root" value="{{ $c['root'] ?? '' }}" placeholder="/public_html/uploads"></div>
        <div class="mui-row">
          <div class="mui-col-sm-4 mui-textfield"><label>Port</label><input type="number" id="ftp_port" value="{{ $c['port'] ?? 21 }}"></div>
          <div class="mui-col-sm-4 mui-checkbox" style="margin-top:28px"><label><input type="checkbox" id="ftp_ssl" @if(!empty($c['ssl'])) checked @endif> SSL</label></div>
          <div class="mui-col-sm-4 mui-checkbox" style="margin-top:28px"><label><input type="checkbox" id="ftp_passive" @if(($c['passive'] ?? true)) checked @endif> Passive</label></div>
        </div>
        <div class="mui-textfield"><label>Timeout (s)</label><input type="number" id="ftp_timeout" value="{{ $c['timeout'] ?? 30 }}"></div>
        <div class="mui--text-caption">
          @if(!extension_loaded('ftp'))
            <span class="mui--text-danger">PHP ftp extension is not enabled. Enable extension=ftp in php.ini.</span>
          @endif
          @if(!class_exists(\League\Flysystem\Ftp\FtpAdapter::class))
            <span class="mui--text-danger">Composer package league/flysystem-ftp:^3.0 is not installed.</span>
          @endif
        </div>
      </div>

      <div id="section-s3" class="type-section @if(!in_array($destination->type,['s3','spaces'])) mui--hide @endif">
        <div class="mui-textfield"><label>Access Key</label><input type="text" id="s3_key" value="{{ $c['key'] ?? '' }}"></div>
        <div class="mui-textfield"><label>Secret</label><input type="password" id="s3_secret" value="{{ $c['secret'] ?? '' }}"></div>
        <div class="mui-textfield"><label>Region</label><input type="text" id="s3_region" value="{{ $c['region'] ?? '' }}" placeholder="ap-southeast-1"></div>
        <div class="mui-textfield"><label>Bucket</label><input type="text" id="s3_bucket" value="{{ $c['bucket'] ?? '' }}"></div>
        <div class="mui-textfield"><label>Endpoint</label><input type="text" id="s3_endpoint" value="{{ $c['endpoint'] ?? '' }}" placeholder="https://sgp1.digitaloceanspaces.com"></div>
        <div class="mui-textfield"><label>Public URL (optional)</label><input type="text" id="s3_url" value="{{ $c['url'] ?? '' }}" placeholder="https://cdn.example.com"></div>
        <div class="mui-textfield"><label>Prefix/Root (optional)</label><input type="text" id="s3_root" value="{{ $c['root'] ?? '' }}" placeholder="uploads"></div>
        <div class="mui-checkbox"><label><input type="checkbox" id="s3_use_path_style" @if(!empty($c['use_path_style'])) checked @endif> Use Path Style</label></div>
        <div class="mui--text-caption">
          @if(!class_exists(\Aws\S3\S3Client::class))
            <span class="mui--text-danger">Composer package aws/aws-sdk-php:^3.0 is not installed.</span>
          @endif
          @if(!class_exists(\League\Flysystem\AwsS3V3\AwsS3V3Adapter::class))
            <span class="mui--text-danger">Composer package league/flysystem-aws-s3-v3:^3.0 is not installed.</span>
          @endif
        </div>
      </div>

      <textarea id="credentials" name="credentials" class="mui--hide">{{ json_encode($destination->credentials) }}</textarea>
      <div class="mui-checkbox"><label>
        <input type="checkbox" name="is_default" value="1" @if($destination->is_default) checked @endif> Default
      </label></div>
      <div style="display:flex; gap:8px;">
        <button type="button" id="btnTest" class="mui-btn">Test Connection</button>
        <button type="submit" class="mui-btn mui-btn--raised mui-btn--primary">Update</button>
      </div>
      <div id="testResult" class="mui--text-body2" style="margin-top:12px;"></div>
    </form>
  </div>

  <script>
  (function(){
    const typeEl = document.getElementById('type');
    const sections = ['local','ftp','s3'];
    function currentSectionId(){ return (typeEl.value === 'spaces') ? 's3' : typeEl.value; }
    function updateSection(){
      const active = currentSectionId();
      sections.forEach(s => {
        const el = document.getElementById('section-'+s);
        if (!el) return;
        el.classList.toggle('mui--hide', s !== active);
      });
    }
    typeEl.addEventListener('change', updateSection); updateSection();

    function buildCreds(){
      const t = typeEl.value;
      let c = {};
      if (t==='local') {
        c.root = document.getElementById('local_root').value || '';
      } else if (t==='ftp') {
        c.host = document.getElementById('ftp_host').value || '';
        c.username = document.getElementById('ftp_username').value || '';
        c.password = document.getElementById('ftp_password').value || '';
        c.root = document.getElementById('ftp_root').value || '';
        c.port = parseInt(document.getElementById('ftp_port').value||'21',10);
        c.ssl = document.getElementById('ftp_ssl').checked;
        c.passive = document.getElementById('ftp_passive').checked;
        c.timeout = parseInt(document.getElementById('ftp_timeout').value||'30',10);
      } else if (t==='s3' || t==='spaces') {
        c.key = document.getElementById('s3_key').value || '';
        c.secret = document.getElementById('s3_secret').value || '';
        c.region = document.getElementById('s3_region').value || '';
        c.bucket = document.getElementById('s3_bucket').value || '';
        c.endpoint = document.getElementById('s3_endpoint').value || '';
        c.url = document.getElementById('s3_url').value || '';
        c.root = document.getElementById('s3_root').value || '';
        c.use_path_style = document.getElementById('s3_use_path_style').checked;
      }
      document.getElementById('credentials').value = JSON.stringify(c);
      return {type: t, credentials: c};
    }

    document.getElementById('destForm').addEventListener('submit', function(){ buildCreds(); });
    document.getElementById('btnTest').addEventListener('click', async function(){
      const payload = buildCreds();
      const resEl = document.getElementById('testResult');
      resEl.textContent = 'Testing...';
      try {
        const r = await fetch('{{ route('admin.storage.test') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: JSON.stringify(payload)
        });
        const data = await r.json();
        resEl.textContent = (data.ok ? 'OK: ' : 'ERROR: ') + (data.messages||[]).join(' | ');
        resEl.style.color = data.ok ? '#2e7d32' : '#c62828';
      } catch (e) {
        resEl.textContent = 'ERROR: '+ e.message;
        resEl.style.color = '#c62828';
      }
    });
  })();
  </script>
@endsection
