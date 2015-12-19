function updateGraph(table){
    $.get('fetch-stats.php?type=7day&table='+table, function(ajaxData){
        var scale = d3.scale.linear()
            .domain([0, d3.max(ajaxData, function(d) { return d.amount; })])
            .range([0, 800]);

        d3.select("#"+table+"-stats")
            .selectAll("div")
            .data(ajaxData)
            .enter().append("div")
            .html(function(d) { return '<span class="time bar-label" time="'+d.dateAdded+'">...</span><div class="bar" style="width: '+scale(d.amount)+'px">'+d.amount+'</div>'; });
        updateTimes('.bar-label', 'll');
    });
}

$(document).ready(function(){
    updateGraph('templates');
    updateGraph('source')
});