@extends('indigo-layout::installation')

@section('meta_title', _p('packaginator::pages.installation.meta_title', 'Installation packaginator') . ' - ' . config('app.name'))
@section('meta_description', _p('packaginator::pages.installation.meta_description', 'Installation packaginator in application'))

@push('head')

@endpush

@section('title')
    <h2>{{ _p('packaginator::pages.installation.headline', 'Installation packaginator') }}</h2>
@endsection

@section('content')
    <form-builder disabled-dialog="" url="{{ route('packaginator.installation.index') }}" send-text="{{ _p('packaginator::pages.installation.send_text', 'Install') }}"
    edited>
        <div class="section">
            <ul>
                <li>
                    {{ _p('packaginator::pages.installation.will_be_execute_migrations', 'Will be execute package migrations') }}
                </li>
            </ul>
        </div>
    </form-builder>
@endsection
