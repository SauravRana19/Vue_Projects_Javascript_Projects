let form = document.forms["my-form"];
form.addEventListener("submit", getValues);

function getValues(event){
	event.preventDefault();

	let data = {
		"name": this.name.value, 
		"bio": this.bio.value, 
		"fav color": this['fav-color'].value, 
		"gender": this.gender.value,
		"fav food": []
	}

	let favFoods = document.querySelectorAll(".fav-foods");
	for (let food of favFoods){
		if(food.checked == true){
			data['fav food'].push(food.value);
		}
	}

	let	out = `
		<p>Name: <span>${data.name}</span></p>
		<p>Bio: <span>${data.bio}</span></p>
		<p>Fav color: <span>${data['fav color']}</span></p>
		<p>Gender: <span>${data.gender}</span></p>
		<p>Fav food: <span>${data["fav food"]}</span></p>
	`;

	document.querySelector(".out code").innerHTML = out;
}


