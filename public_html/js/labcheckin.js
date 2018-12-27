class Labcheckin {
	constructor(){
		console.log("Lab Check");
		// global from AdminController
		this.config = config;
		this.canShowErrors = false;
		this.canShowSuccess = false;
		// Can we show success alerts?
		if (document.querySelector('.success') !== null) {
			this.canShowSuccess = true;
			this.successBox = document.getElementsByClassName('success')[0];
		}

		// Can we show error alerts?
		// Check to see if you have a div that can show errors
		if (document.querySelector('.error') !== null) {
			this.canShowErrors = true;
			this.errorBox = document.getElementsByClassName('error')[0];
		}
		// Check if there is an add class button
		if (document.querySelector('.class-add-submit') !== null) {
			console.log("bound .class-add-submit");
			document.getElementsByClassName('class-add-submit')[0].addEventListener('click',(event)=>{
					event.preventDefault();
					labcheckin.classAddSubmit(this);
			});
			document.getElementsByClassName('class-add-className')[0].addEventListener('keypress',(event)=>{
					labcheckin.hideAlerts();
			});
		}
		// Check for edit users table
		this.editUsersBox = document.getElementById("editUsers");
		if(this.editUsersBox != null){
			this.buildUsersBox();
			console.log("buliding user box");
		}
		

	}
	editBindbuttons(){
		// check for buttons
		console.log("btns");
		if(document.querySelector(".editUsers-btnDelete") !== null){
			
			let buttons = document.getElementsByClassName('editUsers-btnDelete');
			for(let i=0; i< buttons.length;i++){
			console.log("btn");	
				buttons[i].addEventListener('click', (event) => {
					this.editUsersDelete(event); });
			}	
		}

		if(document.querySelector(".editUsers-btnChangePass") !== null){
			let buttons = document.getElementsByClassName('editUsers-btnChangePass');			
			for(let i=0; i< buttons.length;i++){
				buttons[i].addEventListener('click', (event) => {
					console.log(event);
					this.editUsersChangePass(event); });
			}	

		}
	}
	editUsersDelete(event){
		if (confirm("Are you sure you want to delete user?")) {
			console.log("Delete" + event.srcElement.dataset.id);
			let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "admin/editusers";
			fetch(url,{
					method: "POST",
					headers: {'Content-Type':'application/x-www-form-urlencoded'},
					body: "action=delete" + "&id=" + event.srcElement.dataset.id
				}).then(res => res.json())
				.then(response => {
					this.buildUsersBox();
					alert(response.response);

				}
				);
		}
	}
	editUsersChangePass(event){
		let newpass = window.prompt("Enter the new password");
		console.log("change password" + event.srcElement.dataset.id + " to: " + newpass);
		if(newpass.length > 0){
			let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "admin/editusers";
			fetch(url,{
					method: "POST",
					headers: {'Content-Type':'application/x-www-form-urlencoded'},
					body: "action=changePass&password=" + newpass + "&id=" + event.srcElement.dataset.id
				}).then(res => res.json())
				.then(response => {
					alert(response.response);
				}
				);
		}
	}
	buildUsersBox(){
		let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "admin/getusers";
		fetch(url,{
				method: "POST",
				headers: {'Content-Type':'application/x-www-form-urlencoded'}
			}).then(res => res.json())
			.then(response => {
				let output = "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Options</th></tr>";
				for(let i=0; i<response.data.length;i++){
					output += "<tr><td>" + response.data[i]["ID"] + "</td>";
					output += "<td>" + response.data[i]["Name"] + "</td>";
					output += "<td>" + response.data[i]["EmailAddress"] + "</td>";
					if(i > 0){
						output += "<td><button data-id='"+ response.data[i]["ID"] +"' class='editUsers-btnDelete'>Delete</button>&nbsp;";
						output += "<button data-id='"+ response.data[i]["ID"] +"' class='editUsers-btnChangePass'>Change Pass</button></td></tr>";
					}
					else{
						output += "<td><button data-id='"+ response.data[i]["ID"] +"' class='editUsers-btnChangePass'>Change Pass</button></td></tr>";
					}
				}
				output += "</table";
				this.editUsersBox.innerHTML = output;
				this.editBindbuttons();
			}
			);
	}
	classAddSubmit(e){
		let className = document.getElementsByClassName('class-add-className')[0];
		if(className.value.length == 0){
			labcheckin.showError("Class Name cannot be blank");
		}
		else{
			let formVars = {
				"save" : true,
				"name" : className.value
			};
			console.log("Form" + JSON.stringify(formVars));
			let url = location.protocol + '//' + location.hostname + "/" + this.config.base + "admin/editcourses";
			console.log(url);
			fetch(url,{
				method: "POST",
				headers: {'Content-Type':'application/x-www-form-urlencoded'},
				body:  "save=true&name=" +className.value
			}).then(res => res.json())
			.then(response => {
				if(response.success == "true"){
					labcheckin.showSuccess(response.response);
					console.log("true");
				}
				else{
					labcheckin.showError(response.response);
					console.log("false");
				}
				console.log('Success:', JSON.stringify(response))
			});
		}
	}
	showError(error){
		if(this.canShowErrors){
			this.errorBox.innerHTML = error;
			$(this.errorBox).fadeIn();
		}
	}
	hideError(){
		if(this.canShowErrors){
			// Jquery
			 $(this.errorBox).fadeOut();
		}
	}
	showSuccess(success){
		if(this.canShowSuccess){
			this.successBox.innerHTML = success;
			
			$(this.successBox).fadeIn();
		}
	}
	hideSuccess(){
		if(this.canShowSuccess){
			// Jquery
			 $(this.successBox).fadeOut();
			//this.errorBox.classList.remove("d-block");
			//this.errorBox.classList.add("d-none");
		}
	}
	hideAlerts(){
		this.hideError();
		this.hideSuccess();
	}
}
let labcheckin;
window.addEventListener('load',() =>{
	labcheckin = new Labcheckin();
});
//window.addEventListener('load',() => {thewall = new TheWall()});