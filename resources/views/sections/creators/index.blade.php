@extends('indigo-layout::main')

@section('meta_title', _p('packaginator::pages.creator.meta_title', 'Creator') . ' - ' . config('app.name'))
@section('meta_description', _p('packaginator::pages.creator.meta_description', 'Creator in application'))

@push('head')

@endpush

@section('title')
    {{ _p('packaginator::pages.creator.headline', 'Creator') }}
@endsection

@section('content')
    <div class="grid">
        <div class="cell-1-3 cell--dsm">
            <h4>{{ _p('packaginator::pages.creator.create_package', 'Create package') }}</h4>
            <div class="card">
                <div class="card-body">
                    <p>{{ _p('packaginator::pages.creator.description_create_your_package', 'Create your package of Awema') }}</p>
                   <div class="section">
                       <form-builder url="/" send-text="{{ _p('packaginator::pages.creator.send_text', 'Create') }}"
                                     @send="(data) => {AWEMA._store.commit('setData', {param: 'createPackage', data: data}); AWEMA.emit('modal::create_package_confirm:open')}"
                                     disabled-dialog>
                           <fb-input name="name_package" label="{{ _p('packaginator::pages.creator.name_package', 'Name package') }}"
                           hint=""></fb-input>
                           <small class="text-caption">{{ _p('packaginator::pages.creator.hint_only_letters', 'You can only use letters') }}</small>
                       </form-builder>
                   </div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid section">
        <div class="cell-1-1 cell--dsm">
            <h4>{{ _p('packaginator::pages.example.history_packages', 'History packages') }}</h4>
            <div class="card">
                <div class="card-body">
                    <content-wrapper :url="$url.urlFromOnlyQuery('{{ route('packaginator.creator.scope')}}', ['page', 'limit'], $route.query)"
                        :check-empty="function(test) { return !(test && (test.data && test.data.length || test.length)) }"
                        name="histories_table">
                        <template slot-scope="table">
                            <table-builder :default="table.data">
                                <tb-column name="created_at" label="{{ _p('packaginator::pages.creator.created_at', 'Created at') }}">
                                    <template slot-scope="col">
                                        @{{ col.data.created_at }}
                                    </template>
                                </tb-column>
                                <tb-column name="name" label="{{ _p('packaginator::pages.creator.name', 'Name') }}"></tb-column>
                            </table-builder>
                            <paginate-builder
                                :meta="table.meta"
                            ></paginate-builder>
                        </template>
                        @include('indigo-layout::components.base.loading')
                        @include('indigo-layout::components.base.empty')
                        @include('indigo-layout::components.base.error')
                    </content-wrapper>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')

    <content-window name="create_package_confirm" class="modal_formbuilder"
                  title="{{ _p('packaginator::pages.creator.confirm_create', 'Confirm create') }}">
        <form-builder :edited="true" url="{{ route('packaginator.creator.store') }}"
                      @sended="AWEMA.emit('content::histories_table:update')"
                      send-text="{{ _p('packaginator::pages.creator.confirm', 'Confirm') }}" store-data="createPackage"
                      disabled-dialog>
            <fb-input name="name_package" label="{{ _p('packaginator::pages.creator.name_package', 'Name package') }}"></fb-input>
        </form-builder>
    </content-window>
@endsection
