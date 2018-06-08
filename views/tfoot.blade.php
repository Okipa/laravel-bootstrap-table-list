<tfoot {{ classTag(config('tablelist.template.table.tfoot.item.class')) }}>
    <tr {{ classTag(config('tablelist.template.table.tr.class'), config('tablelist.template.table.tfoot.tr.class')) }}>
        <td {{ classTag(config('tablelist.template.table.td.class'), config('tablelist.template.table.tfoot.td.class')) }}
            colspan="{{ $table->getColumnsCount() + ($table->isRouteDefined('edit') || $table->isRouteDefined('destroy') ? 1 : 0) }}">
            <div {{ classTag(config('tablelist.template.table.tfoot.options-bar.item.class')) }}>
                {{-- create button --}}
                @if($table->isRouteDefined('create'))
                    <div {{ classTag('create-container', config('tablelist.template.table.tfoot.options-bar.create.container.class')) }}>
                        <a href="{{ $table->getRoute('create') }}"
                           {{ classTag(config('tablelist.template.table.tfoot.options-bar.create.item.class')) }}
                           title="{{ trans('tablelist::tablelist.tfoot.action.create') }}">
                            {!! config('tablelist.template.table.tfoot.options-bar.create.item.icon') !!}
                            {{ trans('tablelist::tablelist.tfoot.action.create') }}
                        </a>
                    </div>
                @endif
                {{-- navigation --}}
                <div {{ classTag('navigation', $table->isRouteDefined('create') 
                ? config('tablelist.template.table.tfoot.options-bar.navigation.with-create-route.container.class') 
                : config('tablelist.template.table.tfoot.options-bar.navigation.without-create-route.container.class')) }}>
                    {!! $table->navigationStatus() !!}
                </div>
                {{-- pagination container --}}
                <div {{ classTag('pagination-container', $table->isRouteDefined('create')
                ? config('tablelist.template.table.tfoot.options-bar.pagination.with-create-route.container.class')
                : config('tablelist.template.table.tfoot.options-bar.pagination.without-create-route.container.class')) }}>
                    {!! $table->list->render() !!}
                </div>
            </div>
        </td>
    </tr>
</tfoot>
