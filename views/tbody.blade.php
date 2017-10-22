<tbody>
    @if($table->list->isEmpty())
        <tr>
            <td class="empty-list"
                colspan="{{ $table->getColumnsCount() + ($table->isRouteDefined('edit') || $table->isRouteDefined('destroy') ? 1 : 0) }}">
                <span class="text-info">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                </span>
                {{ trans('tablelist::tablelist.tbody.empty') }}
            </td>
        </tr>
    @else
        @foreach($table->list as $entity)
            <tr>
                @foreach($table->columns as $column)
                    <td>
                        {{-- button start--}}
                        @if($buttonClass = $column->buttonClass)
                            <button class="{{ $buttonClass }} {{ str_slug(strip_tags($entity->{$column->attribute})) }}">
                        @endif
                            {{-- string limit --}}
                            @if($stringLimit = $column->stringLimit)
                                {{ str_limit(strip_tags($entity->{$column->attribute}, $stringLimit)) }}
                            {{-- date format --}}
                            @elseif($dateFormat = $column->dateFormat)
                                {{ $entity->{$column->attribute} ? Carbon\Carbon::createFromFormat(
                                    'Y-m-d H:i:s', $entity->{$column->attribute}
                                )->format($dateFormat) : null }}
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
                            @elseif($customHtmlElementClosure = $column->customHtmlElementClosure)
                                {!! $customHtmlElementClosure($entity, $column) !!}
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
                @if($table->isRouteDefined('edit') || $table->isRouteDefined('destroy'))
                    <td class="actions">
                        {{-- edit button --}}
                        @if($table->isRouteDefined('edit'))
                            @include('tablelist::components.edit-button')
                        @endif
                        {{-- destroy button --}}
                        @if($table->isRouteDefined('destroy'))
                            @include('tablelist::components.destroy-button')
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    @endif
</tbody>
