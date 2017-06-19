<tbody>
    @if($table->list->isEmpty())
        <tr>
            <td class="empty-list" colspan="{{ $table->getColumnsCount() + ($table->isRouteDefined('edit') || $table->isRouteDefined('destroy') ? 1 : 0) }}">
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
                        @if($button_class = $column->button_class)
                            <button class="{{ $button_class }} {{ str_slug(strip_tags($entity->{$column->attribute})) }}">
                        @endif
                            @if($image_path_closure = $column->image_path_closure)
                                <img src="{{ $image_path_closure($entity, $column) }}" alt="{{ strip_tags($entity->{$column->attribute}) }}">
                            @elseif($string_limit = $column->string_limit)
                                {{ str_limit(strip_tags($entity->{$column->attribute}, $string_limit)) }}
                            @elseif($date_format = $column->date_format)
                                {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $entity->{$column->attribute})->format($date_format) }}
                            @elseif($column->is_activation_toggle)
                                <form role="form" method="POST" action="{{ $table->getRoute('activation', ['id' => $entity->id]) }}">
                                    {!! csrf_field() !!}
                                    {!! ToggleSwitchButton::render(
                                        'active',
                                        old('active') ? old('active') : $entity->{$column->attribute},
                                        null,
                                        null,
                                        'active_' . $entity->id
                                    ) !!}
                                </form>
                            @elseif($configuration_closure = $column->configuration_closure)
                                {{ $configuration_closure($entity, $column) }}
                            @elseif($link_closure = $column->link_closure)
                                <a href="{{ $link_closure($entity, $column) }}" title="{{ strip_tags($entity->{$column->attribute}) }}">
                                    {!! $entity->{$column->attribute} !!}
                                </a>
                            @elseif($html_element_closure = $column->html_element_closure)
                                {!! $html_element_closure($entity, $column) !!}
                            @else
                                {!! $entity->{$column->attribute} !!}
                            @endif
                        @if($button_class)
                            </button>
                        @endif
                    </td>
                @endforeach

                {{-- actions --}}
                @if($table->isRouteDefined('edit') || $table->isRouteDefined('destroy'))
                    <td class="actions">

                        {{-- edit --}}
                        @if($table->isRouteDefined('edit'))
                            <form role="form" method="GET" action="{{ $table->getRoute('edit', ['id' => $entity->id]) }}">
                                <button class="btn btn-primary btn-rounded" type="submit" title="{{ trans('tablelist::tablelist.tbody.action.edit') }}">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </button>
                            </form>
                        @endif

                        {{-- delete --}}
                        @if($table->isRouteDefined('destroy'))
                            <form role="form" method="POST" action="{{ $table->getRoute('destroy', ['id' => $entity->id]) }}">
                                {!! csrf_field() !!}
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="btn btn-danger btn-rounded" data-toggle="modal" data-target=".destroy-confirm-modal-{{ $entity->id }}" title="{{ trans('tablelist::tablelist.tbody.action.destroy') }}">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </button>
                                @include('tablelist::destroy-confirm-modal')
                            </form>
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    @endif
</tbody>