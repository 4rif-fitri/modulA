let cards = document.querySelectorAll(".card input")
let card1 
let innerCard1 
let card2 
let innerCard2
let count = 0
let isPick = false

let move = 0
let coutPair = 0
let minit = 0
let saat = 0
let idMasa

let timer = () => {
	return setInterval(()=>{
		
		saat++
		if(saat >= 60){
			saat = 0
			minit++
		}
		renderStatus()
	},1000)
}

idMasa = timer()


let delay = time => new Promise(resolve => setTimeout((resolve), time) )
let semak = async () => {
	if (card1.style.backgroundImage == card2.style.backgroundImage){
		// console.log("betul");
		innerCard1.classList.add("betul")
		innerCard2.classList.add("betul")
		coutPair++
	}else{
		await delay(1000)
		// console.log("salah");
		innerCard1.classList.remove("flip")
		innerCard2.classList.remove("flip")
	}
	renderStatus()
	isPick = false
}
cards.forEach((card,idx) => {
	card.parentElement.dataset.id = idx 
})
cards.forEach(card => {
	card.addEventListener("change", e => {
		// if (isPick || (card1.dataset.id == card2.dataset.id )) return

		if (isPick) return

		let back =
			card.parentElement.querySelector(".back")

		let inner =
			card.parentElement.querySelector(".innerCard")

		inner.classList.add("flip")

		renderStatus()
		if (count == 0){
			count++
			card1 = back
			innerCard1 = inner
			// card1 = card.parentElement.querySelector('.back')
			// innerCard1 = card.parentElement.querySelector('.innerCard')
			// innerCard1.classList.add("flip")
		} else if (count == 1){
			if (innerCard1 === inner) {
				return
			}
			count = 0
			// card2 = card.parentElement.querySelector('.back')
			// innerCard2 = card.parentElement.querySelector('.innerCard')
			// innerCard2.classList.add("flip")
			card2 = back
			innerCard2 = inner

			isPick = true	
			renderStatus()	
			move++
			semak()
		}
	
	})
})

let renderStatus = () => {
	document.getElementById("countMove").textContent = move
	document.getElementById("countTime").textContent = `${String(minit).padStart(2, "0")}:${String(saat).padStart(2, "0") }`
	document.getElementById("countPair").textContent = coutPair
}

document.getElementById("reset").addEventListener("click", () => {

	let container = document.getElementById("cardContainer")

	let cards = Array.from(document.querySelectorAll(".card"))

	// shuffle
	for (let i = cards.length - 1; i > 0; i--) {
		let j = Math.floor(Math.random() * (i + 1))
			;[cards[i], cards[j]] = [cards[j], cards[i]]
	}

	// clear container
	container.innerHTML = ""

	// reset state
	move = 0
	coutPair = 0
	minit = 0
	saat = 0
	isPick = false
	count = 0

	// rebuild DOM
	cards.forEach(card => {
		card.querySelector(".innerCard").classList.remove("flip", "betul")
		card.querySelector("input").checked = false
		container.appendChild(card)
	})

	renderStatus()

	clearInterval(idMasa)
	timer()
})