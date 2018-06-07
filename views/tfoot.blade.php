<tfoot {{ classTag(config('tablelist.template.global.tfoot.class')) }}>
    <tr {{ classTag(config('tablelist.template.table.tr.class'), config('tablelist.template.tfoot.tr.class')) }}>
        <td {{ classTag(config('tablelist.template.table.td.class'), config('tablelist.template.tfoot.td.class')) }}
            colspan="{{ $table->getColumnsCount() + ($table->isRouteDefined('edit') || $table->isRouteDefined('destroy') ? 1 : 0) }}">
            <div class="row">
                {{-- create button --}}
                @if($table->isRouteDefined('create'))
                    <div {{ classTag(config('tablelist.template.tfoot.options-bar.create.container.class')) }}>
                        @include('tablelist::components.buttons.create', ['route' => $table->getRoute('create')])
                    </div>
                @endif
                {{-- navigation status --}}
                <div {{ classTag(
                $table->isRouteDefined('create') 
                ? config('tablelist.template.tfoot.options-bar.navigation-status.with-create-route.container.class') 
                : config('tablelist.template.tfoot.options-bar.navigation-status.without-create-route.container.class')) }}>
                    {!! $table->navigationStatus() !!}
                </div>
                {{-- pagination container --}}
                <div {{ classTag($table->isRouteDefined('create')
                ? config('tablelist.template.tfoot.options-bar.pagination-container.with-create-route.container.class')
                :config('tablelist.template.tfoot.options-bar.pagination-container.without-create-route.container.class')) }}>
                    {!! $table->list->render() !!}
                </div>
            </div>
        </td>
    </tr>
</tfoot>
