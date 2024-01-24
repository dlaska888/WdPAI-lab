import AddLinkForm from "./js/modules/AddLinkForm.js";

document.body.appendChild(await AddLinkForm.render((await getCurrentTab()).url));

async function getCurrentTab() {
	let queryOptions = { active: true, lastFocusedWindow: true };
	let [tab] = await chrome.tabs.query(queryOptions);
	return tab;
}
