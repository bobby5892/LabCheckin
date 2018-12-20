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
class CurrentLab{
	constructor(){
		this.config = config;
		console.log("Loaded CurrentLab Widget");
//how often to refresh
		this.refreshSeconds = 20;
		this.timeTillRefresh = 15;

		this.box = document.getElementById("currentLab");
		this.courses = null;
		this.liveData = null;
		this.courseDataReady = false;
		this.liveDataReady = false;

		this.loadCourses();
		this.loadLiveData();

		this.timer = setInterval(() => {this.tick()}, 1000);
		//this.buildTable();
	}
	tick(){
		this.timeTillRefresh--;
		if(this.timeTillRefresh == 0){
			this.loadLiveData();
			this.timeTillRefresh = this.refreshSeconds;
		}
		document.getElementById("current-lab-refreshtimer").innerHTML = this.getLabRefreshTimer();
		document.getElementById("current-lab-labcount").innerHTML = this.getLabVisitorCount();
	}
	getLabVisitorCount(){
		let count = 0;
		if(this.liveData.length > count){
			count = this.liveData.length;
		}
		return  "Count: " + count;
	}
	getLabRefreshTimer(){
		return  "Refresh: " + this.timeTillRefresh;
	}
	updateWhenReady(){
		console.log("update Course:" + this.courseDataReady + " Live:" + this.liveDataReady );

		if(this.courseDataReady && this.liveDataReady){
			this.buildTable();
		}
	}
	buildTable(){
		let output = "<div id='current-lab-labcount'>" + this.getLabVisitorCount() + "</div><div id='current-lab-refreshtimer'>"+ this.getLabRefreshTimer()+  "</div> <table>";
		output += "<tr><th>Student</th><th>Course</th><th>Duration</th><th>&nbsp;</th></tr>";
		console.log(this.liveData.length);
		for(let i=0; i<this.liveData.length;i++){
			output += "<tr>" 
			+ "<td class='studentid'>"+ this.liveData[i].studentid + "</td>"
			+ "<td class='course'>" + this.getCourseName(this.liveData[i].courseid) + "</td>"
			+ "<td class='duration'>" + this.liveData[i].duration + "</td>"
			+ "<td class='buttonContainer'><button class='currentlab-checkout-button' data-studentid=\"" + this.liveData[i].studentid + "\">Checkout</button></td>"
			+"</tr>";
		}
		
		output + "</table>";
		this.box.innerHTML = output;

		this.bindEventcheckouts();
	}
	bindEventcheckouts(){

		let list = document.getElementsByClassName("currentlab-checkout-button");
		if (typeof list !== 'undefined') {
			for(let i=0;i<list.length;i++){
				list[i].addEventListener('click', (event) =>{
					if(confirm("Are you sure you want to check this student out of the lab?")){
						//console.log(event);
						console.log();
						let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "savecheckout";
						console.log(url);
						fetch(url,{
							method: "POST",
							headers: {'Content-Type':'application/x-www-form-urlencoded'},
							body:  "studentid=" + event.srcElement.dataset.studentid
						}).then(res => res.json())
						.then(response => {
							if(response.success == "true"){
								console.log("Checked out");
							}
							else{
								console.log("Not found");
							}
							// either way lets reload data
							this.loadLiveData();
						});
					}
				});
			}
		}
		
	}
	getCourseName(id){
		for(let i=0; i<this.courses.length;i++){
			if(this.courses[i].id == id){
				return this.courses[i].name;
			}
		}
		
	}
	loadCourses(){
		let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "/admin/" + "getcourses";
		 fetch(url)
		.then(res => res.json())
		.then(response => {
			this.courses = response.data;
			this.courseDataReady = true;
			this.updateWhenReady();
		});
		
	}
	loadLiveData(){
		let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "/admin/" + "getlivelab";
		 fetch(url)
		.then(res => res.json())
		.then(response => {
			this.liveData = response.data;
			this.liveDataReady = true;
			this.updateWhenReady();
		});
	}

}
let currentlab;
window.addEventListener('load',() =>{
	currentlab = new CurrentLab();
});