$(function(){
var vm = {
	'rooms': ko.observableArray([
		{
			'adults': 3,
			'children': 0,
		},
		{
			'adults': 2,
			'children': 2,
			'ages': [7,12]
		},
		{
			'adults': 1,
			'children': 2,
			'ages': [7,12]
		},
		{
			'adults': 3,
			'children': 1,
			'ages': [7,12]
		}
	])
};
ko.applyBindings(vm);
});