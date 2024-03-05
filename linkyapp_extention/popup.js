import AddLinkForm from "./js/modules/AddLinkForm.js";

document.body.appendChild(await AddLinkForm.render(await getCurrentTab()));

async function getCurrentTab() {
	const queryOptions = { active: true, lastFocusedWindow: true };
	const [tab] = await chrome.tabs.query(queryOptions);
	return tab;
}
