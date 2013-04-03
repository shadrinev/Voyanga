<script type="text/html" id="items">
    <!-- ko if: $data.roundTrip -->
        <!-- ko foreach: $data.items -->
            <!-- ko if:$data.isFlight -->
                <span data-bind="template: {name: 'flight-rt-part-template', data: $data}"></span>
            <!-- /ko -->
            <!-- ko if: $data.isHotel -->
                <span data-bind="template: {name: 'hotel-part-template', data: $data}"></span>
            <!-- /ko -->
        <!-- /ko -->
    <!-- /ko -->
    <!-- ko ifnot: $data.roundTrip -->
        <!-- ko foreach: $data.items -->
            <!-- ko if:$data.isFlight -->
                <span data-bind="template: {name: 'flight-part-template', data: $data}"></span>
            <!-- /ko -->
            <!-- ko if:$data.isHotel -->
                <span data-bind="template: {name: 'hotel-part-template', data: $data}"></span>
            <!-- /ko -->
        <!-- /ko -->
    <!-- /ko -->
</script>