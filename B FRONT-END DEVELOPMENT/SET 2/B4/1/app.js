let datas = []
let table = document.querySelector("table")
let tbody = document.querySelector("tbody")
let index = 0
let filterCatagory = document.getElementById("filterCatagory")
let dateStart = document.getElementById("dateStart")
let dateEnd = document.getElementById("dateEnd")
let canvas = document.querySelector("canvas")
let canvasContainer = document.querySelector(".canvas")
let rec = canvasContainer.getBoundingClientRect()
let canvasWidth = rec.right - rec.left
let canvasHeight = rec.bottom - rec.top 
let ctx = canvas.getContext('2d')
let colors = ["red","blue","green","lightgreen","gray"]
let total
canvas.width = canvasWidth
canvas.height = canvasHeight
let facthData = () => {
	let d = JSON.parse(localStorage.getItem("recordData")) || []
	index = d.length > 0 ? Math.max(...d.map(dta => dta.index)) + 1 : 0 
	// console.log({ index });
	return d
}

window.addEventListener("resize", () => {
	rec = canvasContainer.getBoundingClientRect()
	canvasWidth = rec.right - rec.left
	canvasHeight = rec.bottom - rec.top
	canvas.width = canvasWidth
	canvas.height = canvasHeight	
	renderCanvas()
})
let startX = 50
let startY = canvasHeight - 50

let _filterCatagory = () => {
	let dt = facthData()
	let Food = dt.filter(d => d.catagory == "Food")
	let Shopping = dt.filter(d => d.catagory == "Shopping")
	let Transport = dt.filter(d => d.catagory == "Transport")
	let Bills = dt.filter(d => d.catagory == "Bills")
	let Other = dt.filter(d => d.catagory == "Other")
	
	return [
		{
			catagory: "Food",
			amount: Food.reduce((acc, curr) => acc + Number(curr.amount),0)
		}, {
			catagory: "Shopping",
			amount: Shopping.reduce((acc, curr) => acc + Number(curr.amount), 0)
		},{
			catagory: "Transport",
			amount: Transport.reduce((acc, curr) => acc + Number(curr.amount), 0)	
		},{
			catagory: "Bills",
			amount: Bills.reduce((acc, curr) => acc + Number(curr.amount), 0)
		}, {
			catagory: "Other",
			amount: Other.reduce((acc, curr) => acc + Number(curr.amount), 0)
		}
	]

}
let renderCanvas = (show = -1) => {
	dt = _filterCatagory()
	total = dt.reduce((acc, d) => acc + Number(d.amount), 0)
	// console.log({total});
	document.getElementById("ttl").textContent = total
	
	ctx.clearRect(0, 0, canvas.width, canvas.height)
	let gap = 20
	let barWidth = 0.12 * canvas.width 
	let max = Math.max(...dt.map(d => d.amount))
	
	// console.log({ max });
	
	ctx.beginPath()
	ctx.moveTo(startX, startY)
	ctx.lineTo(startX + canvasWidth - 100, startY)
	ctx.lineTo(startX, startY)
	ctx.lineTo(startX, startY - canvasHeight + 100)
	ctx.lineTo(startX, startY)

	dt.forEach((d,idx) => {
		// let barHeight = (Number(d.amount) / max) * canvasHeight - 100 
		let barHeight = (Number(d.amount) / max) * (canvasHeight - 100)
		let x = startX + gap + idx * (barWidth + gap)
		let y = startY - barHeight
		
		ctx.font = "bold 1rem arial"
		ctx.fillStyle = colors[idx]
		if (idx === show || show === -1){
			ctx.fillRect(x,y,barWidth,barHeight)
			ctx.fillText(`RM ${d.amount}`, x, y - 10)
		}
		
		ctx.fillText(d.catagory, x, startY + 20)
	})

	ctx.fillStyle = "#000"
	ctx.lineWidth = 3
	ctx.stroke()

}
renderCanvas()

filterCatagory.addEventListener("change", e => {
	// console.log(e.target.value);
	let i = -1
	if (e.target.value == "All Catagory") i = -1
	else if (e.target.value == "Food") i = 0
	else if (e.target.value == "Shopping") i = 1
	else if (e.target.value == "Transport") i = 2
	else if (e.target.value == "Bills") i = 3
	else if (e.target.value == "Other") i = 4
	let value = e.target.value

	let filtered = value === "All Catagory" ? facthData() : facthData().filter(
				d => d.catagory === value)
	// console.log(d);
	renderTable(filtered)
	renderCanvas(i)
})

let breakdown = () =>{
	let dts = facthData()
	console.log(dts);
	
}	
breakdown()
dateStart.addEventListener("change", e => {
	
	renderTable()
})

let getMonthlySummary = () => {
	let dt = facthData()
	let result = {}

	dt.forEach(d => {
		let date = new Date(d.date)
		let key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}`

		if (!result[key]) {
			result[key] = {
				total: 0,
				Food: 0,
				Shopping: 0,
				Transport: 0,
				Bills: 0,
				Other: 0
			}
		}

		let amount = Number(d.amount)
		let cat = d.catagory?.trim()

		result[key].total += amount

		if (!result[key][cat]) result[key][cat] = 0
		result[key][cat] += amount
	})

	let container = document.querySelector(".mountlu-summery")
	container.innerHTML = ""

	for (const key in result) {
		let r = result[key]

		container.innerHTML += `
			<div class="mounly">
				<h3>${key}</h3>
				<h4>Total: RM ${r.total}</h4>
				<p>Food: ${r.Food}</p>
				<p>Shopping: ${r.Shopping}</p>
				<p>Transport: ${r.Transport}</p>
				<p>Bills: ${r.Bills}</p>
				<p>Other: ${r.Other}</p>
			</div>
		`
	}

	return result
}
getMonthlySummary()
dateEnd.addEventListener("change", e => {
	
	renderTable()
})

let editData = (e, idx, element) => {
	console.log(idx);
	
}

let deleteData = (e,idx,element) => {
	let d = facthData()
	d = d.filter((c) => c.index != idx)
	localStorage.setItem("recordData", JSON.stringify(d))
	renderTable()
	renderCanvas()
}
let renderTable = (datas = facthData()) => {
	getMonthlySummary()
	tbody.innerHTML = ""

	datas.forEach((data,index) => {
		// console.log(data);
		
		tbody.innerHTML += `
		<tr>
			<td>
				<h4>${data.description}</h4>
			</td>
			<td>
				<h4>${data.amount}</h4>
			</td>
			<td>
				<h4>${data.catagory}</h4>
			</td>
			<td>
				<h4>${data.date}</h4>
			</td>
			<td>
				<button onClick="editData(event,${data.index},this)">Edit</button>
				<button onClick="deleteData(event,${data.index},this)">Delete</button>
			</td>
		</tr>
		`
	})
	renderCanvas()
}
renderTable()

document.addEventListener("submit", e => {
	e.preventDefault()

	datas = facthData()

	let description = document.getElementById("description").value.trim()
	let amount = document.getElementById("amount").value.trim()
	let catagory = document.getElementById("catagory").value.trim()
	let date = document.getElementById("date").value.trim()

	// console.log(description);
	// console.log(amount);
	// console.log(catagory);
	// console.log(date);
	
	let dt = {
		index: index++,
		description: description,
		amount: Number(amount),
		catagory:catagory,
		date:date
	}
	datas.unshift(dt)
	localStorage.setItem("recordData", JSON.stringify(datas))
	console.log(index);
	
	renderTable()
})