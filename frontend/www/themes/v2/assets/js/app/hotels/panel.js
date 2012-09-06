$(function(){
var vm = {
	'rooms': [
		[{
			'adults': 3,
			'children': 0,
		},
		{
			'adults': 2,
			'children': 1,
			'ages': [7,12]
		}],[ 
		{
			'adults': 1,
			'children': 0,
			'ages': [7,12, 13, 18]
		},
		{
			'adults': 2,
			'children': 0,
			'ages': [7]
		}]
	]
};
ko.applyBindings(vm);
});