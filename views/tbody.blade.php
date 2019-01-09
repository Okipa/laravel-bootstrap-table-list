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
                    @php
                        $value = $entity->{$column->attribute};
                        $customHtml = $column->customHtmlEltClosure ? ($column->customHtmlEltClosure)($entity, $column) : null;
                        $customValue = $column->customValueClosure ? ($column->customValueClosure)($entity, $column) : null;
                        $link = $column->url instanceof Closure 
                                    ? ($column->url)($entity, $column) 
                                    : ($column->url !== true 
                                        ? $column->url 
                                        : ($customValue ? $customValue : $value));
                        $showLink = $link && ($customValue || $value || $column->showIconWithNoValue);
                        $showButton = $column->buttonClass && ($value || $customValue || $column->showIconWithNoValue);
                    @endphp
                    {{--{{ dd($entity->toArray(), $column->attribute) }}--}}
                    <td {{ classTag(config('tablelist.template.table.td.class'), config('tablelist.template.table.tbody.td.class')) }}>
                        {{-- custom html element --}}
                        @if($customHtml)
                            {!! $customHtml !!}
                        @else
                            {{-- link --}}
                            @if($showLink)
                                <a href="{{ $link }}" title="{{ $customValue ? $customValue : $value }}">
                            @endif
                            {{-- button start--}}
                            @if($showButton)
                                <button {{ classTag(
                                    $column->buttonClass,
                                    $value ? str_slug(strip_tags($value), '-') : null,
                                    $customValue ? str_slug(strip_tags($customValue), '-') : null
                                ) }}>
                            @endif
                                {{-- icon--}}
                                @if(($value && $column->icon) || (! $value && $column->icon && $column->showIconWithNoValue))
                                    {!! $column->icon !!}
                                @endif
                                {{-- custom value --}}
                                @if($customValue)
                                    {{ $customValue }}
                                {{-- string limit --}}
                                @elseif($column->stringLimit)
                                    {{ str_limit(strip_tags($value), $column->stringLimit) }}
                                {{-- date format --}}
                                @elseif($column->columnDateFormat)
                                    {{ $value 
                                        ? Carbon\Carbon::parse($value)->format($column->columnDateFormat) 
                                        : null }}
                                {{-- time format --}}
                                @elseif($column->columnDateTimeFormat)
                                    {{ $value 
                                        ? Carbon\Carbon::parse($value)->format($column->columnDateTimeFormat)
                                        : null }}
                                {{-- basic value --}}
                                @else
                                    {!! $value !!}
                                @endif
                            {{-- button end --}}
                            @if($showButton)
                                </button>
                            @endif
                            {{-- link end --}}
                            @if($showLink)
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
