<tbody {{ classTag(config('tablelist.template.global.tbody.class')) }}>
    @if($table->list->isEmpty())
        <tr {{ classTag(config('tablelist.template.table.tr.class'), config('tablelist.template.table.tbody.tr.class')) }}>
            <td {{ classTag(config('tablelist.template.table.td.class'), config('tablelist.template.table.tbody.td.class')) }}
                colspan="{{ $table->getColumnsCount() + ($table->isRouteDefined('edit') || $table->isRouteDefined('destroy') ? 1 : 0) }}">
                <span class="text-info">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                </span>
                {{ trans('tablelist::tablelist.tbody.empty') }}
            </td>
        </tr>
    @else
        @foreach($table->list as $entity)
            <tr {{ classTag(
            config('tablelist.template.table.tr.class'),
            config('tablelist.template.table.tbody.tr.class'),
            $entity->disabled ? $table->disableLinesClass : null,
            $entity->highlighted ? $table->highlightLinesClass : null
            ) }}>
                @foreach($table->columns as $column)
                    <td {{ classTag(config('tablelist.template.table.td.class'), config('tablelist.template.table.tbody.td.class')) }}>
                        {{-- button start--}}
                        @if($buttonClass = $column->buttonClass)
                            <button {{ classTag($buttonClass, str_slug(strip_tags($entity->{$column->attribute}))) }}>
                        @endif
                            {{-- string limit --}}
                            @if($stringLimit = $column->stringLimit)
                                {{ str_limit(strip_tags($entity->{$column->attribute}, $stringLimit)) }}
                            {{-- date format --}}
                            @elseif($columnDateFormat = $column->columnDateFormat)
                                {{ $entity->{$column->attribute} ? Carbon\Carbon::createFromFormat(
                                    'Y-m-d H:i:s', $entity->{$column->attribute}
                                )->format($columnDateFormat) : null }}
                            {{-- link --}}
                            @elseif($linkClosure = $column->linkClosure)
                                <a href="{{ $linkClosure($entity, $column) }}"
                                   title="{{ strip_tags($entity->{$column->attribute}) }}">
                                    {!! $entity->{$column->attribute} !!}
                                </a>
                            {{-- custom value --}}
                            @elseif($customValueClosure = $column->customValueClosure)
                                {{ $customValueClosure($entity, $column) }}
                            {{-- custom html element --}}
                            @elseif($customHtmlEltClosure = $column->customHtmlEltClosure)
                                {!! $customHtmlEltClosure($entity, $column) !!}
                            {{-- basic value --}}
                            @else
                                {!! $entity->{$column->attribute} !!}
                            @endif
                        {{-- button end --}}
                        @if($buttonClass)
                            </button>
                        @endif
                    </td>
                @endforeach
                {{-- actions --}}
                @if(($table->isRouteDefined('edit') || $table->isRouteDefined('destroy')))
                    <td {{ classTag('text-right', config('tablelist.template.table.td.class'), config('tablelist.template.table.tbody.td.class')) }}>
                        {{-- edit button --}}
                        @if($table->isRouteDefined('edit'))
                            @if(! $entity->disabled)
                                <form {{ classTag('edit-' . $entity->id, config('tablelist.template.table.tbody.edit.container.class')) }}
                                      role="form"
                                      method="GET"
                                      action="{{ $table->getRoute('edit', ['id' => $entity->id]) }}">
                            @endif
                                    <button type="submit"
                                            {{ classTag(config('tablelist.template.table.tbody.edit.item.class'), $entity->disabled ? 'disabled' : null) }}
                                            title="{{ trans('tablelist::tablelist.tbody.action.edit') }}"
                                            @if($entity->disabled)disabled="disabled" @endisset>
                                        {!! config('tablelist.template.table.tbody.edit.item.icon') !!}
                                    </button>
                            @if(! $entity->disabled)
                                </form>
                            @endif
                        @endif
                        {{-- destroy button --}}
                        @if($table->isRouteDefined('destroy'))
                            @if(! $entity->disabled)
                                <form {{ classTag('destroy-' . $entity->id, config('tablelist.template.table.tbody.destroy.container.class')) }}
                                      role="form"
                                      method="POST"
                                      action="{{ $table->getRoute('destroy', ['id' => $entity->id]) }}">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="_method" value="DELETE">
                            @endif
                                    <button type="button"
                                            {{ classTag(config('tablelist.template.table.tbody.destroy.item.class'), $entity->disabled ? 'disabled' : null) }}
                                            title="{{ trans('tablelist::tablelist.tbody.action.destroy') }}"
                                            @if(config('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal'))
                                            data-toggle="modal"
                                            data-target="#destroy-confirm-modal-{{ $entity->id }}"
                                            @endif
                                            @if($entity->disabled)disabled="disabled" @endif>
                                        {!! config('tablelist.template.table.tbody.destroy.item.icon') !!}
                                    </button>
                            @if(! $entity->disabled)
                                    @if(config('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal'))
                                        @include('tablelist::destroy-confirm-modal')
                                    @endif
                                </form>
                            @endif
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    @endif
</tbody>
