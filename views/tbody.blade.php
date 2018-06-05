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
            <tr class="@if($entity->disabled)disabled @endif()@if($entity->highlighted)highlighted @endif">
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
                    <td class="actions">
                        {{-- edit button --}}
                        @if($table->isRouteDefined('edit'))
                            @if($entity->disabled)
                                @include('tablelist::components.buttons.edit', ['class' => 'disabled'])
                            @else
                                <form class="edit"
                                      role="form"
                                      method="GET"
                                      action="{{ $table->getRoute('edit', ['id' => $entity->id]) }}">
                                    @include('tablelist::components.buttons.edit')
                                </form>
                            @endif
                        @endif
                        {{-- destroy button --}}
                        @if($table->isRouteDefined('destroy'))
                            @if($entity->disabled)
                                @include('tablelist::components.buttons.destroy', ['class' => 'disabled'])
                            @else
                                <form class="destroy"
                                      role="form"
                                      method="POST"
                                      action="{{ $table->getRoute('destroy', ['id' => $entity->id]) }}">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="_method" value="DELETE">
                                    @include('tablelist::components.buttons.destroy')
                                    @if(config('tablelist.template.button.destroy.trigger-bootrap-native-modal'))
                                        @include('tablelist::components.modals.destroy-confirmation')
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
