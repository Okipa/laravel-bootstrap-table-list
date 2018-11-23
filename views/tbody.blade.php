<tbody {{ classTag(config('tablelist.template.global.tbody.class')) }}>
    @if($table->list->isEmpty())
        <tr {{ classTag(config('tablelist.template.table.tr.class'), config('tablelist.template.table.tbody.tr.class')) }}>
            <td {{ classTag(config('tablelist.template.table.td.class'), config('tablelist.template.table.tbody.td.class'), 'text-center') }}
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
                    {{--{{ dd($entity->toArray(), $column->attribute) }}--}}
                    <td {{ classTag(config('tablelist.template.table.td.class'), config('tablelist.template.table.tbody.td.class')) }}>
                        {{-- custom html element --}}
                        @if($customHtmlEltClosure = $column->customHtmlEltClosure)
                            {!! $customHtmlEltClosure($entity, $column) !!}
                        @else
                            {{-- link --}}
                            @if(($isLink = $column->url) && ($entity->{$column->attribute} || $column->showIconWithNoValue))
                                <a href="{{ $isLink instanceof Closure 
                                    ? $isLink($entity, $column) 
                                    : ($isLink !== true 
                                        ? $isLink 
                                        : $entity->{$column->attribute}) }}" title="{{ strip_tags($column->title) }}">
                            @endif
                            {{-- button start--}}
                            @if($showButton = ($buttonClass = $column->buttonClass) 
                                && ($entity->{$column->attribute} 
                                    || ($customValueClosure = $column->customValueClosure) 
                                    || $column->showIconWithNoValue))
                                <button {{ classTag(
                                    $buttonClass,
                                    $entity->{$column->attribute} 
                                        ? str_slug(strip_tags($entity->{$column->attribute}), '-')
                                        : null,
                                    isset($customValueClosure)
                                        ? str_slug(strip_tags($customValueClosure($entity, $column)), '-')
                                        : null
                                ) }}>
                            @endif
                                {{-- icon--}}
                                @if(($entity->{$column->attribute} && $column->icon) 
                                || (! $entity->{$column->attribute} && $column->icon && $column->showIconWithNoValue))
                                    {!! $column->icon !!}
                                @endif
                                {{-- custom value --}}
                                @if($customValueClosure = $column->customValueClosure)
                                    {{ $customValueClosure($entity, $column) }}
                                {{-- string limit --}}
                                @elseif($stringLimit = $column->stringLimit)
                                    {{ str_limit(strip_tags($entity->{$column->attribute}), $stringLimit) }}
                                {{-- date format --}}
                                @elseif($columnDateFormat = $column->columnDateFormat)
                                    {{ $entity->{$column->attribute} 
                                        ? Carbon\Carbon::parse($entity->{$column->attribute})->format($columnDateFormat) 
                                        : null }}
                                {{-- time format --}}
                                @elseif($columnDateTimeFormat = $column->columnDateTimeFormat)
                                    {{ $entity->{$column->attribute} 
                                        ? Carbon\Carbon::parse($entity->{$column->attribute})->format($columnDateTimeFormat)
                                        : null }}
                                {{-- basic value --}}
                                @else
                                    {!! $entity->{$column->attribute} !!}
                                @endif
                            {{-- button end --}}
                            @if($showButton)
                                </button>
                            @endif
                            {{-- link end --}}
                            @if($isLink)
                                </a>
                            @endif
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
