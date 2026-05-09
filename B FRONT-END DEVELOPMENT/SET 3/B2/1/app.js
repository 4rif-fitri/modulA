let select = document.getElementById("catagory")
let products = document.querySelectorAll(".card")
let btnReset = document.getElementById("btnReset") 
let filterText = document.getElementById("filterText")
let selectFilter = "All"
let textFilter = ""
let timer
let count = 0
// console.log(products);

select.addEventListener("change", e => {
	selectFilter = e.currentTarget.value
	// console.log(selectFilter);	
	render()
})

let render = () => {
	// let dataFilter
	// let dataNeed
	// if (selectFilter == "All"){
	// 	dataNeed = products
	// 	dataNeed.forEach(card => card.style.animation = "fadeIn 0.5s forwards")
	// }else{
	// 	dataFilter = [...products].filter(product => product.dataset.type != selectFilter)
	// 	dataNeed = [...products].filter(product => product.dataset.type == selectFilter)
	// 	// console.log(dataFilter);
	// 	dataNeed.forEach(card => card.style.animation = "fadeIn 0.5s forwards")
	// 	dataFilter.forEach(card => card.style.animation = "fadeOut 0.5s forwards")
	// }
	// // console.log(dataNeed);
	

	// dataNeed.forEach(data => {
	// 	console.log(textFilter);
		
	// 	console.log(data.querySelector("h3").textContent.toLowerCase().includes(textFilter.toLowerCase()))
		
	// 	if (!data.querySelector("h3")
	// 		.textContent
	// 		.toLowerCase()
	// 		.includes(textFilter.toLowerCase())
	// 	){
	// 		data.style.animation = "fadeOut 0.5s forwards"
	// 	}else{
	// 		console.log(data);
	// 		data.style.animation = "fadeIn 0.5s forwards"
	// 	}
	// })
	count = 0
	let a = document.querySelectorAll(".ppp") || [] 
	a.forEach(e => e.remove())

	products.forEach(product => {
		const productType = product.dataset.type
		const productName = product.querySelector("h3").textContent.toLowerCase()

		// Check if product matches Category
		const matchesCategory = (selectFilter === "All" || productType === selectFilter)

		// Check if product matches Search Text
		const matchesText = productName.includes(textFilter.toLowerCase())

		// Apply animation based on both conditions
		if (matchesCategory && matchesText) {
			product.style.display = "block" // Ensure it takes up space
			product.style.animation = "fadeIn 0.5s forwards"
			count++
		} else {
			product.style.animation = "fadeOut 0.5s forwards"
			// Optional: Use a timeout to set display: none after fadeOut if you want the grid to collapse
		}
	})
	document.getElementById("status").textContent = `Showing ${count} of 12 products`

	if (count == 0){
		for (let index = 0; index < 3; index++) {
			let div = document.createElement("h4")
			div.classList.add("ppp")
			if (index == 1){
				div.textContent = "No products found"
			}
			document.querySelector("main").appendChild(div)
			
		}
	}
}
btnReset.addEventListener("click" , e => {
	selectFilter = "All"
	textFilter = ""

	select.value = "All"
	filterText.value = ""

	render()
})

filterText.addEventListener("input", e =>{
	clearTimeout(timer)

	timer = setTimeout(() => {
		textFilter = e.target.value
		render()
	}, 300)
})