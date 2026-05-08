let tasks = document.querySelectorAll(".task")
let containers = document.querySelectorAll(".container")
let element

let drag = e => {
	element = e.currentTarget
	element.classList.add("isDrag")
	// console.log(element);
}
let updateData = () => {
	let tasks = document.querySelectorAll(".task")

	let array = []
	tasks.forEach(task => {
		array.push({
			task: task.querySelector("h4").textContent,
			type: task.dataset.type
		})
	})
	// console.log(array);

	localStorage.setItem("record", JSON.stringify(array))
	console.log(JSON.parse(localStorage.getItem("record")));
}
document.getElementById("btnToDo").addEventListener("click", e => {
	let text = document.querySelector("#textToDo").value.trim()
	if (text == "") {
		return
	}

	let div = document.createElement("div")
	div.classList.add("task")
	div.setAttribute("draggable",true)
	div.addEventListener("dragstart", e => drag(e))
	div.addEventListener("dragend", () => {
		div.classList.remove("isDrag")
	})
	div.dataset.type = "toDo"
	
	let h4 = document.createElement("h4")
	h4.textContent = text
	
	let button = document.createElement("button")
	button.textContent = "X"
	button.classList.add("deleteTask")
	button.addEventListener("click", e => {
		e.target.parentElement.remove()
		renderTask()
	})
	div.appendChild(h4)
	div.appendChild(button)
	document.querySelector("#containerToDo").appendChild(div)	
	
	div.addEventListener("dragstart" , e => {
		element = div 
	})
	renderTask()
	document.querySelector("#textToDo").value = ""
})
document.getElementById("btnInProgress").addEventListener("click", e => {
	
	let text = document.querySelector("#textInProgress").value.trim()
	if (text == "") {
		return
	}

	let div = document.createElement("div")
	div.classList.add("task")
	div.setAttribute("draggable", true)
	div.addEventListener("dragstart", e => drag(e))
	div.addEventListener("dragend", e => element.classList.remove("isDrag"))
	div.dataset.type = "inProgress"

	let h4 = document.createElement("h4")
	h4.textContent = text

	let button = document.createElement("button")
	button.textContent = "X"
	button.classList.add("deleteTask")
	button.addEventListener("click", e => e.target.parentElement.remove())

	div.appendChild(h4)
	div.appendChild(button)

	document.querySelector("#containerInProgress").appendChild(div)

	div.addEventListener("dragstart", e => {
		element = div
	})
	renderTask()
	document.querySelector("#textInProgress").value = ""
})
document.getElementById("btnDone").addEventListener("click", e => {

	let text = document.querySelector("#textDone").value.trim()
	if (text == "") {
		return
	}

	let div = document.createElement("div")
	div.classList.add("task")
	div.setAttribute("draggable", true)
	div.addEventListener("dragstart", e => drag(e))
	div.addEventListener("dragend", e => element.classList.remove("isDrag"))
	div.dataset.type = "done"

	let h4 = document.createElement("h4")
	h4.textContent = text

	let button = document.createElement("button")
	button.textContent = "X"
	button.classList.add("deleteTask")
	button.addEventListener("click", e => e.target.parentElement.remove())

	div.appendChild(h4)
	div.appendChild(button)

	document.querySelector("#containerDone").appendChild(div)

	div.addEventListener("dragstart", e => {
		element = div
	})
	renderTask()
	document.querySelector("#textDone").value = ""
})

containers.forEach(container => {
	container.addEventListener("dragover" , e =>{
		container.classList.add("higlight")
		e.preventDefault()
	})

	container.addEventListener("dragleave", e => {
		container.classList.remove("higlight")
	})

	container.addEventListener("drop", e => {
		container.classList.remove("higlight")
		console.log(container.dataset.type);
		
		element.dataset.type = container.dataset.type 
		container.appendChild(element)
		updateData()
		renderTask()
	})
})

let renderTask = () => {
	let counts = document.querySelectorAll("h3")
	let containers = (document.querySelectorAll("section"))
	
	containers.forEach((container,index) => {
		let count = container.querySelectorAll("div").length
		counts[index].textContent = `Task: ${count}`
	})
	updateData()
}

let loadData = () => {
	let datas = JSON.parse(localStorage.getItem("record")) || []

	console.log(datas);
	datas.forEach(data => {
		let div = document.createElement("div")
		div.classList.add("task")
		div.setAttribute("draggable", true)
		div.addEventListener("dragstart", e => drag(e))
		div.addEventListener("dragend", e => element.classList.remove("isDrag"))
		div.dataset.type = data.type

		let h4 = document.createElement("h4")
		h4.textContent = data.task

		let button = document.createElement("button")
		button.textContent = "X"
		button.classList.add("deleteTask")
		button.addEventListener("click", e => {
			e.target.parentElement.remove()
			renderTask()
		})
		div.appendChild(h4)
		div.appendChild(button)
		
		if (data.type == "toDo"){
			document.querySelector("#containerToDo").appendChild(div)
		} else if (data.type == "inProgress"){
			document.querySelector("#containerInProgress").appendChild(div)
		} else if (data.type == "done"){
			document.querySelector("#containerDone").appendChild(div)
		}

		div.addEventListener("dragstart", e => {
			element = div
		})
	})
	renderTask()
}
loadData()
