<tr {{ classTag(config('tablelist.template.table.tr.class'), config('tablelist.template.table.thead.titles-bar.tr.class')) }}>
    @foreach($table->columns as $column)
        <th {{ classTag(config('tablelist.template.table.th.class'), config('tablelist.template.table.thead.titles-bar.th.class')) }}
            scope="col">
            @if($column->isSortableColumn)
                <a href="{{ $table->getRoute('index', [
                        'sortBy' => $column->attribute,
                        'sortDir' => $table->request->sortDir === 'asc' ? 'desc' : 'asc',
                        'search'   => $table->request->search,
                        'rowsNumber'    => $table->request->rowsNumber,
                    ]) }}"
                   title="{{ $column->title }}"
                   {{ classTag(config('tablelist.template.table.thead.titles-bar.sort.item.class')) }}>
                    @if($table->request->sortBy === $column->attribute && $table->request->sortDir === 'asc')
                        <span {{ classTag(config('tablelist.template.table.thead.titles-bar.sort.asc.item.class')) }}>
                            {!! config('tablelist.template.table.thead.titles-bar.sort.asc.item.icon') !!}
                        </span>
                    @elseif($table->request->sortBy === $column->attribute && $table->request->sortDir === 'desc')
                        <span {{ classTag(config('tablelist.template.table.thead.titles-bar.sort.desc.item.class')) }}>
                            {!! config('tablelist.template.table.thead.titles-bar.sort.desc.item.icon') !!}
                        </span>
                    @else
                        <span {{ classTag(config('tablelist.template.table.thead.titles-bar.sort.unsorted.item.class')) }}>
                            {!! config('tablelist.template.table.thead.titles-bar.sort.unsorted.item.icon') !!}
                        </span>
                    @endif
                    <span>
                        &nbsp{!! str_replace(' ', '&nbsp;', $column->title) !!}
                    </span>
                </a>
            @else
                {!! str_replace(' ', '&nbsp;', $column->title) !!}
            @endif
        </th>
    @endforeach
    @if($table->isRouteDefined('edit') || $table->isRouteDefined('destroy'))
        <th {{ classTag('text-right', config('tablelist.template.table.th.class'), config('tablelist.template.table.thead.titles-bar.th.class')) }}
            scope="col">
            {{ trans('tablelist::tablelist.thead.actions') }}
        </th>
    @endif
</tr>
