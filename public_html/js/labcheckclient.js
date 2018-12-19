class LabCheckclient{
	constructor(){
		this.config = config;
		this.alertBox = document.getElementById('alertBox');
		this.courses = [];
		this.showCheckin = false;
		this.showCheckout = false;
		/* Lnumber */
		this.stage1 = {
			isVisible: true,
			container:document.getElementsByClassName('container')[0],
			lnumberBox:document.getElementById('Lnumber'),
			submit:document.getElementById('stage1submit')
		}
		
		/* Action - checkin/checkout */
		this.stage2 = {
			isVisible:false,
			container:document.getElementsByClassName('container')[1],
			btnCheckIn:document.getElementById('buttonCheckIn'),
			btnCheckOut:document.getElementById('buttonCheckOut')
		}
		/* Class selection*/
		this.stage3 = { 
			isVisible : false,
			container : document.getElementsByClassName('container')[2],
			selectClass : document.getElementById('classSelect'),
			stage3submit : document.getElementById('stage3submit')
		}
/* Stage 1 */
		this.stage1.submit.addEventListener('click', (event) =>{
			event.preventDefault();
			this.hideAlert();
			this.submitLnumber();
		});

		this.stage1.lnumberBox.addEventListener('keypress', (event) => {
			this.hideAlert();
		});
/* Stage 2 */
		this.stage2.btnCheckIn.addEventListener('click', (event)  => {
			this.hideAlert();
			this.submitActionSelect();
		});	
		this.stage2.btnCheckOut.addEventListener('click', (event)  => {
			this.submitCheckout();
		});	
		// Load Courses
		this.getCourses();
/* Stage 3 */
		this.stage3.stage3submit.addEventListener('click', (event) => {
			this.submitClassSelect();
		});		
	}
	getCourses(){
		let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "courses";
		fetch(url,{
			method: "GET",
		}).then(res => res.json())
		.then(response => {
			this.courses = response;
			return this.courses;
		}).then(
			(courses) => {
				let count = 0;
				for(let i=0; i< courses.length;i++){
					let course = courses[i];
					let option = document.createElement("option");
					option.text = course.name;
					option.value = course.id;
					this.stage3.selectClass.add(option, this.stage3.selectClass[i]);
				}
			}
		);
	}
	submitCheckout(){
		let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "savecheckout";
		console.log(url);
		fetch(url,{
			method: "POST",
			headers: {'Content-Type':'application/x-www-form-urlencoded'},
			body:  "studentid=" + this.stage1.lnumberBox.value
		}).then(res => res.json())
		.then(response => {
			if(response.success == "true"){
				this.reset();
			}
			else{
				this.reset();
			}
		});
	}
	submitLnumber(){
		// check for empty box
		if(this.stage1.lnumberBox.value.length == 0){
			this.showErrorAlert("LNumber cannot be empty");
			return false;
		}
		let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "validateL";
		console.log(url);
		fetch(url,{
			method: "POST",
			headers: {'Content-Type':'application/x-www-form-urlencoded'},
			body:  "studentid=" + this.stage1.lnumberBox.value
		}).then(res => res.json())
		.then(response => {
			if(response.success == "true"){
				console.log("Good L Number");
				this.hideContainers();
				this.stage2.isVisible = true;
				this.showContainer(this.stage2.container);
				this.activateButtons();
			}
			else{
				labcheckin.showErrorAlert(response.response);
				console.log("Bad L Number");
				
			}
		});
	}
	activateButtons(){
		console.log("activating Buttons");
		let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "isCheckedIn";
		console.log(url);
		fetch(url,{
			method: "POST",
			headers: {'Content-Type':'application/x-www-form-urlencoded'},
			body:  "studentid=" + this.stage1.lnumberBox.value
		}).then(res => res.json())
		.then(response => {
			console.log(response)
			if(response.success){
				// Already Checked in - show Checkout
				$(this.stage2.btnCheckOut).fadeIn();
				console.log("show checkout");
			}
			else{
				// Not checked in - show Checkin
				$(this.stage2.btnCheckIn).fadeIn();
				console.log("show checkin");
			}
		});
	}
	submitActionSelect(){
		this.hideContainers();
		this.stage3.isVisible = true;
		this.showContainer(this.stage3.container);
	}
	submitClassSelect(){
		// a check in
		let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "savecheckin";
		console.log(url);
		fetch(url,{
			method: "POST",
			headers: {'Content-Type':'application/x-www-form-urlencoded'},
			body:  "studentid=" + this.stage1.lnumberBox.value + "&courseid=" + this.stage3.selectClass.value
		}).then(res => res.json())
		.then(response => {
			if(response.success){
				console.log("Good Submission");
				this.reset();
			}
			else{
				console.log("Bad Submission");
				labcheckin.showErrorAlert(response.response);
				this.reset();
			}
		});
	}
	showErrorAlert(error){
		this.alertBox.classList.add("alert-danger");
		this.alertBox.innerHTML = error;
		$(this.alertBox).fadeIn();
	}
	hideAlert(){
		$(this.alertBox).fadeOut();
		this.alertBox.classList.remove("alert-danger");
		this.alertBox.innerHTML = "";
	}
	showContainer(container){
		$(container).fadeIn();
	}
	hideContainers(){
		if(this.stage1.isVisible){ 
			this.stage1.isVisible = false;
			$(this.stage1.container).fadeOut();
		}
		if(this.stage2.isVisible){ 
			this.stage2.isVisible = false;
			$(this.stage2.container).fadeOut();
		}
		if(this.stage3.isVisible){ 
			this.stage3.isVisible = false;
			$(this.stage3.container).fadeOut();
		}
	}
	reset(){
		// Reset visibles
		this.stage1.isVisible = true;
		this.stage2.isVisible = false;
		this.stage3.isVisible = false;

		$(this.stage1.container).fadeOut();
		$(this.stage2.container).fadeOut();
		$(this.stage3.container).fadeOut();

		$(this.stage1.container).fadeIn();
		// clear boxes
		this.stage1.lnumberBox.value = "";
		this.stage3.selectClass.selectedIndex = -1;
		// hide buttons
		$(this.stage2.btnCheckOut).hide();
		$(this.stage2.btnCheckIn).hide();
		// show fromt
		this.showContainer(this.stage1.container);
		console.log("reset for next user");

	}
}

let labcheckin;
window.addEventListener('load',() =>{
	labcheckin = new LabCheckclient();
});