<div {{ classTag('table-list-container', config('tablelist.template.table.container.class')) }}>
    <table {{ classTag('table', config('tablelist.template.table.item.class')) }}>
        @include('tablelist::thead')
        @include('tablelist::tbody')
        @include('tablelist::tfoot')
    </table>
</div>
