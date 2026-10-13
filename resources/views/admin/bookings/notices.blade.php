@if(session('status'))<div class="editor-notice success" role="status">{{ session('status') }}</div>@endif
@if($errors->any())<div class="editor-notice error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
