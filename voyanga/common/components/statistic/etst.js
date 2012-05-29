var map = function() {
    var date = ISODate(this.dateCreate);
    var key = {modelName: this.modelName, modelId: this.modelId, year: date.getFullYear(), month: date.getMonth(), day: date.getDate()};
    emit(key, {count: 1});
};

var reduce = function(key, values) {
    var sum = 0;
    values.forEach(function(value) {
        sum += value['count'];
    });
    return {count: sum};
};

db..mapReduce(
    function() {
        var k = {modelName: this.modelName, modelId: this.modelId};
        emit(k, {count:1});
    },
    function (key, vals) {
        var total = 0;
        for (var i = 0; i < vals.length; i++) {
            total += vals[i];
        }
        return total;
    },
    {
        out : {merge : "resultName" }
    }
);
