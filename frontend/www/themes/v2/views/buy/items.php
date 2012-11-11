<script type="text/html" id="items">
    <!-- ko if: $data.roundTrip -->
        <span data-bind="template: {name: 'flight-rt-part-template', data: items}"></span>
        <!-- ko if: $data.hasHotel -->
            <span data-bind="template: {name: 'hotel-part-template', data: lastHotel}"></span>
        <!-- /ko -->
    <!-- /ko -->
    <!-- ko ifnot: $data.roundTrip -->
        <!-- ko foreach: $data.items -->
            <span data-bind="template: {name: 'flight-part-template', data: $data}"></span>
            <span data-bind="template: {name: 'hotel-part-template', data: $data}"></span>
        <!-- /ko -->
    <!-- /ko -->
</script>