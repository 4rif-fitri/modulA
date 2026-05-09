let second = 
document.querySelector(".long")
let minute = 
document.querySelector(".midd")
let hour = 
document.querySelector(".short")

setInterval(() => {

	let now = new Date()

	let s = now.getSeconds() * 6
	let m = now.getMinutes() * 6
	let h = now.getHours() * 30

	second.style.transform = `rotate(${s}deg)`
	minute.style.transform = `rotate(${m}deg)`
	hour.style.transform = `rotate(${h}deg)`

}, 1000)