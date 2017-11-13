<thead>
    {{-- commands --}}
    @if(count($table->sortableColumns) || count($table->searchableColumns))
        <tr>
            <td colspan="{{ $table->getColumnsCount() + ($table->isRouteDefined('edit') || $table->isRouteDefined('destroy') ? 1 : 0) }}">
                <div class="row">
                    {{-- row number selector --}}
                    <div class="col-sm-4 col-xs-12 rows-number-selector">
                        @if($table->rowsNumberSelectorEnabled)
                            <form role="form" method="GET" action="{{ $table->getRoute('index') }}">
                                <input type="hidden" name="search" value="{{ $table->request->search }}">
                                <input type="hidden" name="sortBy" value="{{ $table->request->sortBy }}">
                                <input type="hidden" name="sortDir" value="{{ $table->request->sortDir }}">
                                <span class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-list-ol"></i>
                                    </span>
                                    <input class="form-control"
                                           type="number"
                                           name="rowsNumber"
                                           value="{{ $table->request->rowsNumber }}"
                                           placeholder="{{ trans('tablelist::tablelist.thead.rowsNumber') }}">
                                    <span class="input-group-addon submit">
                                        <a href=""
                                           title="{{ trans('tablelist::tablelist.thead.rowsNumber') }}"
                                           onclick="$(this).closest('form').submit();">
                                            <i class="fa fa-check"></i>
                                        </a>
                                    </span>
                                </span>
                            </form>
                        @endif
                    </div>
                    {{-- empty column --}}
                    <div class="col-sm-2 hidden-xs">
                    </div>
                    {{-- search input --}}
                    <div class="col-sm-6 col-xs-12 search-bar">
                        @if(count($table->searchableColumns))
                            <form role="form" method="GET" action="{{ $table->getRoute('index') }}">
                                <input type="hidden" name="rowsNumber" value="{{ $table->request->rowsNumber }}">
                                <input type="hidden" name="sortBy" value="{{ $table->request->sortBy }}">
                                <input type="hidden" name="sortDir" value="{{ $table->request->sortDir }}">
                                <span class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </span>
                                    <input class="form-control"
                                           type="text"
                                           name="search"
                                           value="{{ $table->request->search }}"
                                           placeholder="{{ trans('tablelist::tablelist.thead.search') }} {{ $table->getSearchableTitles() }}">
                                        @if($table->request->search)
                                            <span class="input-group-addon">
                                                <a href="{{ $table->getRoute('index', [
                                                    'search' => null,
                                                    'rowsNumber' => $table->request->rowsNumber,
                                                    'sortBy' => $table->request->sortBy,
                                                    'sortDir' => $table->request->sortDir,
                                                    ]) }}">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </span>
                                        @else
                                        <span class="input-group-addon submit">
                                            <a href=""
                                               title="{{ trans('tablelist::tablelist.thead.search') }} {{ $table->getSearchableTitles() }}"
                                               onclick="$(this).closest('form').submit();">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        </span>
                                    @endif
                                </span>
                            </form>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    @endif
    {{-- titles --}}
    <tr>
        @foreach($table->columns as $column)
            <th>
                @if($column->isSortableColumn)
                    <a href="{{ $table->getRoute('index', [
                        'sortBy' => $column->attribute,
                        'sortDir' => $table->request->sortDir === 'asc' ? 'desc' : 'asc',
                        'search'   => $table->request->search,
                        'rowsNumber'    => $table->request->rowsNumber,
                    ]) }}"
                       title="{{ $column->title }}"
                       class="sort {{ config('tablelist.template.indicator.sort.class') }}">
                        @if($table->request->sortBy === $column->attribute && $table->request->sortDir === 'asc')
                            {!! config('tablelist.template.indicator.sort.icon.asc') !!}
                        @elseif($table->request->sortBy === $column->attribute && $table->request->sortDir === 'desc')
                            {!! config('tablelist.template.indicator.sort.icon.desc') !!}
                        @else
                            {!! config('tablelist.template.indicator.sort.icon.unsorted') !!}
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
            <th class="actions">
                {{ trans('tablelist::tablelist.thead.actions') }}
            </th>
        @endif
    </tr>
</thead>
