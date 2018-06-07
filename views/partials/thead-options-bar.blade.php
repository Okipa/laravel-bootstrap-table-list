@if(count($table->sortableColumns) || count($table->searchableColumns) || $table->rowsNumberSelectorEnabled)
    <tr {{ classTag(config('tablelist.template.table.tr.class'), config('tablelist.template.table.thead.options-bar.tr.class')) }}>
        <td {{ classTag(config('tablelist.template.table.td.class'), config('tablelist.template.table.thead.options-bar.td.class')) }}
            colspan="{{ $table->getColumnsCount() + ($table->isRouteDefined('edit') || $table->isRouteDefined('destroy') ? 1 : 0) }}">
            <div class="row">
                {{-- rows number selector --}}
                <div {{ classTag('rows-number-selector', config('tablelist.template.table.thead.options-bar.rows-number-selector.item.class')) }}>
                    @if($table->rowsNumberSelectorEnabled)
                        <form role="form" method="GET" action="{{ $table->getRoute('index') }}">
                            <input type="hidden" name="search" value="{{ $table->request->search }}">
                            <input type="hidden" name="sortBy" value="{{ $table->request->sortBy }}">
                            <input type="hidden" name="sortDir" value="{{ $table->request->sortDir }}">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span {{ classTag('input-group-text', config('tablelist.template.table.thead.options-bar.rows-number-selector.lines.container.class')) }}>
                                        <span {{ classTag(config('tablelist.template.table.thead.options-bar.rows-number-selector.lines.item.class')) }}>
                                            {!! config('tablelist.template.table.thead.options-bar.rows-number-selector.lines.item.icon') !!}
                                        </span>
                                    </span>
                                </div>
                                <input class="form-control"
                                       type="number"
                                       name="rowsNumber"
                                       value="{{ $table->request->rowsNumber }}"
                                       placeholder="{{ trans('tablelist::tablelist.thead.rows_number') }}"
                                       aria-label="{{ trans('tablelist::tablelist.thead.rows_number') }}">
                                <div class="input-group-append">
                                    <div {{ classTag('input-group-text', config('tablelist.template.table.thead.options-bar.rows-number-selector.validate.container.class')) }}>
                                        <button type="submit"
                                                {{ classTag(config('tablelist.template.table.thead.options-bar.rows-number-selector.validate.item.class')) }}
                                                title="{{ trans('tablelist::tablelist.thead.rows_number') }}">
                                            {!! config('tablelist.template.table.thead.options-bar.rows-number-selector.validate.item.icon') !!}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
                {{-- spacer --}}
                <div {{ classTag('spacer', config('tablelist.template.table.thead.options-bar.spacer.item.class')) }}>
                </div>
                {{-- search bar --}}
                <div {{ classTag('search-bar', config('tablelist.template.table.thead.options-bar.search-bar.item.class')) }}>
                    @if(count($table->searchableColumns))
                        <form role="form" method="GET" action="{{ $table->getRoute('index') }}">
                            <input type="hidden" name="rowsNumber" value="{{ $table->request->rowsNumber }}">
                            <input type="hidden" name="sortBy" value="{{ $table->request->sortBy }}">
                            <input type="hidden" name="sortDir" value="{{ $table->request->sortDir }}">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span {{ classTag('input-group-text', config('tablelist.template.table.thead.options-bar.search-bar.search.container.class')) }}>
                                        <span {{ classTag(config('tablelist.template.table.thead.options-bar.search-bar.search.item.class')) }}>
                                            {!! config('tablelist.template.table.thead.options-bar.search-bar.search.item.icon') !!}
                                        </span>
                                    </span>
                                </div>
                                <input class="form-control"
                                       type="text"
                                       name="search"
                                       value="{{ $table->request->search }}"
                                       placeholder="{{ trans('tablelist::tablelist.thead.search') }} {{ $table->getSearchableTitles() }}"
                                       aria-label="{{ trans('tablelist::tablelist.thead.search') }} {{ $table->getSearchableTitles() }}">
                                @if($table->request->search)
                                    <div class="input-group-append">
                                        <a {{ classTag('input-group-text', config('tablelist.template.table.thead.options-bar.search-bar.cancel.container.class')) }}
                                           href="{{ $table->getRoute('index', [
                                                'search' => null,
                                                'rowsNumber' => $table->request->rowsNumber,
                                                'sortBy' => $table->request->sortBy,
                                                'sortDir' => $table->request->sortDir,
                                                ]) }}">
                                            <span {{ classTag(config('tablelist.template.table.thead.options-bar.search-bar.cancel.item.class')) }}>
                                                {!! config('tablelist.template.table.thead.options-bar.search-bar.cancel.item.icon') !!}
                                            </span>
                                        </a>
                                    </div>
                                @else
                                    <div class="input-group-append">
                                        <span {{ classTag('input-group-text', config('tablelist.template.table.thead.options-bar.search-bar.validate.container.class')) }}>
                                            <button type="submit"
                                                    {{ classTag(config('tablelist.template.table.thead.options-bar.search-bar.validate.item.class')) }}
                                                    title="{{ trans('tablelist::tablelist.thead.search') }} {{ $table->getSearchableTitles() }}">
                                                {!! config('tablelist.template.table.thead.options-bar.search-bar.validate.item.icon') !!}
                                            </button>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </td>
    </tr>
@endif
