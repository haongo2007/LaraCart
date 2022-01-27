@extends($templatePath.'.Layout.main')

@section('content')
    {{-- Content --}}
@endsection

@section('breadcrumb')
    <div class="breadcrumbs">
        <ol class="breadcrumb">
          <li><a href="{{ bc_route('home') }}">{{ bc_language_render('front.home') }}</a></li>
          <li class="active">{{ $title ?? '' }}</li>
        </ol>
      </div>
@endsection

@push('styles')
      {{-- style css --}}
@endpush

@push('scripts')
      {{-- script --}}
@endpush