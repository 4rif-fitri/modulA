let select = document.getElementById("tipPercentage")
let customTipPercentage = document.getElementById("customTipPercentage")
let bil = document.getElementById("bil")
let numberOfPeople = document.getElementById("numberOfPeople")
let billPeople = 1
let tipPersent = 15
let amountBil = 0

numberOfPeople.addEventListener("input", e => {
	let value = parseInt(e.target.value.trim())
	if (value == 0 || isNaN(value) ){
		return
	}else{
		billPeople = parseInt(e.target.value)
		render()
	}
})

bil.addEventListener("input", e =>{
	if (e.target.value.trim() < 0){
		bil.focus()
		document.querySelector("span").style.display = "block"
	}else{
		amountBil = parseFloat(e.target.value.trim())
		document.querySelector("span").style.display = "none"
	}
	render()
})

select.addEventListener("change", e =>{
	console.log(e.target.value);
	if (e.target.value == "Custom"){
		customTipPercentage.style.display = "block"
	}else{
		tipPersent = parseFloat(e.target.value.trim())
		customTipPercentage.style.display = "none"
	}
	render()
})

customTipPercentage.addEventListener("input", e => {
	tipPersent = parseFloat(e.target.value.trim()) || 0
	console.log(tipPersent);
	render()
})

let render = () => {

	let total = ((amountBil) + (amountBil * (tipPersent / 100))).toFixed(2)
	document.getElementById("TipAmount").textContent = "RM" + (amountBil * (tipPersent / 100)).toFixed(2)
	document.getElementById("TotalBill").textContent = "RM" + amountBil.toFixed(2) + " + RM " + (amountBil * (tipPersent / 100)).toFixed(2) +
		" = RM" + total
	document.getElementById("AmountPerPerson").textContent = `RM ${total} / ${billPeople} = RM ${(total / billPeople).toFixed(2)}`

}