<thead>

    {{-- commands --}}
    @if(count($table->sortable_columns) || count($table->searchable_columns))

        <tr>

            <td colspan="{{ $table->getColumnsCount() + ($table->isRouteDefined('edit') || $table->isRouteDefined('destroy') ? 1 : 0) }}">

                <div class="row">

                    {{-- row number selector --}}
                    <div class="col-sm-4 col-xs-12 rows-number-selector">
                        @if($table->rows_number_selector_enabled)
                            <form role="form" method="GET" action="{{ $table->getRoute('index') }}">
                                <input type="hidden" name="search" value="{{ $table->request->search }}">
                                <input type="hidden" name="sortBy" value="{{ $table->request->sortBy }}">
                                <input type="hidden" name="sortDir" value="{{ $table->request->sortDir }}">
                                <span class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-list-ol"></i></span>
                                    <input class="form-control"
                                           type="number"
                                           name="rows_number"
                                           value="{{ $table->request->rows_number }}"
                                           placeholder="{{ trans('tablelist::tablelist.thead.rows_number') }}">
                                    <span class="input-group-addon submit">
                                        <a href="" title="{{ trans('tablelist::tablelist.thead.rows_number') }}">
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
                        @if(count($table->searchable_columns))
                            <form role="form" method="GET" action="{{ $table->getRoute('index') }}">
                                <input type="hidden" name="rows_number" value="{{ $table->request->rows_number }}">
                                <input type="hidden" name="sortBy" value="{{ $table->request->sortBy }}">
                                <input type="hidden" name="sortDir" value="{{ $table->request->sortDir }}">
                                <span class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                    <input class="form-control"
                                           type="text"
                                           name="search"
                                           value="{{ $table->request->search }}"
                                           placeholder="{{ trans('tablelist::tablelist.thead.search') }} {{ $table->getSearchableTitles() }}">
                                        @if($table->request->search)
                                            <span class="input-group-addon">
                                                <a href="{{ $table->getRoute('index', [
                                                'search' => null,
                                                'rows_number' => $table->request->rows_number,
                                                'sortBy' => $table->request->sortBy,
                                                'sortDir' => $table->request->sortDir,
                                                ]) }}">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </span>
                                        @else
                                            <span class="input-group-addon submit">
                                                <a href="" title="{{ trans('tablelist::tablelist.thead.search') }} {{ $table->getSearchableTitles() }}">
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
                @if($column->is_sortable)
                    <a href="{{ $table->getRoute('index', [
                        'sortBy' => $column->attribute,
                        'sortDir' => $table->request->sortDir === 'asc' ? 'desc' : 'asc',
                        'search'   => $table->request->search,
                        'rows_number'    => $table->request->rows_number,
                    ]) }}" title="{{ $column->title }}" class="sort">
                        @if($table->request->sortBy === $column->attribute && $table->request->sortDir === 'asc')
                            <i class="fa fa-sort-asc"></i>
                        @elseif($table->request->sortBy === $column->attribute && $table->request->sortDir === 'desc')
                            <i class="fa fa-sort-desc"></i>
                        @else
                            <i class="fa fa-sort"></i>
                        @endif
                        <span>&nbsp;{!! str_replace(' ', '&nbsp;', $column->title) !!}</span>
                    </a>
                @else
                    {!! str_replace(' ', '&nbsp;', $column->title) !!}
                @endif
            </th>
        @endforeach
        @if($table->isRouteDefined('edit') || $table->isRouteDefined('destroy'))
            <th class="actions">{{ trans('tablelist::tablelist.thead.actions') }}</th>
        @endif
    </tr>
</thead>