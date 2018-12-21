/*
[---------------------------------------------------]

   __       _         ___ _               _    
  / /  __ _| |__     / __\ |__   ___  ___| | __
 / /  / _` | '_ \   / /  | '_ \ / _ \/ __| |/ /
/ /__| (_| | |_) | / /___| | | |  __/ (__|   < 
\____/\__,_|_.__/  \____/|_| |_|\___|\___|_|\_\

[---------------------------------------------------]                                               

Lab Check
by Robert Moore 12/19/2018

robert@eugeneprogramming.com
https://github.com/bobby5892/LabCheckin

License CC BY 
https://creativecommons.org/licenses/by/4.0/
*/
class DataViewer{
	constructor(){
		this.config = config;
		
		this.Data = [];
		// elements
		this.startDatePicker = document.getElementById('startDate');
		this.endDatePicker = document.getElementById('endDate');
		this.updateButton = document.getElementById('update');

		// bind click event
		this.updateButton.addEventListener('click',(event) =>{
			event.preventDefault();
			this.update();
		});
		this.update();
		console.log("Loaded DataViewer");
	}
	dateForsubmission(string){
		// Change Date from Y-m-d to m-d-Y
		let temp = string.split("-");
		return (temp[1] + "-" + temp[2] + "-" + temp[0]);
	}
	getData(){
		let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "/admin/getData";
		fetch(url,{
			method: "POST",
				headers: {'Content-Type':'application/x-www-form-urlencoded'},
				body:  "startDate=" + this.dateForsubmission(this.startDatePicker.value) +"&endDate=" + this.dateForsubmission(this.endDatePicker.value)
			}).then(res => res.json())
			.then(response => {
				console.log(response.Count);
				this.Data = response.Data;
				this.renderData();
			});
	}
	update(){
		this.getData();
		
	}
	renderData(){
		// Lets put the data where it goes
		if(this.config.dataType == "Table"){
			document.getElementById(this.config.dataSource).innerHTML = this.buildTable();
		}
		else if(this.config.dataType == "Chart"){
			this.buildChart();
		}
	}
	buildTable(){
		let output = "<table>";
		output += "<tr><th>Course</th><th>LabVisits</th></tr>";
		for(let i=0; i<this.Data.length;i++){
			output += "<tr>" 
			+ "<td class='course'>" + this.Data[i].course + "</td>"
			+ "<td class='duration'>" + this.Data[i].count + "</td>"
			+"</tr>";
		}
		
		output + "</table>";
		return output;	
	}
	buildChart(){
		// put the canvas on the page
		let output = "<canvas id='barChart'></canvas>";
		document.getElementById(this.config.dataSource).innerHTML += output;

		let chartDataSet = [];
		let chartLabels = []
		// now build chart data for chart.js
		for(let i=0; i<this.Data.length;i++){
			// this is [x,y]
			chartDataSet.push(this.Data[i].count);
			chartLabels.push(this.Data[i].course);
		}

		// Build data to spec https://www.chartjs.org/docs/latest/axes/cartesian/category.html

		let chartData = {
			labels: chartLabels,
			datasets: [{chartDataSet}]
		};

		console.log("chartData" + JSON.stringify(chartData));
		let ctx = document.getElementById('barChart').getContext("2d");
		let stackedBar = new Chart(ctx, {
		    type: 'horizontalBar',
		    data: chartData,
		    options: {
		        legend: {
		            display: true,
		            labels: {
		                fontColor: 'rgb(255, 99, 132)'
		            }
		        },
		        title: {
	         	   display: true,
	            	text: 'LAB USAGE'
        		}
		    },
		   
		});
	}
}
let dataviewer;
window.addEventListener('load',() =>{
	dataviewer = new DataViewer();
});