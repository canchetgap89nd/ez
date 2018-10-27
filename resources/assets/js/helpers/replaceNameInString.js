export function addName (name, str) {
	let toName = name.toUpperCase()
	var res = str.replace("[FULL_NAME]", toName)
	return res
}
