window.onload = function() {

    let income = '{{ income }}';
    let  myPoints = "";

    for (let i = 0; i < income['amount'].length; i++) {
        myPoints = myPoints + "{y: " + income['amount'][i] + ": " + income['name'][i] + "}";
    }
    
    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        title: {
            text: "Desktop Search Engine Market Share - 2016"
        },
        data: [{
            type: "pie",
            startAngle: 240,
            yValueFormatString: "##0.00\"%\"",
            indexLabel: "{label} {y}",
            dataPoints: [
                myPoints
            ]
        }]
    });
    chart.render();

    console.log(myPoints);
    
    }