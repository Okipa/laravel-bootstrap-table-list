<tfoot>

    <tr>

        <td colspan="{{ $table->getColumnsCount() + ($table->isRouteDefined('edit') || $table->isRouteDefined('destroy') ? 1 : 0) }}">

            <div class="row">

                @if($table->isRouteDefined('create'))
                    <div class="tfoot-tab col-sm-4 create-button">
                        @include('tablelist::components.buttons.create', ['route' => $table->getRoute('create')])
                    </div>
                @endif

                <div class="tfoot-tab navigation-status @if($table->isRouteDefined('create'))col-sm-4 @else col-sm-6 text-left @endif">
                    {!! $table->navigationStatus() !!}
                </div>

                <div class="tfoot-tab pagination-container @if($table->isRouteDefined('create'))col-sm-4 @else col-sm-6 @endif">
                    {!! $table->list->render() !!}
                </div>
            </div>
        </td>
    </tr>
</tfoot>