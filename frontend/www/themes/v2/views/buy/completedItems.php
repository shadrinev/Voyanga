<script type="text/html" id="completedItems">
    <!-- ko if: $data.roundTrip -->
        <span data-bind="template: {name: 'completed-flight-rt-part-template', data: items}"></span>
        <!-- ko if: $data.hasHotel -->
            <span data-bind="template: {name: 'completed-hotel-part-template', data: lastHotel}"></span>
        <!-- /ko -->
    <!-- /ko -->
    <!-- ko ifnot: $data.roundTrip -->
        <!-- ko foreach: $data.items -->
            <!-- ko if:$data.isFlight -->
                <span data-bind="template: {name: 'completed-flight-part-template', data: $data}"></span>
            <!-- /ko -->
            <!-- ko if:$data.isHotel -->
                <span data-bind="template: {name: 'completed-hotel-part-template', data: $data}"></span>
            <!-- /ko -->
        <!-- /ko -->
    <!-- /ko -->
</script>