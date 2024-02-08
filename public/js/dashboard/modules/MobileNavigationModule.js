import IconModule from "./IconModule.js";
import ApiClient from "../ApiClient.js";
import NotificationService from "../NotificationService.js";

const MobileNavigationModule = (function () {
    async function render() {
        const userData = await fetchUserData();
        const {userName, email} = userData;

        let navUserInfo = document.createElement("div");
        navUserInfo.innerHTML = `
            <div id="nav-user-info" class="flex-column flex-center">
                <div class="profile-photo flex flex-center">
                    <img src="/account/profile-picture" alt="Profile picture">
                </div>
                <div class="profile-info text-secondary text-shadow text-center">
                    <h1 class="profile-username">${userName || "Username"}</h1>
                    <p class="profile-email">${email || "email"}</p>
                </div>
            </div>`;

        navUserInfo = navUserInfo.firstElementChild;

        const pictureContainer = navUserInfo.querySelector(".profile-photo")
        const profilePicture = pictureContainer.querySelector("img");
        profilePicture.src = "/account/profile-picture#" + new Date().getTime();

        profilePicture.onerror = async () => {
            pictureContainer.innerHTML = await IconModule.render("account");
        };
        
        return navUserInfo;
    }

    async function updateState() {
        const nav = document.querySelector("#nav-user-info");
        nav.replaceWith(await render());
    }

    function fetchUserData() {
        return ApiClient.fetchData(`/account`)
            .then(result => {
                if (result.success) return result.data;
                NotificationService.notify(result.message || "Could not get user data", "error")
            })
    }

    return {
        render: render,
        updateState: updateState
    };
}());

export default MobileNavigationModule;
